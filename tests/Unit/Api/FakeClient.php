<?php

namespace OnrampLab\ApiClient\Tests\Unit\Api;

use OnrampLab\ApiClient\Api\Client;

class FakeClient extends Client
{
    public function applyMiddleware($payload): array
    {
        $payload['headers']['fake_header'] = 'fake_header_value';

        return $payload;
    }
}
