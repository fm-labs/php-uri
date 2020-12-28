<?php
declare(strict_types=1);

namespace FmLabs\Test\Uri;

use FmLabs\Uri\UriFactory;

class UriFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testWithScheme(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');
        $uri = $uri->withScheme('http');
        $this->assertEquals('http', $uri->getScheme());
    }

    public function testWithUserInfo(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');
        $uri = $uri->withUserInfo('admin', 'somepass');
        $this->assertEquals('admin:somepass', $uri->getUserInfo());
    }

    public function testWithHost(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');
        $uri = $uri->withHost('example.net');
        $this->assertEquals('example.net', $uri->getHost());
    }

    public function testWithPort(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');
        $uri = $uri->withPort(8080);
        $this->assertEquals('8080', $uri->getPort());
    }

    public function testWithPath(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');
        $uri = $uri->withPath('/foo/bar');
        $this->assertEquals('/foo/bar', $uri->getPath());
    }

    public function testWithQuery(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');
        $uri = $uri->withQuery('foo=bar&a=b');
        $this->assertEquals('foo=bar&a=b', $uri->getQuery());
    }

    public function testWithFragment(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');
        $uri = $uri->withFragment('other');
        $this->assertEquals('other', $uri->getFragment());
    }

    public function testOffsetUnset(): void
    {
        $this->markTestSkipped();
    }

    public function testOffsetSet(): void
    {
        $this->markTestSkipped();
    }
}
