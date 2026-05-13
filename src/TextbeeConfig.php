<?php

namespace Mr3od\Textbee;

class TextbeeConfig
{
    public function __construct(
        private readonly array $config
    ) {}

    public function enabled(): bool
    {
        return $this->config['enabled'] ?? true;
    }

    public function getApiKey(): ?string
    {
        return $this->config['api_key'] ?? null;
    }

    public function getDeviceId(): ?string
    {
        return $this->config['device_id'] ?? null;
    }

    public function getBaseUrl(): string
    {
        return $this->config['base_url'] ?? 'https://api.textbee.dev/api/v1';
    }

    public function getDebugTo(): ?string
    {
        return $this->config['debug_to'] ?? null;
    }
}
