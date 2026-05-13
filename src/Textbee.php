<?php

namespace Mr3od\Textbee;

use Mr3od\Textbee\Exceptions\CouldNotSendNotification;

class Textbee
{
    public function __construct(
        protected TextbeeService $textbeeService,
        protected TextbeeConfig $config
    ) {}

    public function isEnabled(): bool
    {
        return $this->config->enabled();
    }

    /**
     * @throws CouldNotSendNotification
     */
    public function sendMessage(TextbeeMessage $message, ?string $to = null): array
    {
        if ($message instanceof TextbeeSmsMessage) {
            return $this->sendSmsMessage($message, $to);
        }

        throw CouldNotSendNotification::invalidMessageObject($message);
    }

    protected function sendSmsMessage(TextbeeSmsMessage $message, ?string $to): array
    {
        $debugTo = $this->config->getDebugTo();

        if (! empty($debugTo)) {
            $recipients = [$debugTo];
        } elseif (! empty($message->recipients)) {
            $recipients = $message->recipients;
        } else {
            $recipients = [$to];
        }

        $params = [
            'body' => trim($message->content),
        ];

        $from = $this->getDeviceId($message);
        if (empty($from)) {
            throw CouldNotSendNotification::missingDeviceId();
        }

        if ($message->simSubscriptionId !== null) {
            $params['simSubscriptionId'] = $message->simSubscriptionId;
        }

        return $this->textbeeService->send($from, $recipients, $params);
    }

    protected function getDeviceId(TextbeeMessage $message): ?string
    {
        return $message->getDeviceId() ?: $this->config->getDeviceId();
    }
}
