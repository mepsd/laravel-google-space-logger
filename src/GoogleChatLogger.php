<?php

namespace Mepsd\LaravelGoogleChatLogger;

use Monolog\Logger;

class GoogleChatLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('google_chat');
        $logger->pushHandler(new GoogleChatHandler(
            $config['url'],
            $config['level'] ?? Logger::DEBUG,
            $config['bubble'] ?? true
        ));
        
        return $logger;
    }
}