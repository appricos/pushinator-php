<?php

namespace Pushinator;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class PushinatorClient
{
    private string $apiToken;
    private ClientInterface $client;

    private static string $baseUrl = "https://api.pushinator.com";
    private static string $userAgent = 'pushinator-php/1.0';

    /**
     * PushinatorClient constructor.
     * @param string $apiToken
     * @param ClientInterface|null $client
     */
    public function __construct(string $apiToken, ?ClientInterface $client = null)
    {
        if (empty($apiToken)) {
            throw new \InvalidArgumentException('API token cannot be empty');
        }

        $this->apiToken = $apiToken;
        $this->client = $client ?? new Client();
    }

    /**
     * Send a notification synchronously.
     *
     * @param string $channelId
     * @param string $notification
     * @return void
     */
    public function sendNotification(string $channelId, string $notification): void
    {
        try {
            $response = $this->client->request('POST', self::$baseUrl . "/api/v2/notifications/send", [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiToken}",
                    'Content-Type' => 'application/json',
                    'User-Agent' => self::$userAgent,
                ],
                'json' => [
                    'channel_id' => $channelId,
                    'content' => $notification,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException(
                    "Failed to send notification. Status: {$response->getStatusCode()}, Body: {$response->getBody()}"
                );
            }
        } catch (GuzzleException $e) {
            throw new \RuntimeException(
                "Failed to send notification: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }
    }
}
