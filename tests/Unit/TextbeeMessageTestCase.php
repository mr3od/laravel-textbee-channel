<?php

namespace Mr3od\Textbee\Tests\Unit;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mr3od\Textbee\TextbeeMessage;
use PHPUnit\Framework\Attributes\Test;

abstract class TextbeeMessageTestCase extends MockeryTestCase
{
    protected TextbeeMessage $message;

    #[Test]
    abstract public function it_can_accept_a_message_when_constructing_a_message();

    #[Test]
    abstract public function it_provides_a_create_method();

    #[Test]
    public function it_can_set_the_content()
    {
        $this->message->content('myMessage');

        $this->assertEquals('myMessage', $this->message->content);
    }

    #[Test]
    public function it_can_set_the_device_id()
    {
        $this->message->deviceId('device-abc');

        $this->assertEquals('device-abc', $this->message->deviceId);
    }

    #[Test]
    public function it_can_return_the_device_id_using_getter()
    {
        $this->message->deviceId('device-abc');

        $this->assertEquals('device-abc', $this->message->getDeviceId());
    }

    #[Test]
    public function it_can_set_the_sim_subscription_id()
    {
        $this->message->simSubscriptionId(1);

        $this->assertEquals(1, $this->message->simSubscriptionId);
    }
}
