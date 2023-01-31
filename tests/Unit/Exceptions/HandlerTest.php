<?php

namespace OnrampLab\ApiClient\Tests\Unit\Exceptions;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use OnrampLab\ApiClient\Exceptions\Handler;
use OnrampLab\ApiClient\Exceptions\ServiceException;

/**
 * @group php-api-client
 */
class HandlerTest extends TestCase
{
    /**
     * @var Handler
     */
    private $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new FakeHandler();
    }

    /**
     * @test
     */
    public function wrap_should_return_fake_exception(): void
    {
        $data = json_encode(['message' => 'Something wrong']);
        $response = new Response(500, [], $data);
        $originalException = new ServiceException('Server error', 500, $response);
        $wrappedException = $this->handler->wrap($originalException);

        $this->assertTrue($wrappedException instanceof FakeException);
    }

    /**
     * @test
     */
    public function wrap_should_return_service_exception(): void
    {
        $data = json_encode(['data' => 'unknown data']);
        $response = new Response(500, [], $data);
        $originalException = new ServiceException('Server error', 500, $response);
        $wrappedException = $this->handler->wrap($originalException);

        $this->assertTrue($wrappedException instanceof ServiceException);
    }
}
