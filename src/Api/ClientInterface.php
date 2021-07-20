<?php

namespace OnrampLab\ApiClient\Api;

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * Create a new ClientInterface
     *
     * @param  array  $config
     * @return ClientInterface
     */
    public static function create(array $config): ClientInterface;

    public function setHttpClient(HttpClient $httpClient): void;

    public function setBaseUrl(string $baseUrl): void;

    public function setApiToken(string $apiToken): void;

    public function setDebug(bool $debug): void;

    public function getEndPoint(string $resource = ''): string;

    public function request(string $method, string $url, array $params = [], array $data = []): ResponseInterface;

    public function applyAuth(array $payload): array;

    public function applyMiddleware(array $payload): array;

    public function registerResource(string $key, object $instance): void;

    public function __get(string $key): object;
}
