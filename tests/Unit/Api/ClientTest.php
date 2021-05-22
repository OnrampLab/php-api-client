<?php

namespace OnrampLab\ApiClient\Tests\Unit\Api;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use OnrampLab\ApiClient\Api\Client;
use PHPUnit\Framework\TestCase;

/**
 * @group php-api-client
 */
class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Client::create([
            'apiToken' => 'fake_token',
            'baseUrl' => 'https://api.test.com/api',
        ]);
    }

    /**
     * @test
     */
    public function get_end_point_should_work()
    {
        $expectedUrl = 'https://api.test.com/api/test';
        $actualUrl = $this->client->getEndPoint('test');

        $this->assertEquals($expectedUrl, $actualUrl);
    }

    /**
     * @test
     */
    public function request_should_work()
    {
        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], 'Hello, World'),
        ]);
        $container = [];
        $history = Middleware::history($container);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $httpClient = new HttpClient(['handler' => $handlerStack]);
        $url = $this->client->getEndPoint('test');

        $this->client->setHttpClient($httpClient);

        $response = $this->client->request('POST', $url, ['greeting' => 'hi']);

        /** @var Request */
        $request = $container[0]['request'];

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals(['fake_token'], $request->getHeader('token'));
        $this->assertEquals('https://api.test.com/api/test?greeting=hi', (string) $request->getUri());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello, World', $response->getBody());
    }
}
