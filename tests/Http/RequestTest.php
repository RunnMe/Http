<?php

namespace Runn\tests\Http\Uri;

use PHPUnit\Framework\TestCase;
use Runn\Http\Request;
use Runn\Http\Uri;

class RequestTest extends TestCase
{

    public function testRouteParams()
    {
        $request = new Request();
        $this->assertSame([], $request->getRouteParams());
        $this->assertNull($request->getRouteParam('foo'));

        $request->addRouteParam('foo', 'bar');
        $this->assertSame(['foo' => 'bar'], $request->getRouteParams());
        $this->assertSame('bar', $request->getRouteParam('foo'));
    }

    public function testConstructFromGlobals()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'test.ltd';
        $_SERVER['REQUEST_URI'] = '/foo/bar?baz=42';
        $_SERVER['HTTP_USER_AGENT'] = 'PHP';
        $_COOKIE['foo'] = 'bar';

        $request = Request::constructFromGlobals();

        $this->assertSame([], $request->getRouteParams());
        $this->assertSame(['baz' => '42'], $request->getServerParams());
        $this->assertSame(['foo' => 'bar'], $request->getCookieParams());
        $this->assertEquals(new Uri('https://test.ltd/foo/bar?baz=42'), $request->getUri());
    }

}
