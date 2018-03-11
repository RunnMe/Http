<?php

namespace Runn\tests\Http\Uri;

use Runn\Http\Uri;

class UriTest extends \PHPUnit_Framework_TestCase
{

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

}
