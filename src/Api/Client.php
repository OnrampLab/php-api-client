<?php

namespace OnrampLab\ApiClient\Api;

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;

class Client
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
    public array $resources = [];

    public static function create(array $config): Client
    {
        $client = new Client();
        $client->setHttpClient(new HttpClient());
        $client->setBaseUrl($config['baseUrl']);
        $client->setApiToken($config['apiToken']);
        $client->setDebug($config['debug'] ?? false);

        return $client;
    }

    /**
     * This function will take an instance of a PHP stdClass and attempt to cast it to
     * the type of the specified $className.
     *
     * For example, we may pass 'Onramplab\Dummy' as the $className.
     *
     * @param string $className the name of the sub class type to which we want to create
     *
     * @return object a version of the incoming $instance casted as the specified className
     */
    protected function cast(string $className): object
    {

        $instance = new $className();

        $instance->setHttpClient($this->httpClient);
        $instance->setApiToken($this->apiToken);
        $instance->setBaseUrl($this->baseUrl);
        $instance->setDebug($this->debug);

        return $instance;
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
        ]);

        return $this->httpClient->request($method, $url, $payload);
    }

    /**
     * Default auth to put token into header.
     * You can customize your own auth
     */
    public function applyAuth(array $payload): array
    {
        $headers = [
            'headers' => [
                'token' => $this->apiToken,
            ]
        ];

        return array_merge($payload, $headers);
    }

    public function registerResource(string $key, object $instance): void
    {
        $this->resources[$key] = $instance;
    }

    public function __get(string $key): object
    {
        return $this->resources[$key];
    }

    private function applyMiddlewares(array $payload): array
    {
        $payload = $this->applyDebug($payload);

        return $this->applyAuth($payload);
    }

    private function applyDebug(array $payload): array
    {
        $options = [
            'debug' => $this->debug,
        ];

        return array_merge($payload, $options);
    }
}
