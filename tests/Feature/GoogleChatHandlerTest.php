<?php

namespace Mepsd\LaravelGoogleChatLogger\Tests\Feature;

use Mepsd\LaravelGoogleChatLogger\Tests\TestCase;
use Mepsd\LaravelGoogleChatLogger\GoogleChatHandler;
use Monolog\LogRecord;
use Monolog\Level;
use Illuminate\Support\Facades\Http;

class GoogleChatHandlerTest extends TestCase
{
    private string $webhookUrl = 'https://chat.googleapis.com/v1/spaces/test/messages';
    private GoogleChatHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = new GoogleChatHandler($this->webhookUrl);
    }

     /** @test */
     public function it_handles_exceptions_in_context()
     {
         // Start fresh with HTTP fakes
         Http::fake()->assertNothingSent();
 
         // Create new fake for this specific test
         Http::fake([
             '*' => Http::response(['status' => 'success'], 200)
         ]);
 
         // Create test exception
         $testException = new \Exception('Test error message');
         
         // Create log record
         $record = new LogRecord(
             datetime: new \DateTimeImmutable(),
             channel: 'test',
             level: Level::Error,
             message: 'Error occurred',
             context: [
                 'exception' => $testException
             ]
         );
 
         // Handle the record
         $this->handler->handle($record);
 
         // Assert HTTP request was made
         Http::assertSent(function ($request) {
             // For debugging
             print_r([
                 'Request URL' => $request->url(),
                 'Request Body' => json_decode($request->body(), true)
             ]);
 
             // Get request body
             $body = json_decode($request->body(), true);
 
             // Basic check if request was made to Google Chat API
             $urlCheck = str_contains($request->url(), 'chat.googleapis.com');
             
             // Check if message text contains the exception message
             $messageCheck = str_contains($body['text'] ?? '', 'Test error message');
             
             // Return true only if both checks pass
             return $urlCheck && $messageCheck;
         });
     }


    /** @test */
    public function it_sends_basic_log_message()
    {
        Http::fake([
            'https://chat.googleapis.com/*' => Http::response(['status' => 'success'], 200)
        ]);

        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'Test message',
            context: []
        );

        $this->handler->handle($record);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'chat.googleapis.com') &&
                   str_contains($request->body(), 'Test message');
        });
    }
}