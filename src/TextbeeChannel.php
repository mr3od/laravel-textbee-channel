<?php

namespace Mr3od\Textbee;

use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use Mr3od\Textbee\Exceptions\CouldNotSendNotification;

class TextbeeChannel
{
    public function __construct(
        protected Textbee $textbee,
        protected Dispatcher $events
    ) {}

    /**
     * Send the given notification.
     */
    public function send(mixed $notifiable, Notification $notification): mixed
    {
        if (! $this->isEnabled()) {
            return null;
        }

        try {
            $to = $this->getTo($notifiable, $notification);
            $message = $this->getMessage($notifiable, $notification);

            if (is_string($message)) {
                $message = new TextbeeSmsMessage($message);
            }

            if (! $message instanceof TextbeeMessage) {
                throw CouldNotSendNotification::invalidMessageObject($message);
            }

            return $this->textbee->sendMessage($message, $to);
        } catch (Exception $exception) {
            $event = new NotificationFailed(
                $notifiable,
                $notification,
                'textbee',
                ['message' => $exception->getMessage(), 'exception' => $exception]
            );

            $this->events->dispatch($event);

            throw $exception;
        }
    }

    protected function getMessage(mixed $notifiable, Notification $notification)
    {
        return $notification->toTextbee($notifiable);
    }

    protected function isEnabled(): bool
    {
        return $this->textbee->isEnabled();
    }

    protected function getTo(mixed $notifiable, $notification = null)
    {
        if ($to = $notifiable->routeNotificationFor('textbee', $notification)) {
            return $to;
        }

        if ($to = $notifiable->routeNotificationFor(self::class, $notification)) {
            return $to;
        }

        if (method_exists($notifiable, 'routeNotificationForTextbee') &&
            $to = $notifiable->routeNotificationForTextbee($notification)) {
            return $to;
        }

        if (isset($notifiable->phone_number)) {
            return $notifiable->phone_number;
        }

        throw CouldNotSendNotification::invalidReceiver();
    }
}
