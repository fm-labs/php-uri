<?php
namespace UrlUtil;

class UrlExpanderTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $url = 'http://rol.st/1hFNUU8';
        $expander = new UrlExpander($url);

        $this->assertEquals($expander->getUrl(), $url);
    }
}
