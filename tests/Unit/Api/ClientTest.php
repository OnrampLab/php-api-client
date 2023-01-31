<?php

namespace OnrampLab\ApiClient\Tests\Unit\Api;

use Mockery;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use OnrampLab\ApiClient\Api\Client;
use OnrampLab\ApiClient\Exceptions\HttpException;
use OnrampLab\ApiClient\Exceptions\ServiceException;
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
        $this->client = new Client([
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
        $this->assertEquals('https://api.test.com/api/test?greeting=hi&token=fake_token', (string) $request->getUri());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $request->getHeader('Accept')[0]);
        $this->assertEquals('Hello, World', $response->getBody());
    }

    /**
     * @test
     */
    public function request_should_throw_service_exception()
    {
        $httpClient = Mockery::mock(HttpClient::class);
        $httpClient
            ->shouldReceive('request')
            ->once()
            ->andThrow(new ClientException(
                'Client Error: `GET /` resulted in a `404 Not Found` response',
                new Request('GET', '/'),
                new Response(404),
            ));

        $this->client->setHttpClient($httpClient);

        $url = $this->client->getEndPoint();

        try {
            $this->client->request('GET', $url);
        } catch (ServiceException $exception) {
            $this->assertEquals('Client Error: `GET /` resulted in a `404 Not Found` response', $exception->getMessage());
            $this->assertEquals(404, $exception->getCode());
            $this->assertNotNull($exception->getResponse());
        }
    }

    /**
     * @test
     */
    public function request_should_throw_http_exception()
    {
        $httpClient = Mockery::mock(HttpClient::class);
        $httpClient
            ->shouldReceive('request')
            ->once()
            ->andThrow(new ConnectException(
                'Connection refused',
                new Request('GET', '/'),
                null,
            ));

        $this->client->setHttpClient($httpClient);

        $url = $this->client->getEndPoint();

        try {
            $this->client->request('GET', $url);
        } catch (HttpException $exception) {
            $this->assertEquals('Connection refused', $exception->getMessage());
        }
    }

    /**
     * @test
     */
    public function registerResource_should_work()
    {
        $resourceKey = 'myResource';
        $resource = new \stdClass();

        $this->client->registerResource($resourceKey, $resource);

        $myResource = $this->client->myResource;

        $this->assertEquals($resource, $myResource);
    }

    /**
     * @test
     */
    public function applyMiddleware_should_work()
    {
        $client = new FakeClient([
            'apiToken' => 'fake_token',
            'baseUrl' => 'https://api.test.com/api',
        ]);
        $mock = new MockHandler([
            new Response(200),
        ]);
        $container = [];
        $history = Middleware::history($container);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $httpClient = new HttpClient(['handler' => $handlerStack]);
        $url = $client->getEndPoint('test');
        $client->setHttpClient($httpClient);

        $client->request('POST', $url, ['greeting' => 'hi']);

        /** @var Request */
        $request = $container[0]['request'];

        $this->assertEquals(['fake_header_value'], $request->getHeader('fake_header'));
    }
}
