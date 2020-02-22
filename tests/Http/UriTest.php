<?php

namespace Runn\tests\Http\Uri;

use PHPUnit\Framework\TestCase;
use Runn\Core\Std;
use Runn\Http\Exceptions\InvalidUri;
use Runn\Http\Uri;

class UriTest extends TestCase
{

    public function testMalformed()
    {
        try {
            $uri = new Uri('///');
            $this->fail();
        } catch (\Throwable $e) {
            $this->assertSame(InvalidUri::class, get_class($e));
            $this->assertSame('///', $e->getUri());
            $this->assertSame('Invalid URI', $e->getMessage());
        }
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

    public function testWithHost()
    {
        $uri = new Uri('http://test.com/');
        $this->assertSame('test.com', $uri->getHost());

        $modified = $uri->withHost('localhost');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('localhost', $modified->getHost());

        $modified = $uri->withHost('eXaMpLe.Org');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('example.org', $modified->getHost());
    }

    public function testWithoutHost()
    {
        $uri = new Uri('http://test.com/');
        $this->assertSame('test.com', $uri->getHost());

        $modified = $uri->withHost('');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getHost());

        $modified = $uri->withHost();
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getHost());

        $modified = $uri->withoutHost();
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getHost());
    }

    public function testWithPort()
    {
        $uri = new Uri('http://test.com/');
        $this->assertNull($uri->getPort());

        $modified = $uri->withPort(80);
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertNull($modified->getPort());

        $modified = $uri->withPort(81);
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame(81, $modified->getPort());
    }

    public function testWithoutPort()
    {
        $uri = new Uri('http://test.com:81/');
        $this->assertSame(81, $uri->getPort());

        $modified = $uri->withPort(0);
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertNull($modified->getPort());

        $modified = $uri->withPort();
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertNull($modified->getPort());

        $modified = $uri->withoutPort();
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertNull($modified->getPort());
    }

    public function testWithPath()
    {
        $uri = new Uri('http://test.com/');
        $this->assertSame('/', $uri->getPath());

        $modified = $uri->withPath('/foo/bar');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('/foo/bar', $modified->getPath());
    }

    public function testWithQuery()
    {
        $uri = new Uri('http://test.com/?foo=bar');
        $this->assertSame('foo=bar', $uri->getQuery());
        $this->assertEquals(new Std(['foo' => 'bar']), $uri->getQueryParams());

        $modified = $uri->withQuery('foo=bar&baz=bla');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('foo=bar&baz=bla', $modified->getQuery());
        $this->assertEquals(new Std(['foo'=>'bar', 'baz'=>'bla']), $modified->getQueryParams());
    }

    public function testWithoutQuery()
    {
        $uri = new Uri('http://test.com/?foo=bar');
        $this->assertSame('foo=bar', $uri->getQuery());
        $this->assertEquals(new Std(['foo' => 'bar']), $uri->getQueryParams());

        $modified = $uri->withQuery('');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getQuery());
        $this->assertEquals(new Std([]), $modified->getQueryParams());

        $modified = $uri->withQuery();
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getQuery());
        $this->assertEquals(new Std([]), $modified->getQueryParams());

        $modified = $uri->withoutQuery();
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getQuery());
        $this->assertEquals(new Std([]), $modified->getQueryParams());
    }

    public function testWithQueryParam()
    {
        $uri = new Uri('http://test.com/?foo=bar');
        $this->assertEquals(new Std(['foo' => 'bar']), $uri->getQueryParams());
        $this->assertSame('foo=bar', $uri->getQuery());

        $modified = $uri->withQueryParam('foo', 42);
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertEquals(new Std(['foo' => 42]), $modified->getQueryParams());
        $this->assertSame('foo=42', $modified->getQuery());

        $modified = $uri->withQueryParam('baz', 'bla');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertEquals(new Std(['foo' => 'bar', 'baz' => 'bla']), $modified->getQueryParams());
        $this->assertSame('foo=bar&baz=bla', $modified->getQuery());
    }

    public function testWithoutQueryParam()
    {
        $uri = new Uri('http://test.com/?foo=bar');
        $this->assertEquals(new Std(['foo' => 'bar']), $uri->getQueryParams());
        $this->assertSame('foo=bar', $uri->getQuery());

        $modified = $uri->withQueryParam('foo', null);
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertEquals(new Std([]), $modified->getQueryParams());
        $this->assertSame('', $modified->getQuery());

        $modified = $uri->withQueryParam('foo');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertEquals(new Std([]), $modified->getQueryParams());
        $this->assertSame('', $modified->getQuery());
    }

    public function testWithFragment()
    {
        $uri = new Uri('http://test.com/#foo-bar');
        $this->assertSame('foo-bar', $uri->getFragment());

        $modified = $uri->withFragment('baz');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('baz', $modified->getFragment());
    }

    public function testWithoutFragment()
    {
        $uri = new Uri('http://test.com/#foo-bar');
        $this->assertSame('foo-bar', $uri->getFragment());

        $modified = $uri->withFragment('');
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getFragment());

        $modified = $uri->withFragment();
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getFragment());

        $modified = $uri->withoutFragment();
        $this->assertInstanceOf(Uri::class, $modified);
        $this->assertNotSame($modified, $uri);
        $this->assertSame('', $modified->getFragment());
    }

    public function testToString()
    {
        $uri = new Uri('//test.com');
        $this->assertSame('//test.com', (string)$uri);

        $uri = $uri->withScheme('https');
        $this->assertSame('https://test.com', (string)$uri);

        $modified = $uri->withUserName('user')->withPassword('password');
        $this->assertSame('https://user:password@test.com', (string)$modified);

        $modified = $uri->withPath('foo');
        $this->assertSame('https://test.com/foo', (string)$modified);

        $modified = $uri->withoutHost()->withPath('//foo');
        $this->assertSame('https:/foo', (string)$modified);

        $modified = $uri->withQuery('buz=something');
        $this->assertSame('https://test.com?buz=something', (string)$modified);

        $modified = $uri->withPath('/foo/bar')->withQuery('buz=something');
        $this->assertSame('https://test.com/foo/bar?buz=something', (string)$modified);

        $modified = $modified->withFragment('else');
        $this->assertSame('https://test.com/foo/bar?buz=something#else', (string)$modified);

        $modified = $modified->withUserName('user')->withPassword('password');
        $this->assertSame('https://user:password@test.com/foo/bar?buz=something#else', (string)$modified);
    }

}
