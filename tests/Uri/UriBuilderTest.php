<?php

namespace FmLabs\Test\Uri;


use FmLabs\Uri\UriBuilder;

class UriBuilderTest extends \PHPUnit\Framework\TestCase
{

    public function testSetScheme()
    {
        $builder = new UriBuilder("https://user:secret@www.example.org/my/path?some=query#frag");
        $builder->setScheme("http");
        $this->assertEquals("http", $builder->toUri()->getScheme());
    }

    public function testSetUserInfo()
    {
        $builder = new UriBuilder("https://user:secret@www.example.org/my/path?some=query#frag");
        $builder->setUser("admin");
        $builder->setUserPass("somepass");
        $this->assertEquals("admin:somepass", $builder->toUri()->getUserInfo());
    }

    public function testSetHost()
    {
        $builder = new UriBuilder("https://user:secret@www.example.org/my/path?some=query#frag");
        $builder->setHost("example.net");
        $this->assertEquals("example.net", $builder->toUri()->getHost());
    }

    public function testSetPort()
    {
        $builder = new UriBuilder("https://user:secret@www.example.org/my/path?some=query#frag");
        $builder->setPort(8080);
        $this->assertEquals("8080", $builder->toUri()->getPort());
    }

    public function testSetPath()
    {
        $builder = new UriBuilder("https://user:secret@www.example.org/my/path?some=query#frag");
        $builder->setPath("/foo/bar");
        $this->assertEquals("/foo/bar", $builder->toUri()->getPath());
    }

    public function testSetQuery()
    {
        $builder = new UriBuilder("https://user:secret@www.example.org/my/path?some=query#frag");
        $builder->setQuery("foo=bar&a=b");
        $this->assertEquals("foo=bar&a=b", $builder->toUri()->getQuery());
    }

    public function testSetFragment()
    {
        $builder = new UriBuilder("https://user:secret@www.example.org/my/path?some=query#frag");
        $builder->setFragment("other");
        $this->assertEquals("other", $builder->toUri()->getFragment());
    }

    public function testOffsetUnset()
    {
        $builder = new UriBuilder("https://user:secret@www.example.org/my/path?some=query#frag");
        unset($builder['pass']);
        $this->assertEquals("user", $builder->toUri()->getUserInfo());
        unset($builder['fragment']);
        $this->assertEquals(null, $builder->toUri()->getFragment());
    }

    public function testOffsetSet()
    {
        $builder = new UriBuilder("https://user:secret@www.example.org/my/path?some=query#frag");
        $builder['pass'] = 'othersecret';
        $this->assertEquals("user:othersecret", $builder->toUri()->getUserInfo());
        $builder['fragment'] = 'other';
        $this->assertEquals("other", $builder->toUri()->getFragment());
    }
}
