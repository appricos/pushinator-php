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

    /**
     * List all channels.
     *
     * @return array
     */
    public function listChannels(): array
    {
        return $this->request('GET', '/api/v2/channels');
    }

    /**
     * Create a new channel.
     *
     * @param string $name
     * @param string|null $description
     * @param bool|null $acknowledgmentEnabled
     * @return array
     */
    public function createChannel(string $name, ?string $description = null, ?bool $acknowledgmentEnabled = null): array
    {
        $body = ['name' => $name];

        if ($description !== null) {
            $body['description'] = $description;
        }

        if ($acknowledgmentEnabled !== null) {
            $body['acknowledgment_enabled'] = $acknowledgmentEnabled;
        }

        return $this->request('POST', '/api/v2/channels', $body);
    }

    /**
     * Get a channel by ID.
     *
     * @param string $channelId
     * @return array
     */
    public function getChannel(string $channelId): array
    {
        return $this->request('GET', "/api/v2/channels/{$channelId}");
    }

    /**
     * Update a channel by ID.
     *
     * @param string $channelId
     * @param string $name
     * @param string|null $description
     * @param bool|null $acknowledgmentEnabled
     * @return array
     */
    public function updateChannel(string $channelId, string $name, ?string $description = null, ?bool $acknowledgmentEnabled = null): array
    {
        $body = ['name' => $name];

        if ($description !== null) {
            $body['description'] = $description;
        }

        if ($acknowledgmentEnabled !== null) {
            $body['acknowledgment_enabled'] = $acknowledgmentEnabled;
        }

        return $this->request('PUT', "/api/v2/channels/{$channelId}", $body);
    }

    /**
     * Delete a channel by ID.
     *
     * @param string $channelId
     * @return array
     */
    public function deleteChannel(string $channelId): array
    {
        return $this->request('DELETE', "/api/v2/channels/{$channelId}");
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $body
     * @return array
     */
    private function request(string $method, string $path, array $body = []): array
    {
        try {
            $options = [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiToken}",
                    'Content-Type' => 'application/json',
                    'User-Agent' => self::$userAgent,
                ],
            ];

            if (!empty($body)) {
                $options['json'] = $body;
            }

            $response = $this->client->request($method, self::$baseUrl . $path, $options);

            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException(
                    "Request failed. Status: {$response->getStatusCode()}, Body: {$response->getBody()}"
                );
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \RuntimeException(
                "Request failed: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }
    }
}
