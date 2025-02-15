<?php

namespace Mepsd\LaravelGoogleChatLogger;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Illuminate\Support\Facades\Http;
use Monolog\LogRecord;

class GoogleChatHandler extends AbstractProcessingHandler
{
    protected string $webhookUrl;

    public function __construct(
        string $webhookUrl,
        $level = Logger::DEBUG,
        bool $bubble = true
    ) {
        $this->webhookUrl = $webhookUrl;
        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        $message = $this->formatMessage($record);
        
        try {
            Http::post($this->webhookUrl, [
                'text' => $message
            ]);
        } catch (\Exception $e) {
            // Silent fail to prevent application disruption
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
            $message .= "\n\nContext:\n";
            $message .= json_encode($record->context, JSON_PRETTY_PRINT);
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
}