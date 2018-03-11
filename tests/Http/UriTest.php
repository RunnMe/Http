<?php

namespace Runn\tests\Http\Uri;

use Runn\Core\Std;
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

    public function testQuery()
    {
        $uri = new Uri('//test.com/foo/bar');
        $this->assertSame('', $uri->getQuery());

        $uri = new Uri('//test.com/?');
        $this->assertSame('', $uri->getQuery());

        $uri = new Uri('//test.com/?foo=bar');
        $this->assertSame('foo=bar', $uri->getQuery());

        $uri = new Uri('//test.com/?foo=bar&baz=bla');
        $this->assertSame('foo=bar&baz=bla', $uri->getQuery());

        $uri = new Uri('//test.com/?something#else');
        $this->assertSame('something', $uri->getQuery());
    }

    public function testQueryParams()
    {
        $uri = new Uri('//test.com/foo/bar');
        $this->assertEquals(new Std, $uri->getQueryParams());

        $uri = new Uri('//test.com/?');
        $this->assertEquals(new Std, $uri->getQueryParams());

        $uri = new Uri('//test.com/?foo=bar');
        $this->assertEquals(new Std(['foo' => 'bar']), $uri->getQueryParams());

        $uri = new Uri('//test.com/?foo=bar&baz=bla');
        $this->assertEquals(new Std(['foo' => 'bar', 'baz' => 'bla']), $uri->getQueryParams());

        $uri = new Uri('//test.com/?something#else');
        $this->assertEquals(new Std(['something' => '']), $uri->getQueryParams());
    }

    public function testFragment()
    {
        $uri = new Uri('//test.com/foo/bar');
        $this->assertSame('', $uri->getFragment());

        $uri = new Uri('//test.com/#');
        $this->assertSame('', $uri->getFragment());

        $uri = new Uri('//test.com/?something#else');
        $this->assertSame('else', $uri->getFragment());
    }

    public function testWithScheme()
    {
        $uri = new Uri('http://test.com/');
        $this->assertSame('http', $uri->getScheme());

        $modified = $uri->withScheme('https');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('https', $modified->getScheme());
        $this->assertSame('https', $modified->getProtocol());

        $modified = $uri->withScheme('FTP');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('ftp', $modified->getScheme());
        $this->assertSame('ftp', $modified->getProtocol());

        $modified = $uri->withScheme();
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getScheme());
        $this->assertSame('', $modified->getProtocol());
    }

    public function testWithProtocol()
    {
        $uri = new Uri('http://test.com/');
        $this->assertSame('http', $uri->getScheme());

        $modified = $uri->withProtocol('https');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('https', $modified->getProtocol());
        $this->assertSame('https', $modified->getScheme());

        $modified = $uri->withProtocol('FTP');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('ftp', $modified->getProtocol());
        $this->assertSame('ftp', $modified->getScheme());

        $modified = $uri->withProtocol();
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getProtocol());
        $this->assertSame('', $modified->getScheme());
    }

    public function testWithUserName()
    {
        $uri = new Uri('http://test.com/');
        $this->assertSame('', $uri->getUserName());
        $this->assertSame('', $uri->getUserInfo());

        $modified = $uri->withUserName('foo');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('foo', $modified->getUserName());
        $this->assertSame('foo', $modified->getUserInfo());
    }

    public function testWithAndWithoutPassword()
    {
        $uri = new Uri('http://foo:BaR123@test.com/');
        $this->assertSame('BaR123', $uri->getPassword());
        $this->assertSame('foo:BaR123', $uri->getUserInfo());

        $modified = $uri->withPassword('123bAr');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('123bAr', $modified->getPassword());
        $this->assertSame('foo:123bAr', $modified->getUserInfo());

        $modified = $uri->withPassword('');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getPassword());
        $this->assertSame('foo', $modified->getUserInfo());

        $modified = $uri->withPassword();
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getPassword());
        $this->assertSame('foo', $modified->getUserInfo());

        $modified = $uri->withoutPassword();
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getPassword());
        $this->assertSame('foo', $modified->getUserInfo());
    }

    public function testWithUserInfo()
    {
        $uri = new Uri('http://test.com/');
        $this->assertSame('', $uri->getUserInfo());

        $modified = $uri->withUserInfo('foo');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('foo', $modified->getUserInfo());
        $this->assertSame('foo', $modified->getUserName());
        $this->assertSame('', $modified->getPassword());

        $modified = $uri->withUserInfo('foo', 'bAr123');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('foo:bAr123', $modified->getUserInfo());
        $this->assertSame('foo', $modified->getUserName());
        $this->assertSame('bAr123', $modified->getPassword());
    }

    public function testWithoutUserInfo()
    {
        $uri = new Uri('http://foo:bar@test.com/');
        $this->assertSame('foo:bar', $uri->getUserInfo());

        $modified = $uri->withUserInfo('');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getUserInfo());
        $this->assertSame('', $modified->getUserName());
        $this->assertSame('', $modified->getPassword());

        $modified = $uri->withoutUserInfo();
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getUserInfo());
        $this->assertSame('', $modified->getUserName());
        $this->assertSame('', $modified->getPassword());
    }

}
