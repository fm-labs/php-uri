<?php
namespace UrlUtil;

require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class UrlParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \UrlUtil\UrlParser
     */
    public $Parser;

    public function setUp()
    {
        parent::setUp();

        $this->Parser = new UrlParser();
    }

    public function assertEqual($actual, $expected)
    {
        $this->assertEquals($expected, $actual);
    }

    public function testSetUrl()
    {
        $url = 'http://www.example.org';
        $this->Parser->setUrl($url);

        $this->assertEqual($this->Parser->getUrl(), $url);
    }

    public function testGetScheme()
    {
        $this->Parser->setUrl('http://www.example.org');
        $this->assertEqual($this->Parser->getScheme(), 'http');
        $this->Parser->setUrl('https://www.example.org');
        $this->assertEqual($this->Parser->getScheme(), 'https');
    }

    public function testGetHost()
    {
        $this->Parser->setUrl('http://www.example.org');
        $this->assertEqual($this->Parser->getHost(), 'www.example.org');
    }

    public function testGetPort()
    {
        $this->Parser->setUrl('http://www.example.org');
        $this->assertEqual($this->Parser->getPort(), '');

        $this->Parser->setUrl('http://www.example.org:8080');
        $this->assertEqual($this->Parser->getPort(), '8080');
    }

    public function testGetPath()
    {
        $this->Parser->setUrl('http://www.example.org');
        $this->assertEqual($this->Parser->getPath(), '');

        $this->Parser->setUrl('http://www.example.org/');
        $this->assertEqual($this->Parser->getPath(), '/');

        $this->Parser->setUrl('http://www.example.org/hello?test=1#test');
        $this->assertEqual($this->Parser->getPath(), '/hello');
    }

    public function testGetQuery()
    {
        $this->Parser->setUrl('http://www.example.org');
        $this->assertEqual($this->Parser->getQuery(), '');

        $this->Parser->setUrl('http://www.example.org?');
        $this->assertEqual($this->Parser->getQuery(), '');

        $this->Parser->setUrl('http://www.example.org/hello?test=1#test');
        $this->assertEqual($this->Parser->getQuery(), 'test=1');
    }

    public function testGetFragment()
    {
        $this->Parser->setUrl('http://www.example.org/hello?test=1#test');
        $this->assertEqual($this->Parser->getFragment(), 'test');
    }

    public function testGetAuthUser()
    {
        $this->Parser->setUrl('http://user@www.example.org');
        $this->assertEqual($this->Parser->getAuthUser(), 'user');

        $this->Parser->setUrl('http://user:pass@www.example.org');
        $this->assertEqual($this->Parser->getAuthUser(), 'user');
    }

    public function testGetAuthPass()
    {
        $this->Parser->setUrl('http://user@www.example.org');
        $this->assertEqual($this->Parser->getAuthPass(), '');

        $this->Parser->setUrl('http://user:pass@www.example.org');
        $this->assertEqual($this->Parser->getAuthPass(), 'pass');
    }

    public function testGetHostTld()
    {
        //$this->skipIf(true, 'Implement me');
    }

    public function testGetHostIp()
    {
        //$this->skipIf(true, 'Implement me');
    }

    public function testGetQueryData()
    {
        //$this->skipIf(true, 'Implement me');
    }

    public function testHasQueryData()
    {
        //$this->skipIf(true, 'Implement me');
    }

    public function testOffsetGet()
    {

    }

    public function testOffsetSet()
    {

    }

    public function testOffsetExists()
    {

    }

    public function testOffsetUnset()
    {

    }
}
