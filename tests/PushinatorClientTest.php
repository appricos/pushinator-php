<?php

namespace Tests\Pushinator;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Pushinator\PushinatorClient;
use GuzzleHttp\Middleware;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;


#[CoversClass(\Pushinator\PushinatorClient::class)]
class PushinatorClientTest extends TestCase
{

    #[Test]
    public function testSendNotificationSuccess()
    {
        $container = [];
        $history = Middleware::history($container);

        $mock = new MockHandler([new Response(200)]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        $client = new Client(['handler' => $handlerStack]);
        $pushinator = new PushinatorClient('test-token', $client);

        $pushinator->sendNotification('channel-123', 'Hello world');

        $request = $container[0]['request'];

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https://api.pushinator.com/api/v1/send_notification', $request->getUri());
        $this->assertEquals('Bearer test-token', $request->getHeaderLine('Authorization'));
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertEquals('pushinator-php/1.0', $request->getHeaderLine('User-Agent'));

        $this->assertEquals('{"channel":"channel-123","notification":"Hello world"}', $request->getBody()->getContents());
    }
    
    
    #[Test]
    public function testSendNotificationFailure()
    {
        $mock = new MockHandler([new Response(500)]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $pushinator = new PushinatorClient('test-token', $client);

        $this->expectException(\RuntimeException::class);
        $pushinator->sendNotification('channel-123', 'Hello world');
    }
    
    
    #[Test]
    public function testEmptyApiTokenThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PushinatorClient('');
    }
}