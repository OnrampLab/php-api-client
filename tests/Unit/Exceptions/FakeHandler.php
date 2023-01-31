<?php

namespace OnrampLab\ApiClient\Tests\Unit\Exceptions;

use Exception;
use OnrampLab\ApiClient\Exceptions\Handler;

class FakeHandler extends Handler
{
    protected $exceptions = [
        'Something wrong' => FakeException::class,
    ];

    public function wrap(Exception $e): Exception
    {
        return parent::wrap($e);
    }
}
