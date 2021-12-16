<?php

namespace OnrampLab\ApiClient\Api;

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;

class Client implements ClientInterface
{
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $apiToken;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var array
     */
    protected array $resources = [];

    public function __construct(array $config)
    {
        $this->setHttpClient(new HttpClient());
        $this->setBaseUrl($config['baseUrl']);
        $this->setApiToken($config['apiToken']);
        $this->setDebug($config['debug'] ?? false);
    }

    public function setHttpClient(HttpClient $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    public function setApiToken(string $apiToken): void
    {
        $this->apiToken = $apiToken;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    public function getEndPoint(string $resource = ''): string
    {
        return "{$this->baseUrl}/$resource";
    }

    public function request(string $method, string $url, array $params = [], array $data = []): ResponseInterface
    {
        $payload = $this->applyMiddlewares([
            'query' => $params,
            'json' => (object) $data,
            'headers' => [],
        ]);

        return $this->httpClient->request($method, $url, $payload);
    }

    /**
     * Default auth to put token into header.
     * You can customize your own auth
     */
    public function applyAuth(array $payload): array
    {
        $payload['query']['token'] = $this->apiToken;

        return $payload;
    }

    public function applyMiddleware(array $payload): array
    {
        return $payload;
    }

    public function registerResource(string $key, object $instance): void
    {
        $this->resources[$key] = $instance;
    }

    public function __get(string $key): object
    {
        return $this->resources[$key];
    }

    protected function applyMiddlewares(array $payload): array
    {
        $payload = $this->applyDebug($payload);
        $payload = $this->applyAuth($payload);
        $payload = $this->applyMiddleware($payload);

        return $payload;
    }

    protected function applyDebug(array $payload): array
    {
        $payload['debug'] = $this->debug;

        return $payload;
    }
}
