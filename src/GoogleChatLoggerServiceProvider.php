<?php

namespace Mepsd\LaravelGoogleChatLogger;

use Illuminate\Support\ServiceProvider;

class GoogleChatLoggerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/google-chat-logger.php' => config_path('google-chat-logger.php'),
        ], 'google-chat-logger-config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/google-chat-logger.php', 'google-chat-logger'
        );
    }

    public function provides()
    {
        return [];
    }
}