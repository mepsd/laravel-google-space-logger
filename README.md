# Laravel Google Chat Logger 🚀

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mepsd/laravel-google-chat-logger.svg?style=flat-square)](https://packagist.org/packages/mepsd/laravel-google-chat-logger)
[![Total Downloads](https://img.shields.io/packagist/dt/mepsd/laravel-google-chat-logger.svg?style=flat-square)](https://packagist.org/packages/mepsd/laravel-google-chat-logger)
[![Tests](https://github.com/mepsd/laravel-google-chat-logger/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/mepsd/laravel-google-chat-logger/actions/workflows/run-tests.yml)
[![License](https://img.shields.io/packagist/l/mepsd/laravel-google-chat-logger.svg?style=flat-square)](https://packagist.org/packages/mepsd/laravel-google-chat-logger)

Send your Laravel application logs directly to Google Chat spaces with rich formatting, emoji support, and context data.

## Features 🌟

- 🎯 **Easy Integration**: Works seamlessly with Laravel's logging system
- 🎨 **Rich Formatting**: Messages are formatted beautifully in Google Chat
- 🔔 **Level-based Emojis**: Different emojis for different log levels
- 🌍 **Environment Aware**: Includes environment information in logs
- 📊 **Context Support**: Properly formats arrays and objects in context data
- ⚡ **Performance**: Asynchronous logging with failure handling
- 🛡️ **Error Proof**: Silent fail mechanism to prevent application disruption
- 🔧 **Highly Configurable**: Customize log levels, formatting, and more

## Requirements 📋

- PHP 8.1 or higher
- Laravel 10.x or 11.x
- Google Chat space with webhook access

## Installation 💿

You can install the package via composer:

```bash
composer require mepsd/laravel-google-chat-logger
```

## Configuration ⚙️

1. Publish the configuration file:

```bash
php artisan vendor:publish --tag="google-chat-logger-config"
```

2. Add these variables to your `.env` file:

```env
GOOGLE_CHAT_WEBHOOK_URL=your-webhook-url
GOOGLE_CHAT_LOG_LEVEL=debug  # optional
```

3. Add the logging channel in `config/logging.php`:

```php
'channels' => [
    'google_chat' => [
        'driver' => 'custom',
        'via' => mepsd\LaravelGoogleChatLogger\GoogleChatLogger::class,
        'url' => config('google-chat-logger.url'),
        'level' => config('google-chat-logger.level', 'debug'),
    ],
],
```

## Usage 📝

### Basic Usage

```php
// Use the channel directly
Log::channel('google_chat')->info('Hello from Laravel!');

// Log an error
Log::channel('google_chat')->error('Something went wrong!');

// Log with context data
Log::channel('google_chat')->warning('User payment failed', [
    'user_id' => 123,
    'amount' => 99.99,
    'currency' => 'USD'
]);
```

### Make it Your Default Logger

In your `.env` file:

```env
LOG_CHANNEL=google_chat
```

Then you can use the standard Log facade:

```php
Log::info('This will go to Google Chat');
```

### Stack Multiple Channels

```php
// In .env
LOG_CHANNEL=stack

// In config/logging.php
'stack' => [
    'driver' => 'stack',
    'channels' => ['single', 'google_chat'],
],
```

## Message Formatting 🎨

Messages in Google Chat will be formatted as follows:

```
[local] ℹ️ *INFO*
```
Your message here
```

Context:
{
    "user_id": 123,
    "amount": 99.99
}
```

## Log Levels and Emojis 🎯

- 🚨 EMERGENCY
- ⚠️ ALERT
- 🔥 CRITICAL
- ❌ ERROR
- ⚠️ WARNING
- 📝 NOTICE
- ℹ️ INFO
- 🐛 DEBUG

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

If you discover any security-related issues, please email your.email@example.com instead of using the issue tracker.

## Credits 👏

- [Your Name](https://github.com/yourusername)
- [All Contributors](../../contributors)

## License 📄

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Support ❤️

If you find this package helpful, please consider:
- Starring the repository
- Contributing to the code
- Reporting issues or suggesting improvements

Happy Logging! 🎉