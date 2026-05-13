<?php

namespace Mr3od\Textbee\Tests\Unit;

use Mr3od\Textbee\TextbeeConfig;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TextbeeConfigTest extends TestCase
{
    private function config(array $config = []): TextbeeConfig
    {
        return new TextbeeConfig($config);
    }

    #[Test]
    public function it_returns_a_boolean_whether_it_is_enabled_or_not()
    {
        $this->assertTrue($this->config()->enabled());
        $this->assertTrue($this->config(['enabled' => true])->enabled());
        $this->assertFalse($this->config(['enabled' => false])->enabled());
    }

    #[Test]
    public function it_defaults_to_null_for_nullable_string_config_keys()
    {
        $config = $this->config();

        $this->assertNull($config->getApiKey());
        $this->assertNull($config->getDeviceId());
        $this->assertNull($config->getDebugTo());
    }

    #[Test]
    public function it_defaults_base_url_to_textbee_cloud()
    {
        $this->assertEquals('https://api.textbee.dev/api/v1', $this->config()->getBaseUrl());
    }

    #[Test]
    public function it_returns_string_values_for_config_keys()
    {
        $config = $this->config([
            'api_key'   => 'valid-api-key',
            'device_id' => 'valid-device-id',
            'base_url'  => 'https://sms.example.com/api/v1',
            'debug_to'  => 'valid-debug-to',
        ]);

        $this->assertEquals('valid-api-key', $config->getApiKey());
        $this->assertEquals('valid-device-id', $config->getDeviceId());
        $this->assertEquals('https://sms.example.com/api/v1', $config->getBaseUrl());
        $this->assertEquals('valid-debug-to', $config->getDebugTo());
    }
}
