<?php

namespace Mr3od\Textbee;

abstract class TextbeeMessage
{
    public ?string $deviceId = null;

    /** Optional SIM subscription ID if you're using multi‑SIM devices. */
    public ?int $simSubscriptionId = null;

    public array $recipients = [];

    public function __construct(
        public string $content = ''
    ) {}

    public static function create(string $content = ''): static
    {
        return new static($content);
    }

    public function content(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function deviceId(string $deviceId): self
    {
        $this->deviceId = $deviceId;
        return $this;
    }

    public function simSubscriptionId(int $id): self
    {
        $this->simSubscriptionId = $id;
        return $this;
    }

    public function to(string|array $recipients): self
    {
        $this->recipients = (array) $recipients;
        return $this;
    }

    public function getDeviceId(): ?string
    {
        return $this->deviceId;
    }
}
