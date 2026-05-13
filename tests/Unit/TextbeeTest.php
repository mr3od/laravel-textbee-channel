<?php

namespace Mr3od\Textbee\Tests\Unit;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Mr3od\Textbee\Exceptions\CouldNotSendNotification;
use Mr3od\Textbee\Textbee;
use Mr3od\Textbee\TextbeeConfig;
use Mr3od\Textbee\TextbeeMessage;
use Mr3od\Textbee\TextbeeService;
use Mr3od\Textbee\TextbeeSmsMessage;
use PHPUnit\Framework\Attributes\Test;

class TextbeeTest extends MockeryTestCase
{
    /** @var Textbee */
    protected $textbee;

    /** @var TextbeeService|MockInterface */
    protected $service;

    /** @var TextbeeConfig|MockInterface */
    protected $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = Mockery::mock(TextbeeService::class);
        $this->config = Mockery::mock(TextbeeConfig::class);

        $this->textbee = new Textbee($this->service, $this->config);
    }

    #[Test]
    public function it_can_send_a_sms_message()
    {
        $message = new TextbeeSmsMessage('Message text');
        $message->deviceId('device-123');
        $message->simSubscriptionId(2);

        $this->config->shouldReceive('getDebugTo')->once()->andReturn(null);
        $this->config->shouldReceive('getDeviceId')->never();

        $this->service->shouldReceive('send')
            ->atLeast()->once()
            ->with('device-123', ['+1111111111'], Mockery::on(function ($params) {
                return $params['body'] === 'Message text'
                    && $params['simSubscriptionId'] === 2;
            }))
            ->andReturn(['success' => true]);

        $this->textbee->sendMessage($message, '+1111111111');
    }

    #[Test]
    public function it_uses_config_device_id_when_message_has_none()
    {
        $message = new TextbeeSmsMessage('Message text');

        $this->config->shouldReceive('getDebugTo')->once()->andReturn(null);
        $this->config->shouldReceive('getDeviceId')->once()->andReturn('config-device');

        $this->service->shouldReceive('send')
            ->atLeast()->once()
            ->with('config-device', ['+1111111111'], Mockery::any())
            ->andReturn([]);

        $this->textbee->sendMessage($message, '+1111111111');
    }

    #[Test]
    public function it_will_throw_an_exception_when_device_id_is_missing()
    {
        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('Missing `device_id`');

        $message = new TextbeeSmsMessage('Message text');

        $this->config->shouldReceive('getDebugTo')->once()->andReturn(null);
        $this->config->shouldReceive('getDeviceId')->once()->andReturn(null);

        $this->textbee->sendMessage($message, '+1111111111');
    }

    #[Test]
    public function it_will_throw_an_exception_for_an_unrecognized_message_object()
    {
        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('Notification was not sent. Message object class');

        $this->textbee->sendMessage(new InvalidTextbeeMessage, null);
    }

    #[Test]
    public function it_should_use_debug_to_when_set()
    {
        $debugTo = '+1222222222';

        $message = new TextbeeSmsMessage('Message text');
        $message->deviceId('device-123');

        $this->config->shouldReceive('getDebugTo')->once()->andReturn($debugTo);

        $this->service->shouldReceive('send')
            ->atLeast()->once()
            ->with('device-123', [$debugTo], Mockery::any())
            ->andReturn([]);

        $this->textbee->sendMessage($message, '+1111111111');
    }

    #[Test]
    public function it_can_send_to_multiple_recipients()
    {
        $message = new TextbeeSmsMessage('Message text');
        $message->deviceId('device-123');
        $message->to(['+1111111111', '+2222222222']);

        $this->config->shouldReceive('getDebugTo')->once()->andReturn(null);
        $this->config->shouldReceive('getDeviceId')->never();

        $this->service->shouldReceive('send')
            ->atLeast()->once()
            ->with('device-123', ['+1111111111', '+2222222222'], Mockery::any())
            ->andReturn(['success' => true]);

        $this->textbee->sendMessage($message, null);
    }

    #[Test]
    public function it_overrides_recipients_with_debug_to()
    {
        $message = new TextbeeSmsMessage('Message text');
        $message->deviceId('device-123');
        $message->to(['+1111111111', '+2222222222']);

        $this->config->shouldReceive('getDebugTo')->once()->andReturn('+9999999999');

        $this->service->shouldReceive('send')
            ->atLeast()->once()
            ->with('device-123', ['+9999999999'], Mockery::any())
            ->andReturn([]);

        $this->textbee->sendMessage($message, null);
    }
}

class InvalidTextbeeMessage extends TextbeeMessage {}
