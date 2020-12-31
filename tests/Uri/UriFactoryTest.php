<?php
declare(strict_types=1);

namespace FmLabs\Test\Uri;

use FmLabs\Uri\Uri;
use FmLabs\Uri\UriFactory;
use Psr\Http\Message\UriInterface;

class UriFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        UriFactory::setClassName(Uri::class);
    }

    /**
     * @return void
     */
    public function testStaticSetClassName(): void
    {
        $this->expectException(\RuntimeException::class);
        UriFactory::setClassName('\\Invalid\\Class\\Path');
    }

    /**
     * @return void
     */
    public function testStaticCreate(): void
    {
        $uri = UriFactory::create();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertInstanceOf(Uri::class, $uri);

        $this->expectException(\RuntimeException::class);
        UriFactory::setClassName(\stdClass::class);
        UriFactory::create();
    }

    /**
     * @return void
     */
    public function testStaticFromString(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org:1234/my/path?some=query&foo=bar#frag');
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('user:secret', $uri->getUserInfo());
        $this->assertEquals('www.example.org', $uri->getHost());
        $this->assertEquals('1234', $uri->getPort());
        $this->assertEquals('/my/path', $uri->getPath());
        $this->assertEquals('some=query&foo=bar', $uri->getQuery());
        $this->assertEquals('frag', $uri->getFragment());
    }

    /**
     * @return void
     */
    public function testStaticFromUri(): void
    {
        $tmp = UriFactory::fromString('https://user:secret@www.example.org:1234/my/path?some=query&foo=bar#frag');
        $uri = UriFactory::fromUri($tmp);
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertInstanceOf(Uri::class, $uri);
        $this->assertEquals($tmp->getScheme(), $uri->getScheme());
        $this->assertEquals($tmp->getUserInfo(), $uri->getUserInfo());
        $this->assertEquals($tmp->getHost(), $uri->getHost());
        $this->assertEquals($tmp->getPort(), $uri->getPort());
        $this->assertEquals($tmp->getPath(), $uri->getPath());
        $this->assertEquals($tmp->getQuery(), $uri->getQuery());
        $this->assertEquals($tmp->getFragment(), $uri->getFragment());
    }
}
