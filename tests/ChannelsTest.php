<?php

namespace Tests\Pushinator;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Pushinator\PushinatorClient;

#[CoversClass(\Pushinator\PushinatorClient::class)]
class ChannelsTest extends TestCase
{
    private function makeClient(array $responses, array &$container = []): PushinatorClient
    {
        $history = Middleware::history($container);
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        return new PushinatorClient('test-token', new Client(['handler' => $handlerStack]));
    }

    private function channelResponse(array $overrides = []): array
    {
        return array_merge([
            'id' => 'channel-123',
            'name' => 'Test Channel',
            'description' => 'A test channel',
            'acknowledgment_enabled' => false,
            'created_at' => '2024-01-01T00:00:00.000000Z',
            'updated_at' => '2024-01-01T00:00:00.000000Z',
        ], $overrides);
    }

    // -------------------------------------------------------------------------
    // listChannels
    // -------------------------------------------------------------------------

    #[Test]
    public function testListChannelsRequest()
    {
        $container = [];
        $responseData = ['data' => [$this->channelResponse()]];
        $pushinator = $this->makeClient(
            [new Response(200, [], json_encode($responseData))],
            $container
        );

        $result = $pushinator->listChannels();

        $request = $container[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('https://api.pushinator.com/api/v2/channels', $request->getUri());
        $this->assertEquals('Bearer test-token', $request->getHeaderLine('Authorization'));
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertEquals('pushinator-php/1.0', $request->getHeaderLine('User-Agent'));
        $this->assertEquals($responseData, $result);
    }

    #[Test]
    public function testListChannelsFailure()
    {
        $pushinator = $this->makeClient([new Response(500)]);

        $this->expectException(\RuntimeException::class);
        $pushinator->listChannels();
    }

    // -------------------------------------------------------------------------
    // createChannel
    // -------------------------------------------------------------------------

    #[Test]
    public function testCreateChannelRequest()
    {
        $container = [];
        $responseData = ['data' => $this->channelResponse()];
        $pushinator = $this->makeClient(
            [new Response(200, [], json_encode($responseData))],
            $container
        );

        $result = $pushinator->createChannel('Test Channel', 'A test channel', true);

        $request = $container[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https://api.pushinator.com/api/v2/channels', $request->getUri());
        $this->assertEquals('Bearer test-token', $request->getHeaderLine('Authorization'));
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertEquals('pushinator-php/1.0', $request->getHeaderLine('User-Agent'));
        $this->assertEquals(
            '{"name":"Test Channel","description":"A test channel","acknowledgment_enabled":true}',
            $request->getBody()->getContents()
        );
        $this->assertEquals($responseData, $result);
    }

    #[Test]
    public function testCreateChannelWithNameOnly()
    {
        $container = [];
        $pushinator = $this->makeClient(
            [new Response(200, [], json_encode(['data' => $this->channelResponse()]))],
            $container
        );

        $pushinator->createChannel('Test Channel');

        $this->assertEquals(
            '{"name":"Test Channel"}',
            $container[0]['request']->getBody()->getContents()
        );
    }

    #[Test]
    public function testCreateChannelFailure()
    {
        $pushinator = $this->makeClient([new Response(500)]);

        $this->expectException(\RuntimeException::class);
        $pushinator->createChannel('Test Channel');
    }

    // -------------------------------------------------------------------------
    // getChannel
    // -------------------------------------------------------------------------

    #[Test]
    public function testGetChannelRequest()
    {
        $container = [];
        $responseData = ['data' => $this->channelResponse()];
        $pushinator = $this->makeClient(
            [new Response(200, [], json_encode($responseData))],
            $container
        );

        $result = $pushinator->getChannel('channel-123');

        $request = $container[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('https://api.pushinator.com/api/v2/channels/channel-123', $request->getUri());
        $this->assertEquals('Bearer test-token', $request->getHeaderLine('Authorization'));
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertEquals('pushinator-php/1.0', $request->getHeaderLine('User-Agent'));
        $this->assertEquals($responseData, $result);
    }

    #[Test]
    public function testGetChannelNotFound()
    {
        $pushinator = $this->makeClient([new Response(404)]);

        $this->expectException(\RuntimeException::class);
        $pushinator->getChannel('channel-123');
    }

    // -------------------------------------------------------------------------
    // updateChannel
    // -------------------------------------------------------------------------

    #[Test]
    public function testUpdateChannelRequest()
    {
        $container = [];
        $responseData = ['data' => $this->channelResponse(['name' => 'Updated Channel'])];
        $pushinator = $this->makeClient(
            [new Response(200, [], json_encode($responseData))],
            $container
        );

        $result = $pushinator->updateChannel('channel-123', 'Updated Channel', 'New description', false);

        $request = $container[0]['request'];
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('https://api.pushinator.com/api/v2/channels/channel-123', $request->getUri());
        $this->assertEquals('Bearer test-token', $request->getHeaderLine('Authorization'));
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertEquals('pushinator-php/1.0', $request->getHeaderLine('User-Agent'));
        $this->assertEquals(
            '{"name":"Updated Channel","description":"New description","acknowledgment_enabled":false}',
            $request->getBody()->getContents()
        );
        $this->assertEquals($responseData, $result);
    }

    #[Test]
    public function testUpdateChannelWithNameOnly()
    {
        $container = [];
        $pushinator = $this->makeClient(
            [new Response(200, [], json_encode(['data' => $this->channelResponse()]))],
            $container
        );

        $pushinator->updateChannel('channel-123', 'Updated Channel');

        $this->assertEquals(
            '{"name":"Updated Channel"}',
            $container[0]['request']->getBody()->getContents()
        );
    }

    #[Test]
    public function testUpdateChannelNotFound()
    {
        $pushinator = $this->makeClient([new Response(404)]);

        $this->expectException(\RuntimeException::class);
        $pushinator->updateChannel('channel-123', 'Updated Channel');
    }

    // -------------------------------------------------------------------------
    // deleteChannel
    // -------------------------------------------------------------------------

    #[Test]
    public function testDeleteChannelRequest()
    {
        $container = [];
        $responseData = ['data' => $this->channelResponse()];
        $pushinator = $this->makeClient(
            [new Response(200, [], json_encode($responseData))],
            $container
        );

        $result = $pushinator->deleteChannel('channel-123');

        $request = $container[0]['request'];
        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertEquals('https://api.pushinator.com/api/v2/channels/channel-123', $request->getUri());
        $this->assertEquals('Bearer test-token', $request->getHeaderLine('Authorization'));
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertEquals('pushinator-php/1.0', $request->getHeaderLine('User-Agent'));
        $this->assertEquals($responseData, $result);
    }

    #[Test]
    public function testDeleteChannelNotFound()
    {
        $pushinator = $this->makeClient([new Response(404)]);

        $this->expectException(\RuntimeException::class);
        $pushinator->deleteChannel('channel-123');
    }
}
