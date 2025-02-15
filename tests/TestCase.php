<?php

namespace Mepsd\LaravelGoogleChatLogger\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Mepsd\LaravelGoogleChatLogger\GoogleChatLoggerServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure config is loaded
        $this->app['config']->set('google-chat-logger', [
            'url' => 'https://chat.googleapis.com/v1/spaces/test/messages',
            'level' => 'debug',
            'thread_key' => null,
            'includeStackTrace' => true,
            'includeSqlQueries' => false,
            'includeRequestData' => true,
            'maxContextDepth' => 3,
            'timeout' => 5,
            'retries' => 2,
            'backoff' => false,
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            GoogleChatLoggerServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Disable database
        $app['config']->set('database.default', null);
    }
}
