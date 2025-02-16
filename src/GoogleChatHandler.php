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
        Level|int $level = Level::Debug
    ) {
        $this->webhookUrl = $webhookUrl;
        $this->config = array_merge([
            'timeout' => 5,
            'retries' => 2,
        ], $config);

        if (is_int($level)) {
            $level = Level::fromValue($level);
        }

        parent::__construct($level);
    }

    protected function write(LogRecord $record): void
    {
        // Prepare the message
        $text = $this->formatMessage($record);

        // Prepare payload
        $payload = ['text' => $text];

        // Add thread if provided
        if (!empty($record->context['thread_key'])) {
            $payload['thread'] = [
                'threadKey' => $record->context['thread_key']
            ];
        }

        // Send the request
        try {
            $this->sendWithRetry($payload);
        } catch (\Exception $e) {
            error_log("Failed to send to Google Chat: " . $e->getMessage());
        }
    }

    protected function formatMessage(LogRecord $record): string
    {
        $emoji = $this->getEmoji($record->level->name);
        $environment = app()->environment();

        // Start with basic message format
        $message = sprintf(
            "*[%s]* %s *%s*\n```\n%s",
            $environment,
            $emoji,
            $record->level->name,
            $record->message
        );

        // Format exception if present
        if (
            isset($record->context['exception']) &&
            $record->context['exception'] instanceof \Throwable
        ) {
            $exception = $record->context['exception'];
            $message .= "\n\nException Details:";
            $message .= "\nMessage: " . $exception->getMessage();
            $message .= "\nFile: " . $exception->getFile() . ":" . $exception->getLine();
        }

        // Add other context (excluding exception and thread_key)
        $context = $record->context;
        unset($context['exception'], $context['thread_key']);

        if (!empty($context)) {
            $message .= "\n\nContext:\n" . json_encode($context, JSON_PRETTY_PRINT);
        }

        $message .= "\n```";

        return $message;
    }

    protected function sendWithRetry(array $payload, int $attempt = 1)
    {
        $url = $this->webhookUrl;
        if (!empty($payload['thread'])) {
            if (str_contains($url, '?')) {
                $url .= '&messageReplyOption=REPLY_MESSAGE_FALLBACK_TO_NEW_THREAD';
            } else {
                $url .= '?messageReplyOption=REPLY_MESSAGE_FALLBACK_TO_NEW_THREAD';
            }
        }

        try {
            $response = Http::timeout($this->config['timeout'])
                ->post($url, $payload);

            if (!$response->successful() && $attempt < $this->config['retries']) {
                usleep(100000); // 100ms delay
                return $this->sendWithRetry($payload, $attempt + 1);
            }

            return $response;
        } catch (\Exception $e) {
            if ($attempt < $this->config['retries']) {
                usleep(100000); // 100ms delay
                return $this->sendWithRetry($payload, $attempt + 1);
            }
            throw $e;
        }
    }

    protected function getEmoji(string $level): string
    {
        return match (strtolower($level)) {
            'emergency' => '🚨',
            'alert'     => '⚠️',
            'critical'  => '🔥',
            'error'     => '❌',
            'warning'  => '⚠️',
            'notice'   => '📝',
            'info'     => 'ℹ️',
            'debug'    => '🐛',
            default    => '📋'
        };
    }
}
