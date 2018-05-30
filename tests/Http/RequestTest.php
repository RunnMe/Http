<?php

namespace Runn\tests\Http\Uri;

use PHPUnit\Framework\TestCase;
use Runn\Http\Request;

class RequestTest extends TestCase
{
    public function testCreateEmptyRequest()
    {
        Request::constructFromGlobals();
        $this->assertTrue(true);
    }
}
