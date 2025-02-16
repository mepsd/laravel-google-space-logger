<?php

namespace Mepsd\LaravelGoogleChatLogger;

use Monolog\Logger;
use Monolog\Level;

class GoogleChatLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('google_chat');

        // Parse level
        $level = $this->parseLevel($config['level'] ?? 'debug');

        // Build handler config
        $handlerConfig = [
            'timeout' => $config['timeout'] ?? 5,
            'retries' => $config['retries'] ?? 2,
            'thread_key' => $config['thread_key'] ?? null, // Default thread key if set
            'thread_types' => $config['thread_types'] ?? [
                'user' => 'user-{id}',
                'order' => 'order-{id}',
                'deployment' => 'deployment-{date}',
                'error' => 'error-{hash}',
            ],
        ];

        // Create and push handler
        $handler = new GoogleChatHandler(
            $config['url'],
            $handlerConfig,
            $level
        );

        $logger->pushHandler($handler);

        return $logger;
    }

    protected function parseLevel(string|int $level): Level
    {
        if ($level instanceof Level) {
            return $level;
        }

        return match (strtolower((string)$level)) {
            'debug' => Level::Debug,
            'info' => Level::Info,
            'notice' => Level::Notice,
            'warning' => Level::Warning,
            'error' => Level::Error,
            'critical' => Level::Critical,
            'alert' => Level::Alert,
            'emergency' => Level::Emergency,
            default => Level::Debug,
        };
    }
}
