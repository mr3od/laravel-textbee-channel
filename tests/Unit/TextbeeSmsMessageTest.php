<?php

namespace Mr3od\Textbee\Tests\Unit;

use Mr3od\Textbee\TextbeeSmsMessage;
use PHPUnit\Framework\Attributes\Test;

class TextbeeSmsMessageTest extends TextbeeMessageTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->message = new TextbeeSmsMessage;
    }

    #[Test]
    public function it_can_accept_a_message_when_constructing_a_message()
    {
        $message = new TextbeeSmsMessage('myMessage');

        $this->assertEquals('myMessage', $message->content);
    }

    #[Test]
    public function it_provides_a_create_method()
    {
        $message = TextbeeSmsMessage::create('myMessage');

        $this->assertEquals('myMessage', $message->content);
    }
}
