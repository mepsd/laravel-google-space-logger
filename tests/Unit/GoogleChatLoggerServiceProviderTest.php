<?php

namespace Mepsd\LaravelGoogleChatLogger\Tests\Unit;

use Mepsd\LaravelGoogleChatLogger\Tests\TestCase;

class GoogleChatLoggerServiceProviderTest extends TestCase
{
    /** @test */
    public function it_registers_config_file()
    {
        // Instead of checking the published file, we'll check if config is loaded
        $config = config('google-chat-logger');
        $this->assertIsArray($config);
        $this->assertArrayHasKey('url', $config);
    }

    /** @test */
    public function it_has_required_config_values()
    {
        $config = config('google-chat-logger');
        $this->assertArrayHasKey('url', $config);
        $this->assertArrayHasKey('level', $config);
    }
}