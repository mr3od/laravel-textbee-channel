<?php

declare(strict_types=1);

namespace Mr3od\Textbee\Exceptions;

class InvalidConfigException extends \Exception
{
    public static function missingConfig(): self
    {
        return new self('Missing config. You must set api_key and device_id');
    }
}
