<?php

namespace Mr3od\Textbee;

use Illuminate\Support\Facades\Http;
use Mr3od\Textbee\Exceptions\CouldNotSendNotification;

class TextbeeService
{
    public function __construct(
        protected string $apiKey,
        protected string $baseUrl = 'https://api.textbee.dev/api/v1'
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function send(string $deviceId, array $recipients, array $params): array
    {
        $endpoint = $this->baseUrl."/gateway/devices/{$deviceId}/send-sms";

        $payload = [
            'message'    => $params['body'] ?? '',
            'recipients' => $recipients,
        ];

        if (! empty($params['simSubscriptionId'])) {
            $payload['simSubscriptionId'] = (int) $params['simSubscriptionId'];
        }

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Accept'    => 'application/json',
        ])->timeout(5)->post($endpoint, $payload);

        if ($response->failed()) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($response->body());
        }

        return $response->json();
    }
}
