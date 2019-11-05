<?php
namespace FmLabs\Test\Uri;

use FmLabs\Uri\Uri;

class UriTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testConstructor()
    {
        $url = 'http://www.example.org';
        $uri = new Uri($url);

        $this->assertEquals($url, $uri->toString());
    }

    public function testGetScheme()
    {
        $uri = new Uri('http://www.example.org');
        $this->assertEquals('http', $uri->getScheme());
        $uri = new Uri('https://www.example.org');
        $this->assertEquals('https', $uri->getScheme());
    }

    public function testGetHost()
    {
        $uri = new Uri('http://www.example.org');
        $this->assertEquals('www.example.org', $uri->getHost());
    }

    public function testGetPort()
    {
        $uri = new Uri('http://www.example.org');
        $this->assertEquals('', $uri->getPort());

        $uri = new Uri('http://www.example.org:8080');
        $this->assertEquals('8080', $uri->getPort());
    }

    public function testGetHostInfo()
    {
        $uri = new Uri('http://www.example.org');
        $this->assertEquals('www.example.org', $uri->getHostInfo());

        $uri = new Uri('http://www.example.org:8080');
        $this->assertEquals('www.example.org:8080', $uri->getHostInfo());
    }

    public function testGetPath()
    {
        $uri = new Uri('http://www.example.org');
        $this->assertEquals('', $uri->getPath());

        $uri = new Uri('http://www.example.org/');
        $this->assertEquals('/', $uri->getPath());

        $uri = new Uri('http://www.example.org/hello?test=1#test');
        $this->assertEquals('/hello', $uri->getPath());
    }

    public function testGetQuery()
    {
        $uri = new Uri('http://www.example.org');
        $this->assertEquals('', $uri->getQuery());

        $uri = new Uri('http://www.example.org?');
        $this->assertEquals('', $uri->getQuery());

        $uri = new Uri('http://www.example.org/hello?test=1#test');
        $this->assertEquals('test=1', $uri->getQuery());
    }

    public function testGetFragment()
    {
        $uri = new Uri('http://www.example.org/hello?test=1#frag');
        $this->assertEquals('frag', $uri->getFragment());
    }

    public function testGetUser()
    {
        $uri = new Uri('http://user@www.example.org');
        $this->assertEquals('user', $uri->getUser());

        $uri = new Uri('http://user:pass@www.example.org');
        $this->assertEquals('user', $uri->getUser());
    }

    public function testGetUserPass()
    {
        $uri = new Uri('http://user@www.example.org');
        $this->assertEquals('', $uri->getUserPass());

        $uri = new Uri('http://user:pass@www.example.org');
        $this->assertEquals('pass', $uri->getUserPass());
    }

    public function testGetUserInfo()
    {
        $uri = new Uri('http://user@www.example.org');
        $this->assertEquals('user', $uri->getUserInfo());

        $uri = new Uri('http://user:pass@www.example.org');
        $this->assertEquals('user:pass', $uri->getUserInfo());
    }

    public function testGetAuthority()
    {
        $uri = new Uri('http://user@www.example.org:123');
        $this->assertEquals('user@www.example.org:123', $uri->getAuthority());

        $uri = new Uri('http://user:pass@www.example.org');
        $this->assertEquals('user:pass@www.example.org', $uri->getAuthority());

        $uri = new Uri('tel:+123456789');
        $this->assertEquals(null, $uri->getAuthority());
    }

    public function testGetQueryData()
    {
        //$this->skipIf(true, 'Implement me');
    }

    public function testUriHttps()
    {
        $url = "https://john.doe@www.example.com:123/forum/questions/?tag=networking&order=newest#top";
        $uri = new Uri($url);
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('john.doe', $uri->getUser());
        $this->assertEquals('www.example.com', $uri->getHost());
        $this->assertEquals('www.example.com:123', $uri->getHostInfo());
        $this->assertEquals('john.doe@www.example.com:123', $uri->getAuthority());
        $this->assertEquals('/forum/questions/', $uri->getPath());
        $this->assertEquals('tag=networking&order=newest', $uri->getQuery());
        $this->assertEquals(['tag' => 'networking', 'order' => 'newest'], $uri->getQueryData());
        $this->assertEquals('top', $uri->getFragment());
        $this->assertEquals($url, $uri->toString());
    }

    public function testUriMailto()
    {
        $url = "mailto:John.Doe@example.com";
        $uri = new Uri($url);
        $this->assertEquals('mailto', $uri->getScheme());
        $this->assertEquals('John.Doe@example.com', $uri->getPath());
        $this->assertEquals($url, $uri->toString());
    }

    public function testUriTel()
    {
        $url = "tel:+1-816-555-1212";
        $uri = new Uri($url);
        $this->assertEquals('tel', $uri->getScheme());
        $this->assertEquals('+1-816-555-1212', $uri->getPath());
        $this->assertEquals($url, $uri->toString());
    }

    public function testUriTelnet()
    {
        $url = "telnet://192.0.2.16:80/";
        $uri = new Uri($url);
        $this->assertEquals('telnet', $uri->getScheme());
        $this->assertEquals('192.0.2.16:80', $uri->getAuthority());
        $this->assertEquals('/', $uri->getPath());
        $this->assertEquals($url, $uri->toString());
    }

    public function testUriLdap()
    {
        $url = "ldap://[2001:db8::7]/c=GB?objectClass?one";
        $uri = new Uri($url);
        $this->assertEquals('ldap', $uri->getScheme());
        $this->assertEquals('[2001:db8::7]', $uri->getAuthority());
        $this->assertEquals('/c=GB', $uri->getPath());
        $this->assertEquals('objectClass?one', $uri->getQuery());
        $this->assertEquals($url, $uri->toString());
    }

    public function testUriUrn()
    {
        $urn = "urn:oasis:names:specification:docbook:dtd:xml:4.1.2";
        $uri = new Uri($urn);
        $this->assertEquals('urn', $uri->getScheme());
        $this->assertEquals('oasis:names:specification:docbook:dtd:xml:4.1.2', $uri->getPath());
        $this->assertEquals($urn, $uri->toString());
    }

    public function testMagicGetter()
    {
        $url = "https://john.doe:secret@www.example.com:123/forum/questions/?tag=networking&order=newest#top";
        $uri = new Uri($url);
        $this->assertEquals($uri->getScheme(), $uri->scheme);
        $this->assertEquals($uri->getUser(), $uri->user);
        $this->assertEquals($uri->getUserPass(), $uri->pass);
        $this->assertEquals($uri->getUserInfo(), $uri->user_info);
        $this->assertEquals($uri->getHost(), $uri->host);
        $this->assertEquals($uri->getHostInfo(), $uri->host_info);
        $this->assertEquals($uri->getAuthority(), $uri->authority);
        $this->assertEquals($uri->getPath(), $uri->path);
        $this->assertEquals($uri->getQuery(), $uri->query);
        $this->assertEquals($uri->getQueryData(), $uri->query_data);
        $this->assertEquals($uri->getFragment(), $uri->fragment);
        $this->assertEquals($uri->toString(), $uri->url);
    }

    public function testOffsetGet()
    {
        $url = "https://john.doe:secret@www.example.com:123/forum/questions/?tag=networking&order=newest#top";
        $uri = new Uri($url);
        $this->assertEquals($uri->getScheme(), $uri['scheme']);
        $this->assertEquals($uri->getUser(), $uri['user']);
        $this->assertEquals($uri->getUserPass(), $uri['pass']);
        $this->assertEquals($uri->getUserInfo(), $uri['user_info']);
        $this->assertEquals($uri->getHost(), $uri['host']);
        $this->assertEquals($uri->getHostInfo(), $uri['host_info']);
        $this->assertEquals($uri->getAuthority(), $uri['authority']);
        $this->assertEquals($uri->getPath(), $uri['path']);
        $this->assertEquals($uri->getQuery(), $uri['query']);
        $this->assertEquals($uri->getQueryData(), $uri['query_data']);
        $this->assertEquals($uri->getFragment(), $uri['fragment']);
        $this->assertEquals($uri->toString(), $uri['url']);
    }

    public function testOffsetExists()
    {
        $uri = new Uri("https://foo:bar@example.org");
        $this->assertTrue(isset($uri['scheme']));
        $this->assertTrue(isset($uri['user']));
        $this->assertTrue(isset($uri['pass']));
        $this->assertTrue(isset($uri['user_info']));
        $this->assertTrue(isset($uri['host']));
        $this->assertTrue(isset($uri['host_info']));
        $this->assertTrue(isset($uri['authority']));
        $this->assertTrue(isset($uri['path']));
        $this->assertTrue(isset($uri['query']));
        $this->assertTrue(isset($uri['query_data']));
        $this->assertTrue(isset($uri['fragment']));
        $this->assertTrue(isset($uri['url']));
    }

    public function testOffsetSet()
    {
        $uri = new Uri("https://foo:bar@example.org");
        $uri['scheme'] = 'http'; // setters have no effect
        $this->assertEquals('https', $uri['scheme']);
    }

    public function testOffsetUnset()
    {
        $uri = new Uri("https://foo:bar@example.org");
        unset($uri['scheme']); // setters have no effect
        $this->assertEquals('https', $uri['scheme']);
    }
}
