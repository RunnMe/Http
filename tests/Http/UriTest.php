<?php

namespace Runn\tests\Http\Uri;

use Runn\Http\Uri;

class UriTest extends \PHPUnit_Framework_TestCase
{

    public function testMalformed()
    {
        // @todo malformed URL
    }

    public function testScheme()
    {
        $uri = new Uri('test.local');
        $this->assertSame('', $uri->getScheme());
        $this->assertSame('', $uri->getProtocol());

        $uri = new Uri('https://test.local');
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('https', $uri->getProtocol());

        $uri = new Uri('FTP://test.local');
        $this->assertSame('ftp', $uri->getScheme());
        $this->assertSame('ftp', $uri->getProtocol());
    }

    public function testUserName()
    {
        $uri = new Uri('test.local');
        $this->assertSame('', $uri->getUserName());

        $uri = new Uri('http://foo@test.local');
        $this->assertSame('foo', $uri->getUserName());

        $uri = new Uri('http://foo:bAr123@test.local');
        $this->assertSame('foo', $uri->getUserName());
    }

    public function testPassword()
    {
        $uri = new Uri('test.local');
        $this->assertSame('', $uri->getPassword());

        $uri = new Uri('http://foo@test.local');
        $this->assertSame('', $uri->getPassword());

        $uri = new Uri('http://foo:bAr123@test.local');
        $this->assertSame('bAr123', $uri->getPassword());
    }

    public function testUserInfo()
    {
        $uri = new Uri('test.local');
        $this->assertSame('', $uri->getUserInfo());

        $uri = new Uri('http://foo@test.local');
        $this->assertSame('foo', $uri->getUserInfo());

        $uri = new Uri('http://foo:bAr123@test.local');
        $this->assertSame('foo:bAr123', $uri->getUserInfo());
    }

    public function testHost()
    {
        $uri = new Uri('/foo/bar');
        $this->assertSame('', $uri->getHost());

        $uri = new Uri('http://localhost');
        $this->assertSame('localhost', $uri->getHost());

        $uri = new Uri('http://tEsT.cOm/foo/bar');
        $this->assertSame('test.com', $uri->getHost());
    }

    public function testPort()
    {
        $uri = new Uri('/foo/bar');
        $this->assertNull($uri->getPort());

        $uri = new Uri('http://test.com/foo/bar');
        $this->assertNull($uri->getPort());

        $uri = new Uri('http://test.com:80/foo/bar');
        $this->assertNull($uri->getPort());

        $uri = new Uri('http://test.com:81/foo/bar');
        $this->assertSame(81, $uri->getPort());
    }

    public function testAuthority()
    {
        $uri = new Uri('/foo/bar');
        $this->assertSame('', $uri->getAuthority());

        $uri = new Uri('http://user@localhost');
        $this->assertSame('user@localhost', $uri->getAuthority());

        $uri = new Uri('http://user:pAsSwD@localhost');
        $this->assertSame('user:pAsSwD@localhost', $uri->getAuthority());

        $uri = new Uri('http://user:pAsSwD@localhost:81');
        $this->assertSame('user:pAsSwD@localhost:81', $uri->getAuthority());

        $uri = new Uri('http://localhost:80');
        $this->assertSame('localhost', $uri->getAuthority());
    }

    public function testPath()
    {
        $uri = new Uri('foo');
        $this->assertSame('foo', $uri->getPath());

        $uri = new Uri('/');
        $this->assertSame('/', $uri->getPath());

        $uri = new Uri('/foo');
        $this->assertSame('/foo', $uri->getPath());

        $uri = new Uri('//test.com');
        $this->assertSame('', $uri->getPath());

        $uri = new Uri('//test.com/');
        $this->assertSame('/', $uri->getPath());

        $uri = new Uri('//test.com/foo/bar');
        $this->assertSame('/foo/bar', $uri->getPath());
    }

}
