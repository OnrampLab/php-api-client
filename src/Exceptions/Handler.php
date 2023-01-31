<?php

namespace OnrampLab\ApiClient\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;

class Handler
{
    /**
     * @var array
     */
    protected $exceptions = [];

    public function wrap(Exception $exception): Exception
    {
        if ($exception instanceof ServiceException) {
            $response = $exception->getResponse();

            if (empty($response)) {
                return $exception;
            }

            $error = $this->transformError($response);

            if (empty($error)) {
                return $exception;
            }

            $exceptionClass = self::findExceptionClass($error);

            if ($exceptionClass) {
                return new $exceptionClass($error, $exception->getResponse()->getStatusCode(), $exception->getResponse());
            }
        }

        // unknown exception
        return $exception;
    }

    private function findExceptionClass(string $type): ?string
    {
        return isset($this->exceptions[$type]) ? $this->exceptions[$type] : null;
    }

    private function transformError(ResponseInterface $response): ?string
    {
        try {
            $data = json_decode($response->getBody()->getContents());
            return $data->error ?? $data->message;
        } catch (Exception $e) {
            // the API is responding unknown data
            return null;
        }
    }
}
