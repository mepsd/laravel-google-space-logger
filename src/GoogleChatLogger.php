<?php

namespace Mepsd\LaravelGoogleChatLogger;

use Monolog\Logger;
use Monolog\Level;

class GoogleChatLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('google_chat');

        $level = $this->parseLevel($config['level'] ?? 'debug');

        // Extract handler configuration
        $handlerConfig = [
            'retries' => $config['retries'] ?? 2,
            'timeout' => $config['timeout'] ?? 5,
            'thread_key' => $config['thread_key'] ?? null,
            'includeStackTrace' => $config['includeStackTrace'] ?? true,
            'includeSqlQueries' => $config['includeSqlQueries'] ?? false,
            'includeRequestData' => $config['includeRequestData'] ?? true,
            'maxContextDepth' => $config['maxContextDepth'] ?? 3,
            'backoff' => $config['backoff'] ?? false,
        ];

        $logger->pushHandler(new GoogleChatHandler(
            $config['url'],
            $handlerConfig,  // Pass the extracted config
            $level,
            $config['bubble'] ?? true
        ));

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
