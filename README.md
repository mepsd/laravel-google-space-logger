# Laravel Google Chat Logger 🚀

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mepsd/laravel-google-chat-logger.svg?style=flat-square)](https://packagist.org/packages/mepsd/laravel-google-chat-logger)
[![Total Downloads](https://img.shields.io/packagist/dt/mepsd/laravel-google-chat-logger.svg?style=flat-square)](https://packagist.org/packages/mepsd/laravel-google-chat-logger)
[![License](https://img.shields.io/packagist/l/mepsd/laravel-google-chat-logger.svg?style=flat-square)](https://packagist.org/packages/mepsd/laravel-google-chat-logger)

Send your Laravel application logs directly to Google Chat with rich formatting, emoji support, and automatic retries.

## Features 🌟

- 🎯 **Easy Integration**: Works with Laravel's built-in logging system
- 🎨 **Rich Formatting**: Messages are beautifully formatted in Google Chat
- 🔄 **Automatic Retries**: Built-in retry mechanism for failed messages
- 🎯 **Level-based Emojis**: Different emojis for different log levels
- 🌍 **Environment Aware**: Includes environment information in logs
- ⚡ **Performance**: Configurable timeouts and retry settings

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
        'retries' => 2,
        'timeout' => 5,
    ],
],
```

## Usage 📝

### Basic Logging

```php
// Send info message
Log::info('User registered successfully', ['user_id' => 1]);

// Send error message
Log::error('Payment failed', [
    'user_id' => 1,
    'amount' => 99.99,
    'error' => $exception->getMessage()
]);

// Send debug message
Log::debug('Debug information', $debugData);
```

### Log Levels and Emojis

Each log level has its own emoji:
- 🚨 EMERGENCY
- ⚠️ ALERT
- 🔥 CRITICAL
- ❌ ERROR
- ⚠️ WARNING
- 📝 NOTICE
- ℹ️ INFO
- 🐛 DEBUG

### Message Format

Messages in Google Chat will look like:
```
[local] ℹ️ *INFO*
```
Your message here
```

### Configuration Options

```php
'google_chat' => [
    'driver' => 'custom',
    'via' => Mepsd\LaravelGoogleChatLogger\GoogleChatLogger::class,
    'url' => env('GOOGLE_CHAT_WEBHOOK_URL'),
    'level' => env('LOG_LEVEL', 'debug'),
    'retries' => 2,        // Number of retry attempts
    'timeout' => 5,        // Request timeout in seconds
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