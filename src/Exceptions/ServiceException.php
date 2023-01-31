<?php

namespace OnrampLab\ApiClient\Exceptions;

use Psr\Http\Message\ResponseInterface;

class ServiceException extends ApiClientException
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(string $message, int $code = 500, ResponseInterface $response)
    {
        parent::__construct($message, $code);

        $this->response = $response;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
