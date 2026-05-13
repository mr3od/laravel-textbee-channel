<?php

namespace Mr3od\Textbee\Tests\Unit;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Mr3od\Textbee\Exceptions\CouldNotSendNotification;
use Mr3od\Textbee\Textbee;
use Mr3od\Textbee\TextbeeChannel;
use Mr3od\Textbee\TextbeeSmsMessage;
use PHPUnit\Framework\Attributes\Test;

class TextbeeChannelTest extends MockeryTestCase
{
    /** @var TextbeeChannel|MockInterface */
    protected $channel;

    /** @var Textbee|MockInterface */
    protected $textbee;

    /** @var Dispatcher|MockInterface */
    protected $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->textbee = Mockery::mock(Textbee::class);
        $this->textbee->shouldReceive('isEnabled')->andReturn(true)->byDefault();
        $this->dispatcher = Mockery::mock(Dispatcher::class);

        $this->channel = new TextbeeChannel($this->textbee, $this->dispatcher);
    }

    #[Test]
    public function it_will_not_send_a_message_if_not_enabled()
    {
        $notifiable = new TextbeeNotifiable;
        $notification = Mockery::mock(Notification::class);

        $this->textbee->shouldReceive('isEnabled')->andReturn(false);

        $this->dispatcher->shouldNotReceive('dispatch');

        $result = $this->channel->send($notifiable, $notification);

        $this->assertNull($result);
    }

    #[Test]
    public function it_will_not_send_a_message_without_known_receiver()
    {
        $notifiable = new TextbeeNotifiable;
        $notification = Mockery::mock(Notification::class);

        $this->dispatcher->shouldReceive('dispatch')
            ->atLeast()->once()
            ->with(Mockery::type(NotificationFailed::class));

        $this->expectException(CouldNotSendNotification::class);

        $this->channel->send($notifiable, $notification);
    }

    #[Test]
    public function it_will_send_a_sms_message_to_the_result_of_the_route_method_of_the_notifiable()
    {
        $notifiable = new TextbeeNotifiableWithMethod;
        $notification = Mockery::mock(Notification::class);

        $message = new TextbeeSmsMessage('Message text');
        $notification->shouldReceive('toTextbee')->andReturn($message);

        $this->textbee->shouldReceive('sendMessage')
            ->atLeast()->once()
            ->with($message, '+1111111111');

        $this->channel->send($notifiable, $notification);
    }

    #[Test]
    public function it_will_send_a_sms_message_to_the_result_of_the_route_method_if_it_uses_textbee_channel_explicitly()
    {
        $notifiable = new TextbeeNotifiableWithChannel;
        $notification = Mockery::mock(Notification::class);

        $message = new TextbeeSmsMessage('Message text');
        $notification->shouldReceive('toTextbee')->andReturn($message);

        $this->textbee->shouldReceive('sendMessage')
            ->atLeast()->once()
            ->with($message, '+1111111111');

        $this->channel->send($notifiable, $notification);
    }

    #[Test]
    public function it_will_convert_a_string_to_a_sms_message()
    {
        $notifiable = new TextbeeNotifiableWithAttribute;
        $notification = Mockery::mock(Notification::class);

        $notification->shouldReceive('toTextbee')->andReturn('Message text');

        $this->textbee->shouldReceive('sendMessage')
            ->atLeast()->once()
            ->with(Mockery::type(TextbeeSmsMessage::class), Mockery::any());

        $this->channel->send($notifiable, $notification);
    }

    #[Test]
    public function it_will_fire_an_event_in_case_of_an_invalid_message()
    {
        $notifiable = new TextbeeNotifiableWithAttribute;
        $notification = Mockery::mock(Notification::class);

        $notification->shouldReceive('toTextbee')->andReturn(-1);

        $this->dispatcher->shouldReceive('dispatch')
            ->atLeast()->once()
            ->with(Mockery::type(NotificationFailed::class));

        $this->expectException(CouldNotSendNotification::class);

        $this->channel->send($notifiable, $notification);
    }
}

class TextbeeNotifiable
{
    public $phone_number = null;

    public function routeNotificationFor() {}
}

class TextbeeNotifiableWithChannel
{
    public function routeNotificationFor(string $channel)
    {
        if ($channel === TextbeeChannel::class) {
            return '+1111111111';
        }
    }
}

class TextbeeNotifiableWithMethod
{
    public function routeNotificationFor(string $channel)
    {
        if ($channel === 'textbee') {
            return '+1111111111';
        }
    }
}

class TextbeeNotifiableWithAttribute
{
    public $phone_number = '+22222222222';

    public function routeNotificationFor() {}
}
