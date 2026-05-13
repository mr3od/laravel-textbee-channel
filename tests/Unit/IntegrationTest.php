<?php

namespace Mr3od\Textbee\Tests\Unit;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Mr3od\Textbee\Textbee;
use Mr3od\Textbee\TextbeeChannel;
use Mr3od\Textbee\TextbeeConfig;
use Mr3od\Textbee\TextbeeService;
use Mr3od\Textbee\TextbeeSmsMessage;
use PHPUnit\Framework\Attributes\Test;

class IntegrationTest extends MockeryTestCase
{
    /** @var TextbeeService|MockInterface */
    protected $service;

    /** @var Notification|MockInterface */
    protected $notification;

    /** @var Dispatcher */
    protected $events;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = Mockery::mock(TextbeeService::class);
        $this->events = Mockery::mock(Dispatcher::class);
        $this->notification = Mockery::mock(Notification::class);
    }

    #[Test]
    public function it_can_send_a_sms_message()
    {
        $message = TextbeeSmsMessage::create('Message text');
        $message->deviceId('device-123');
        $this->notification->shouldReceive('toTextbee')->andReturn($message);

        $config = new TextbeeConfig([
            'api_key'   => 'test-api-key',
            'device_id' => 'device-123',
        ]);
        $textbee = new Textbee($this->service, $config);
        $channel = new TextbeeChannel($textbee, $this->events);

        $this->service->shouldReceive('send')
            ->atLeast()->once()
            ->with('device-123', ['+22222222222'], Mockery::on(fn ($p) => $p['body'] === 'Message text'))
            ->andReturn(['success' => true]);

        $channel->send(new IntegrationNotifiable, $this->notification);
    }

    #[Test]
    public function it_routes_to_debug_to_when_set()
    {
        $message = TextbeeSmsMessage::create('Message text');
        $message->deviceId('device-123');
        $this->notification->shouldReceive('toTextbee')->andReturn($message);

        $config = new TextbeeConfig([
            'api_key'   => 'test-api-key',
            'device_id' => 'device-123',
            'debug_to'  => '+99999999999',
        ]);
        $textbee = new Textbee($this->service, $config);
        $channel = new TextbeeChannel($textbee, $this->events);

        $this->service->shouldReceive('send')
            ->atLeast()->once()
            ->with('device-123', ['+99999999999'], Mockery::any())
            ->andReturn(['success' => true]);

        $channel->send(new IntegrationNotifiable, $this->notification);
    }
}

class IntegrationNotifiable
{
    public $phone_number = '+22222222222';

    public function routeNotificationFor() {}
}
