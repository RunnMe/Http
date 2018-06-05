<?php

namespace Runn\tests\Http;

use PHPUnit\Framework\TestCase;
use Runn\Http\Request;
use Runn\Http\Uri;

class RequestTest extends TestCase
{
    public function testCreateEmptyRequest()
    {
        $request = Request::constructFromGlobals([]);
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('1.1', $request->getProtocolVersion());
        $this->assertEmpty($request->getBody()->getContents());
        $this->assertEmpty($request->getHeaders());
    }

    public function testCreateUri()
    {
        $request = Request::constructFromGlobals([
            'HTTPS' => true,
            'HTTP_HOST' => 'example.net:443',
            'REQUEST_URI' => '/page?id=42',
            'QUERY_STRING' => 'id=42',
        ]);
        $this->assertEquals(new Uri('https://example.net:443/page?id=42'), $request->getUri());

        $request = Request::constructFromGlobals([
            'HTTP_HOST' => 'example.net',
            'REQUEST_URI' => '/page#print',
        ]);
        $this->assertEquals(new Uri('http://example.net/page#print'), $request->getUri());

        /* ISS URI */
        $request = Request::constructFromGlobals([
            'HTTP_HOST' => 'example.net',
            'IIS_WasUrlRewritten' => '1',
            'UNENCODED_URL' => '/page',
        ]);
        $this->assertEquals(new Uri('http://example.net/page'), $request->getUri());

        $request = Request::constructFromGlobals([
            'HTTP_HOST' => 'example.net',
            'HTTP_X_REWRITE_URL' => '/page',
        ]);
        $this->assertEquals(new Uri('http://example.net/page'), $request->getUri());

        $request = Request::constructFromGlobals([
            'HTTP_HOST' => 'example.net',
            'HTTP_X_REWRITE_URL' => '/page',
            'HTTP_X_ORIGINAL_URL' => '/real_page',
        ]);
        $this->assertEquals(new Uri('http://example.net/real_page'), $request->getUri());

        /* ORIG_PATH_INFO */
        $request = Request::constructFromGlobals([
            'HTTP_HOST' => 'example.net',
            'ORIG_PATH_INFO' => '/real_page',
        ]);
        $this->assertEquals(new Uri('http://example.net/real_page'), $request->getUri());
    }

    public function testProtocolVersion()
    {
        $request = Request::constructFromGlobals(['SERVER_PROTOCOL' => 'HTTP/1.0']);
        $this->assertSame('1.0', $request->getProtocolVersion());
    }

    /**
     * @expectedException \Runn\Http\Exceptions\UnexpectedValueException
     */
    public function testUnsupportedProtocolVersion()
    {
        Request::constructFromGlobals(['SERVER_PROTOCOL' => 'HTTP/0.9']);
    }
}
