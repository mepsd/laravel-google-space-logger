<?php

namespace Mepsd\LaravelGoogleChatLogger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Monolog\Level;
use Illuminate\Support\Facades\Http;

class GoogleChatHandler extends AbstractProcessingHandler
{
    protected string $webhookUrl;
    protected array $config;

    public function __construct(
        string $webhookUrl,
        array $config = [],
        Level|int $level = Level::Debug,
        bool $bubble = true
    ) {
        $this->webhookUrl = $webhookUrl;
        $this->config = array_merge([
            'thread_key' => null,
            'includeStackTrace' => true,
            'includeSqlQueries' => false,
            'includeRequestData' => true,
            'maxContextDepth' => 3,
            'timeout' => 5,
            'retries' => 2,
            'backoff' => false,
        ], $config);

        if (is_int($level)) {
            $level = Level::fromValue($level);
        }

        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        $message = $this->formatMessage($record);
        $payload = ['text' => $message];

        if ($this->config['thread_key']) {
            $payload['thread'] = ['name' => $this->config['thread_key']];
        }

        $this->sendWithRetry($payload);
    }

    protected function sendWithRetry(array $payload, int $attempt = 1): void
    {
        try {
            $response = Http::timeout($this->config['timeout'])
                ->post($this->webhookUrl, $payload);
                echo $response->status();

            if (!$response->successful()) {
                if ($attempt < $this->config['retries']) {
                    // Apply backoff if enabled
                    if ($this->config['backoff']) {
                        $sleepTime = min(pow(2, $attempt - 1), 10);
                        sleep($sleepTime);
                    }

                    // Make next attempt
                    $this->sendWithRetry($payload, $attempt + 1);
                }
            }
        } catch (\Exception $e) {
            if ($attempt < $this->config['retries']) {
                // Apply backoff if enabled
                if ($this->config['backoff']) {
                    $sleepTime = min(pow(2, $attempt - 1), 10);
                    sleep($sleepTime);
                }

                // Make next attempt
                $this->sendWithRetry($payload, $attempt + 1);
            }
        }
    }

    protected function formatMessage(LogRecord $record): string
    {
        $emoji = $this->getEmoji($record->level->name);
        $environment = app()->environment();

        $message = sprintf(
            "*[%s]* %s *%s*\n```\n%s",
            $environment,
            $emoji,
            $record->level->name,
            $record->message
        );

        if (!empty($record->context)) {
            $context = $this->processContext($record->context);
            $message .= "\n\nContext:\n";
            $message .= json_encode($context, JSON_PRETTY_PRINT);
        }

        $message .= "\n```";

        return $message;
    }

    protected function getEmoji(string $level): string
    {
        return match (strtolower($level)) {
            'emergency' => '🚨',
            'alert'     => '⚠️',
            'critical'  => '🔥',
            'error'     => '❌',
            'warning'   => '⚠️',
            'notice'    => '📝',
            'info'      => 'ℹ️',
            'debug'     => '🐛',
            default     => '📋'
        };
    }

    protected function processContext(array $context, int $depth = 0): array
    {
        if ($depth >= $this->config['maxContextDepth']) {
            return ['[Max Depth Reached]'];
        }

        $processed = [];
        foreach ($context as $key => $value) {
            if (is_array($value)) {
                $processed[$key] = $this->processContext($value, $depth + 1);
            } elseif (is_object($value)) {
                if ($value instanceof \Throwable) {
                    $processed[$key] = [
                        'class' => get_class($value),
                        'message' => $value->getMessage(),
                        'code' => $value->getCode(),
                        'file' => $value->getFile(),
                        'line' => $value->getLine()
                    ];
                } else {
                    $processed[$key] = method_exists($value, 'toArray')
                        ? $value->toArray()
                        : get_object_vars($value);
                }
            } else {
                $processed[$key] = $value;
            }
        }

        return $processed;
    }
}
