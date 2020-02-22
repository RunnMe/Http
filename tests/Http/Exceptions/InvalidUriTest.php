<?php

namespace Runn\tests\Http\Exceptions\InvalidUri;

use PHPUnit\Framework\TestCase;
use Runn\Http\Exception;
use Runn\Http\Exceptions\InvalidUri;

class InvalidUriTest extends TestCase
{

    public function testUri()
    {
        $uri = 'foo';
        $exception = new InvalidUri($uri);

        $this->assertInstanceOf(\Throwable::class, $exception);
        $this->assertInstanceOf(Exception::class, $exception);

        $this->assertSame($uri, $exception->getUri());
    }

}
