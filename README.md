# Laravel Google Chat Logger 🚀

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mepsd/laravel-google-chat-logger.svg?style=flat-square)](https://packagist.org/packages/mepsd/laravel-google-chat-logger)
[![Total Downloads](https://img.shields.io/packagist/dt/mepsd/laravel-google-chat-logger.svg?style=flat-square)](https://packagist.org/packages/mepsd/laravel-google-chat-logger)
[![License](https://img.shields.io/packagist/l/mepsd/laravel-google-chat-logger.svg?style=flat-square)](https://packagist.org/packages/mepsd/laravel-google-chat-logger)

Send your Laravel application logs directly to Google Chat with rich formatting, emoji support, threading, and automatic retries.

## Features 🌟

- 🎯 **Easy Integration**: Works with Laravel's built-in logging system
- 🧵 **Message Threading**: Group related logs into threads
- 🎨 **Rich Formatting**: Messages are beautifully formatted in Google Chat
- 🔄 **Automatic Retries**: Built-in retry mechanism for failed messages
- 🎯 **Level-based Emojis**: Different emojis for different log levels
- 🌍 **Environment Aware**: Includes environment information in logs
- ⚡ **Performance**: Configurable timeouts and retry settings
- 🚨 **Exception Handling**: Detailed exception formatting with stack traces

## Requirements 📋

- PHP 8.1 or higher
- Laravel 10.x or 11.x
- Google Chat space with webhook access

## Installation 💿

1. Install the package via composer:
```bash
composer require mepsd/laravel-google-chat-logger
```

2. Add these variables to your `.env` file:
```env
LOG_CHANNEL=google_chat
GOOGLE_CHAT_WEBHOOK_URL=your-webhook-url
```

3. Add this to your `config/logging.php` channels array:
```php
'channels' => [
    'google_chat' => [
        'driver' => 'custom',
        'via' => Mepsd\LaravelGoogleChatLogger\GoogleChatLogger::class,
        'url' => env('GOOGLE_CHAT_WEBHOOK_URL'),
        'level' => env('LOG_LEVEL', 'debug'),
        'retries' => 2,        // Number of retry attempts
        'timeout' => 5,        // Request timeout in seconds
    ],
],
```

## Usage 📝

### Basic Logging

```php
// Simple info message
Log::info('User registered successfully', ['user_id' => 1]);

// Error with exception
try {
    // Some code
} catch (Exception $e) {
    Log::error('Process failed', [
        'exception' => $e,
        'user_id' => 1
    ]);
}
```

### Message Threading

Group related logs into threads:

```php
// Order Processing Thread
$threadKey = 'order-' . $orderId;

Log::info('Order received', [
    'thread_key' => $threadKey,
    'order_id' => $orderId,
    'amount' => 99.99
]);

Log::info('Processing payment', [
    'thread_key' => $threadKey,
    'payment_method' => 'credit_card'
]);

Log::info('Order completed', [
    'thread_key' => $threadKey,
    'status' => 'completed'
]);

// User Activity Thread
$userThread = 'user-' . $userId;

Log::info('User logged in', [
    'thread_key' => $userThread,
    'ip' => $request->ip()
]);

Log::warning('Failed login attempt', [
    'thread_key' => $userThread,
    'attempts' => 3
]);
```

### Exception Handling

Exceptions are automatically formatted with details:

```php
try {
    // Your code
} catch (Exception $e) {
    Log::error('Process failed', [
        'thread_key' => 'process-123',
        'exception' => $e,
        'additional_data' => $data
    ]);
}
```

### Log Levels and Emojis

Each log level has its own emoji:
- 🚨 EMERGENCY - System is unusable
- ⚠️ ALERT - Action must be taken immediately
- 🔥 CRITICAL - Critical conditions
- ❌ ERROR - Error conditions
- ⚠️ WARNING - Warning conditions
- 📝 NOTICE - Normal but significant conditions
- ℹ️ INFO - Informational messages
- 🐛 DEBUG - Debug-level messages

### Message Format

Messages in Google Chat will look like:
```
[local] ℹ️ *INFO*
```
Your message here

Context:
{
    "user_id": 123,
    "action": "login"
}
```

## Configuration Options

Full configuration options:

```php
'google_chat' => [
    'driver' => 'custom',
    'via' => Mepsd\LaravelGoogleChatLogger\GoogleChatLogger::class,
    'url' => env('GOOGLE_CHAT_WEBHOOK_URL'),
    'level' => env('LOG_LEVEL', 'debug'),
    'retries' => 2,              // Number of retry attempts
    'timeout' => 5,              // Request timeout in seconds
],
```

## Setting Up Google Chat Webhook 🔗

1. Open your Google Chat space
2. Click the space name to open the dropdown menu
3. Select "Manage webhooks"
4. Click "Add webhook"
5. Name your webhook (e.g., "Laravel Logs")
6. Copy the webhook URL
7. Add the URL to your `.env` file

## Best Practices 🎯

1. Use meaningful thread keys:
```php
'user-{id}'        // For user activities
'order-{id}'       // For order processing
'deploy-{date}'    // For deployments
'job-{id}'         // For background jobs
```

2. Group related logs:
```php
$threadKey = 'payment-' . $paymentId;
Log::info('Starting payment', ['thread_key' => $threadKey]);
Log::info('Processing', ['thread_key' => $threadKey]);
Log::info('Completed', ['thread_key' => $threadKey]);
```

3. Include context data:
```php
Log::info('User action', [
    'thread_key' => 'user-123',
    'action' => 'profile_update',
    'changes' => ['name' => 'New Name'],
    'ip' => $request->ip()
]);
```

## Testing 🧪

```bash
composer test
```

## Security 🔒

If you discover any security-related issues, please use the issue tracker.

## Credits 👏

- [Paras Suthar Darji](https://github.com/mepsd)
- [All Contributors](../../contributors)

## License 📄

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.