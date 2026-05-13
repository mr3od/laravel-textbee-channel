<?php

declare(strict_types=1);

namespace Mr3od\Textbee\Exceptions;

use Mr3od\Textbee\TextbeeSmsMessage;

class CouldNotSendNotification extends \Exception
{
    public static function invalidMessageObject(mixed $message): self
    {
        $className = is_object($message) ? get_class($message) : gettype($message);

        return new static("Notification was not sent. Message object class `{$className}` is invalid. It should be `".TextbeeSmsMessage::class.'`');
    }

    public static function missingDeviceId(): self
    {
        return new static('Notification was not sent. Missing `device_id`.');
    }

    public static function invalidReceiver(): self
    {
        return new static('The notifiable did not have a receiving phone number. Add a routeNotificationForTextbee method or a phone_number attribute to your notifiable.');
    }

    public static function serviceRespondedWithAnError($response): self
    {
        $body = substr($response, 0, 200);

        return new static("TextBee API responded with an error: {$body}");
    }
}
