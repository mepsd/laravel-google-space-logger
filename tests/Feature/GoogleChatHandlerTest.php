<?php

namespace Mepsd\LaravelGoogleChatLogger\Tests\Feature;

use Mepsd\LaravelGoogleChatLogger\Tests\TestCase;
use Mepsd\LaravelGoogleChatLogger\GoogleChatHandler;
use Monolog\Logger;
use Monolog\LogRecord;
use Monolog\Level;
use Illuminate\Support\Facades\Http;

class GoogleChatHandlerTest extends TestCase
{
    protected GoogleChatHandler $handler;
    protected Logger $logger;
    protected string $webhookUrl = 'https://chat.googleapis.com/v1/spaces/test/messages';

    protected function setUp(): void
    {
        parent::setUp();

        // Clear any previous fake
        Http::fake()->assertNothingSent();

        $this->handler = new GoogleChatHandler($this->webhookUrl);
        $this->logger = new Logger('test');
        $this->logger->pushHandler($this->handler);
    }

    /** @test */
    public function it_sends_basic_log_message()
    {
        // Clear any existing fakes
        Http::fake()->assertNothingSent();

        // Create new handler
        $handler = new GoogleChatHandler($this->webhookUrl);

        // Create log record with proper Level object
        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'Test message',
            context: []
        );

        // Write the record
        $handler->handle($record);

        // Assert
        Http::assertSent(function ($request) {
            return $request->url() === $this->webhookUrl
                && str_contains($request['text'], 'Test message');
        });
    }

    /** @test */
    public function it_includes_context_data()
    {
        Http::fake()->assertNothingSent();

        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'User action',
            context: ['user_id' => 123, 'action' => 'login']
        );

        $this->handler->handle($record);

        Http::assertSent(function ($request) {
            return str_contains($request['text'], 'user_id')
                && str_contains($request['text'], '123');
        });
    }

    /** @test */
    public function it_handles_exceptions_in_context()
    {
        Http::fake()->assertNothingSent();

        $exception = new \Exception('Test error');
        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Error,
            message: 'Error occurred',
            context: ['exception' => $exception]
        );

        $this->handler->handle($record);

        Http::assertSent(function ($request) {
            return str_contains($request['text'], 'Test error');
        });
    }

    /** @test */
    public function it_formats_different_log_levels()
    {
        Http::fake()->assertNothingSent();

        $levels = [
            Level::Debug,
            Level::Info,
            Level::Notice,
            Level::Warning,
            Level::Error,
            Level::Critical,
            Level::Alert,
            Level::Emergency
        ];

        foreach ($levels as $level) {
            $record = new LogRecord(
                datetime: new \DateTimeImmutable(),
                channel: 'test',
                level: $level,
                message: 'Test message',
                context: []
            );

            $this->handler->handle($record);
        }

        Http::assertSentCount(count($levels));
    }
}
