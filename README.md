# TextBee notifications channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mr3od/laravel-textbee-channel.svg)](https://packagist.org/packages/mr3od/laravel-textbee-channel)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://github.com/mr3od/laravel-textbee-channel/actions/workflows/ci.yml/badge.svg)](https://github.com/mr3od/laravel-textbee-channel/actions/workflows/ci.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/mr3od/laravel-textbee-channel.svg)](https://packagist.org/packages/mr3od/laravel-textbee-channel)

This package makes it easy to send SMS notifications via [TextBee](https://textbee.dev) with Laravel 12 and 13.

TextBee lets you send SMS messages through an Android device you own — no carrier contract required.

## Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Installation

```bash
composer require mr3od/laravel-textbee-channel
```

## Configuration

Add your TextBee API key and device ID to `.env`:

```dotenv
TEXTBEE_API_KEY=your-api-key-here
TEXTBEE_DEVICE_ID=your-device-id-here       # optional if set per-message
TEXTBEE_ENABLED=true
TEXTBEE_BASE_URL=https://api.textbee.dev/api/v1  # optional — override for self-hosted instances
TEXTBEE_DEBUG_TO=+1234567890                # optional — redirect all messages here during development
```

Publish the config file:

```bash
php artisan vendor:publish --provider="Mr3od\Textbee\TextbeeProvider"
```

This creates `config/textbee-notification-channel.php`.

## Usage

Use the channel in your notification's `via()` method:

```php
use Mr3od\Textbee\TextbeeChannel;
use Mr3od\Textbee\TextbeeSmsMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [TextbeeChannel::class];
    }

    public function toTextbee($notifiable)
    {
        return (new TextbeeSmsMessage())
            ->content("Your {$notifiable->service} account was approved!");
    }
}
```

The channel resolves the recipient's phone number by calling `routeNotificationFor('textbee')` on the notifiable, then `routeNotificationFor(TextbeeChannel::class)`, and finally falls back to the `phone_number` attribute.

Add the routing method to your notifiable model to override the default:

```php
public function routeNotificationForTextbee()
{
    return $this->mobile_number;
}
```

### Per-message device override

If you have multiple Android devices registered in TextBee you can pick one per message:

```php
public function toTextbee($notifiable)
{
    return TextbeeSmsMessage::create("Your OTP is 123456")
        ->deviceId('your-other-device-id');
}
```

### Multiple recipients

```php
return TextbeeSmsMessage::create('Your OTP is 123456')
    ->to(['+1234567890', '+0987654321']);
```

### Multi-SIM devices

```php
return TextbeeSmsMessage::create('Hello!')
    ->simSubscriptionId(1);
```

## Available Message methods

#### `TextbeeSmsMessage`

| Method | Description |
|--------|-------------|
| `content(string $text)` | SMS body |
| `to(string\|array $recipients)` | One or more recipient phone numbers |
| `deviceId(string $id)` | Override the TextBee device to send from |
| `simSubscriptionId(int $id)` | SIM slot to use on multi-SIM devices |

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## Testing

```bash
composer test
```

## Security

If you discover a security issue please email me@mr3od.dev instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Abdulrahman Alwan](https://github.com/mr3od)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
