<?php
declare(strict_types=1);

namespace FmLabs\Test\Uri;

use FmLabs\Uri\Uri;
use FmLabs\Uri\UriFactory;

class UriTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testConstructor(): void
    {
        $uri = new Uri();

        $this->assertEquals('', $uri->getScheme());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('', $uri->getHost());
        $this->assertEquals('', $uri->getPort());
        $this->assertEquals('', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('', $uri->getAuthority());
        $this->assertEquals('', $uri->getHostInfo());
        $this->assertEquals([], $uri->getQueryData());
        $this->assertEquals([
            'scheme' => '',
            'host' => '',
            'port' => '',
            'user' => '',
            'pass' => '',
            'path' => '',
            'query' => '',
            'fragment' => '',
        ], $uri->getComponents());
    }

    /**
     * @return void
     */
    public function testGetScheme(): void
    {
        $uri = UriFactory::fromString('http://www.example.org');
        $this->assertEquals('http', $uri->getScheme());
        $uri = UriFactory::fromString('https://www.example.org');
        $this->assertEquals('https', $uri->getScheme());
    }

    /**
     * @return void
     */
    public function testGetHost(): void
    {
        $uri = UriFactory::fromString('http://www.example.org');
        $this->assertEquals('www.example.org', $uri->getHost());
    }

    /**
     * @return void
     */
    public function testGetPort(): void
    {
        $uri = UriFactory::fromString('http://www.example.org');
        $this->assertEquals('', $uri->getPort());

        $uri = UriFactory::fromString('http://www.example.org:8080');
        $this->assertEquals('8080', $uri->getPort());
    }

    /**
     * @return void
     */
    public function testGetHostInfo(): void
    {
        $uri = UriFactory::fromString('http://www.example.org');
        $this->assertEquals('www.example.org', $uri->getHostInfo());

        $uri = UriFactory::fromString('http://www.example.org:80');
        $this->assertEquals('www.example.org:80', $uri->getHostInfo());

        $uri = UriFactory::fromString('http://www.example.org:8080');
        $this->assertEquals('www.example.org:8080', $uri->getHostInfo());
    }

    /**
     * @return void
     */
    public function testGetPath(): void
    {
        $uri = UriFactory::fromString('http://www.example.org');
        $this->assertEquals('', $uri->getPath());

        $uri = UriFactory::fromString('http://www.example.org/');
        $this->assertEquals('/', $uri->getPath());

        $uri = UriFactory::fromString('http://www.example.org/hello?test=1#test');
        $this->assertEquals('/hello', $uri->getPath());
    }

    /**
     * @return void
     */
    public function testGetQuery(): void
    {
        $uri = UriFactory::fromString('http://www.example.org');
        $this->assertEquals('', $uri->getQuery());

        $uri = UriFactory::fromString('http://www.example.org?');
        $this->assertEquals('', $uri->getQuery());

        $uri = UriFactory::fromString('http://www.example.org/hello?test=1#test');
        $this->assertEquals('test=1', $uri->getQuery());
    }

    /**
     * @return void
     */
    public function testGetFragment(): void
    {
        $uri = UriFactory::fromString('http://www.example.org/hello?test=1#frag');
        $this->assertEquals('frag', $uri->getFragment());
    }

    /**
     * @return void
     */
    public function testGetUser(): void
    {
        $uri = UriFactory::fromString('http://user@www.example.org');
        $this->assertEquals('user', $uri->getUser());

        $uri = UriFactory::fromString('http://user:pass@www.example.org');
        $this->assertEquals('user', $uri->getUser());
    }

    /**
     * @return void
     */
    public function testGetUserPass(): void
    {
        $uri = UriFactory::fromString('http://user@www.example.org');
        $this->assertEquals('', $uri->getUserPass());

        $uri = UriFactory::fromString('http://user:pass@www.example.org');
        $this->assertEquals('pass', $uri->getUserPass());
    }

    /**
     * @return void
     */
    public function testGetUserInfo(): void
    {
        $uri = UriFactory::fromString('http://user@www.example.org');
        $this->assertEquals('user', $uri->getUserInfo());

        $uri = UriFactory::fromString('http://user:pass@www.example.org');
        $this->assertEquals('user:pass', $uri->getUserInfo());
    }

    /**
     * @return void
     */
    public function testGetAuthority(): void
    {
        $uri = UriFactory::fromString('http://user@www.example.org:123');
        $this->assertEquals('user@www.example.org:123', $uri->getAuthority());

        $uri = UriFactory::fromString('http://user:pass@www.example.org');
        $this->assertEquals('user:pass@www.example.org', $uri->getAuthority());

        $uri = UriFactory::fromString('tel:+123456789');
        $this->assertEquals('', $uri->getAuthority());
    }

    /**
     * @return void
     */
    public function testGetQueryData(): void
    {
        $url = '/?';
        $uri = UriFactory::fromString($url);
        $this->assertEquals([], $uri->getQueryData());

        $url = '/?tag=networking&order=newest';
        $uri = UriFactory::fromString($url);
        $this->assertEquals(['tag' => 'networking', 'order' => 'newest'], $uri->getQueryData());
    }

    /**
     * @return void
     */
    public function testUriHttps(): void
    {
        $url = 'https://john.doe@www.example.com:123/forum/questions/?tag=networking&order=newest#top';
        $uri = UriFactory::fromString($url);
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('john.doe', $uri->getUser());
        $this->assertEquals('www.example.com', $uri->getHost());
        $this->assertEquals('www.example.com:123', $uri->getHostInfo());
        $this->assertEquals('john.doe@www.example.com:123', $uri->getAuthority());
        $this->assertEquals('/forum/questions/', $uri->getPath());
        $this->assertEquals('tag=networking&order=newest', $uri->getQuery());
        $this->assertEquals(['tag' => 'networking', 'order' => 'newest'], $uri->getQueryData());
        $this->assertEquals('top', $uri->getFragment());
        $this->assertEquals($url, (string)$uri);
    }

    /**
     * @return void
     */
    public function testUriMailto(): void
    {
        $url = 'mailto:John.Doe@example.com';
        $uri = UriFactory::fromString($url);
        $this->assertEquals('mailto', $uri->getScheme());
        $this->assertEquals('John.Doe@example.com', $uri->getPath());
        $this->assertEquals($url, (string)$uri);
    }

    /**
     * @return void
     */
    public function testUriTel(): void
    {
        $url = 'tel:+1-816-555-1212';
        $uri = UriFactory::fromString($url);
        $this->assertEquals('tel', $uri->getScheme());
        $this->assertEquals('+1-816-555-1212', $uri->getPath());
        $this->assertEquals($url, (string)$uri);
    }

    /**
     * @return void
     */
    public function testUriTelnet(): void
    {
        $url = 'telnet://192.0.2.16:80/';
        $uri = UriFactory::fromString($url);
        $this->assertEquals('telnet', $uri->getScheme());
        $this->assertEquals('192.0.2.16:80', $uri->getAuthority());
        $this->assertEquals('/', $uri->getPath());
        $this->assertEquals($url, (string)$uri);
    }

    /**
     * @return void
     */
    public function testUriLdap(): void
    {
        $url = 'ldap://[2001:db8::7]/c=GB?objectClass?one';
        $uri = UriFactory::fromString($url);
        $this->assertEquals('ldap', $uri->getScheme());
        $this->assertEquals('[2001:db8::7]', $uri->getAuthority());
        $this->assertEquals('/c=GB', $uri->getPath());
        $this->assertEquals('objectClass?one', $uri->getQuery());
        $this->assertEquals($url, (string)$uri);
    }

    /**
     * @return void
     */
    public function testUriUrn(): void
    {
        $urn = 'urn:oasis:names:specification:docbook:dtd:xml:4.1.2';
        $uri = UriFactory::fromString($urn);
        $this->assertEquals('urn', $uri->getScheme());
        $this->assertEquals('oasis:names:specification:docbook:dtd:xml:4.1.2', $uri->getPath());
        $this->assertEquals($urn, (string)$uri);
    }

    /**
     * @return void
     */
    public function testMagicGetter(): void
    {
        $url = 'https://john.doe:secret@www.example.com:123/forum/questions/?tag=networking&order=newest#top';
        $uri = UriFactory::fromString($url);
        $this->assertEquals($uri->getScheme(), $uri->scheme);
        $this->assertEquals($uri->getUser(), $uri->user);
        $this->assertEquals($uri->getUserPass(), $uri->pass);
        $this->assertEquals($uri->getUserInfo(), $uri->userinfo);
        $this->assertEquals($uri->getHost(), $uri->host);
        $this->assertEquals($uri->getHostInfo(), $uri->hostinfo);
        $this->assertEquals($uri->getAuthority(), $uri->authority);
        $this->assertEquals($uri->getPath(), $uri->path);
        $this->assertEquals($uri->getQuery(), $uri->query);
        $this->assertEquals($uri->getQueryData(), $uri->querydata);
        $this->assertEquals($uri->getFragment(), $uri->fragment);
        //$this->assertEquals((string)$uri, $uri->url);
    }

    /**
     * @return void
     */
    public function testOffsetGet(): void
    {
        $url = 'https://john.doe:secret@www.example.com:123/forum/questions/?tag=networking&order=newest#top';
        $uri = UriFactory::fromString($url);
        $this->assertEquals($uri->getScheme(), $uri['scheme']);
        $this->assertEquals($uri->getUser(), $uri['user']);
        $this->assertEquals($uri->getUserPass(), $uri['pass']);
        $this->assertEquals($uri->getUserInfo(), $uri['userinfo']);
        $this->assertEquals($uri->getHost(), $uri['host']);
        $this->assertEquals($uri->getHostInfo(), $uri['hostinfo']);
        $this->assertEquals($uri->getAuthority(), $uri['authority']);
        $this->assertEquals($uri->getPath(), $uri['path']);
        $this->assertEquals($uri->getQuery(), $uri['query']);
        $this->assertEquals($uri->getQueryData(), $uri['querydata']);
        $this->assertEquals($uri->getFragment(), $uri['fragment']);
        //$this->assertEquals((string)$uri, $uri['url']);
    }

    /**
     * @return void
     */
    public function testOffsetExists(): void
    {
        $url = 'https://john.doe:secret@www.example.com:123/forum/questions/?tag=networking&order=newest#top';
        $uri = UriFactory::fromString($url);
        $this->assertTrue(isset($uri['scheme']));
        $this->assertTrue(isset($uri['user']));
        $this->assertTrue(isset($uri['pass']));
        $this->assertTrue(isset($uri['userinfo']));
        $this->assertTrue(isset($uri['host']));
        $this->assertTrue(isset($uri['hostinfo']));
        $this->assertTrue(isset($uri['authority']));
        $this->assertTrue(isset($uri['path']));
        $this->assertTrue(isset($uri['query']));
        $this->assertTrue(isset($uri['querydata']));
        $this->assertTrue(isset($uri['fragment']));
        //$this->assertTrue(isset($uri['url']));
    }

    /**
     * @return void
     */
    public function testWithScheme(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');

        $uri2 = $uri->withScheme('http');
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('http', $uri2->getScheme());

        $uri2 = $uri->withScheme('');
        $this->assertIsString('', $uri2->getScheme());
        $this->assertEquals('', $uri2->getScheme());

        $uri2 = $uri->withScheme(null);
        $this->assertIsString('', $uri2->getScheme());
        $this->assertEquals('', $uri2->getScheme());
    }

    /**
     * @return void
     */
    public function testWithUserInfo(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');

        $uri2 = $uri->withUserInfo('admin', 'somepass');
        $this->assertEquals('user:secret', $uri->getUserInfo());
        $this->assertEquals('admin:somepass', $uri2->getUserInfo());

        $uri2 = $uri->withUserInfo('');
        $this->assertIsString('', $uri2->getUserInfo());
        $this->assertEquals('', $uri2->getUserInfo());

        $uri2 = $uri->withUserInfo(null);
        $this->assertIsString('', $uri2->getUserInfo());
        $this->assertEquals('', $uri2->getUserInfo());
    }

    /**
     * @return void
     */
    public function testWithHost(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');

        $uri2 = $uri->withHost('example.net');
        $this->assertEquals('www.example.org', $uri->getHost());
        $this->assertEquals('example.net', $uri2->getHost());

        $uri2 = $uri->withHost('');
        $this->assertIsString('', $uri2->getHost());
        $this->assertEquals('', $uri2->getHost());

        $uri2 = $uri->withHost(null);
        $this->assertIsString('', $uri2->getHost());
        $this->assertEquals('', $uri2->getHost());
    }

    /**
     * @return void
     */
    public function testWithPort(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');

        $uri2 = $uri->withPort(8080);
        $this->assertNull($uri->getPort());
        $this->assertEquals('8080', $uri2->getPort());

        // with default ports removed
        //$uri2 = $uri->withPort(80);
        //$this->assertEquals(80, $uri->getPort());
        //$this->assertNull($uri2->getPort());

        //$uri2 = $uri->withPort('');
        //$this->assertNull($uri2->getPort());

        $uri2 = $uri->withPort(null);
        $this->assertNull($uri2->getPort());
    }

    /**
     * @return void
     */
    public function testWithInvalidPort(): void
    {
        $uri = UriFactory::create();

        $this->expectException(\InvalidArgumentException::class);
        $uri->withPort(0);
    }

    /**
     * @return void
     */
    public function testWithPath(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');

        $uri2 = $uri->withPath('/foo/bar');
        $this->assertEquals('/my/path', $uri->getPath());
        $this->assertEquals('/foo/bar', $uri2->getPath());

        $uri2 = $uri->withPath('/');
        $this->assertEquals('/', $uri2->getPath());

        $uri2 = $uri->withPath('');
        $this->assertEquals('', $uri2->getPath());

        $uri2 = $uri->withPath(null);
        $this->assertIsString('', $uri2->getPath());
        $this->assertEquals('', $uri2->getPath());
    }

    /**
     * @return void
     */
    public function testWithQuery(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');

        $uri2 = $uri->withQuery('foo=bar&a=b');
        $this->assertEquals('some=query', $uri->getQuery());
        $this->assertEquals(['some' => 'query'], $uri->getQueryData());

        $this->assertEquals('foo=bar&a=b', $uri2->getQuery());
        $this->assertEquals(['foo' => 'bar', 'a' => 'b'], $uri2->getQueryData());
    }

    /**
     * @return void
     */
    public function testWithFragment(): void
    {
        $uri = UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');

        $uri2 = $uri->withFragment('other');
        $this->assertEquals('frag', $uri->getFragment());
        $this->assertEquals('other', $uri2->getFragment());
    }

    /**
     * @return void
     */
    public function testOffsetSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $uri = UriFactory::fromString('https://example.org');
        $uri['scheme'] = 'https';
    }

    /**
     * @return void
     */
    public function testOffsetUnset(): void
    {
        $this->expectException(\RuntimeException::class);
        $uri = UriFactory::fromString('https://example.org');
        unset($uri['scheme']);
    }
}
