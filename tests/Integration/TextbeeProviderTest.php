<?php

declare(strict_types=1);

namespace Mr3od\Textbee\Tests\Integration;

use Mr3od\Textbee\Exceptions\InvalidConfigException;
use Mr3od\Textbee\Tests\IntegrationTestCase;
use Mr3od\Textbee\TextbeeChannel;
use Mr3od\Textbee\TextbeeConfig;
use Mr3od\Textbee\TextbeeProvider;
use Mr3od\Textbee\TextbeeService;
use PHPUnit\Framework\Attributes\Test;

class TextbeeProviderTest extends IntegrationTestCase
{
    #[Test]
    public function it_cannot_create_the_application_without_config()
    {
        $this->expectException(InvalidConfigException::class);

        $this->app->get(TextbeeChannel::class);
    }

    #[Test]
    public function it_can_create_the_application_without_device_id()
    {
        $this->app['config']->set('textbee-notification-channel.api_key', 'key');

        $this->assertInstanceOf(TextbeeChannel::class, $this->app->get(TextbeeChannel::class));
    }

    #[Test]
    public function it_can_create_the_application_with_device_id()
    {
        $this->app['config']->set('textbee-notification-channel.api_key', 'key');
        $this->app['config']->set('textbee-notification-channel.device_id', '1234');

        $this->assertInstanceOf(TextbeeChannel::class, $this->app->get(TextbeeChannel::class));
    }

    #[Test]
    public function it_cannot_create_the_application_without_api_key()
    {
        $this->app['config']->set('textbee-notification-channel.device_id', '1234');

        $this->expectException(InvalidConfigException::class);
        $this->app->get(TextbeeChannel::class);
    }

    #[Test]
    public function it_can_create_the_application_with_api_key()
    {
        $this->app['config']->set('textbee-notification-channel.api_key', 'key');
        $this->app['config']->set('textbee-notification-channel.device_id', '1234');

        $this->assertInstanceOf(TextbeeChannel::class, $this->app->get(TextbeeChannel::class));
    }

    #[Test]
    public function it_can_create_the_application_with_custom_base_url()
    {
        $this->app['config']->set('textbee-notification-channel.api_key', 'key');
        $this->app['config']->set('textbee-notification-channel.base_url', 'https://sms.example.com/api/v1');

        $this->assertInstanceOf(TextbeeChannel::class, $this->app->get(TextbeeChannel::class));
    }

    #[Test]
    public function it_provides_three_classes()
    {
        $provides = (new TextbeeProvider($this->app))->provides();

        $this->assertTrue(in_array(TextbeeChannel::class, $provides));
        $this->assertTrue(in_array(TextbeeConfig::class, $provides));
        $this->assertTrue(in_array(TextbeeService::class, $provides));
    }
}
