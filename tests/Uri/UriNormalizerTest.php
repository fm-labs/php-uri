<?php
namespace FmLabs\Test\Uri;

use FmLabs\Uri\Uri;
use FmLabs\Uri\UriNormalizer;

class UriNormalizerTest extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function assertEqual($actual, $expected)
    {
        $this->assertEquals($expected, $actual);
    }

    public function testLowercaseScheme()
    {
        $uri = new Uri('HTTP://example.org/');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/');
    }

    public function testLowercaseHost()
    {
        $uri = new Uri('http://eXamPLE.oRg/');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/');
    }

    public function testRemoveDefaultPorts()
    {
        $uri = new Uri('http://example.org:80/');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/');

        $uri = new Uri('http://example.org:8080/');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org:8080/');

        $uri = new Uri('https://example.org:443/');
        $this->assertEqual(UriNormalizer::normalize($uri), 'https://example.org/');

        $uri = new Uri('https://example.org:9443/');
        $this->assertEqual(UriNormalizer::normalize($uri), 'https://example.org:9443/');
    }

    public function testAddTrailingSlash()
    {
        $uri = new Uri('http://example.org/');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/');

        $uri = new Uri('http://example.org');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/');

        $uri = new Uri('http://example.org/test');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/test/');

        $uri = new Uri('http://example.org/test?q=1');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/test/?q=1');

        $uri = new Uri('http://example.org/test?q=1#frag');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/test/?q=1#frag');
    }

    public function testStaticCapitalizeEscapeSequences() {
        $url = 'http://www.example.com/a%c2%b1b/';
        $expected = 'http://www.example.com/a%C2%B1b/';

        $this->assertEqual(UriNormalizer::capitalizeEscapeSequences($url), $expected);
    }

    public function testCapitalizeEscapeSequences()
    {
        $url = 'http://www.example.com/a%c2%b1b/';
        $expected = 'http://www.example.com/a%C2%B1b/';

        $uri = new Uri($url);
        $this->assertEqual(UriNormalizer::normalize($uri), $expected);
    }

    public function testStaticDecodeUnreservedChars()
    {
        $tests = array(
            '%41' => 'A',
            '%42' => 'B',
            '%5A' => 'Z',
            '%61' => 'a',
            '%62' => 'b',
            '%7A' => 'z',
            '%30' => 0,
            '%39' => 9,
            '%2D' => chr(0x2D), // hyphen
            '%2E' => chr(0x2E), // period
            '%5F' => chr(0x5F), // underscore
            '%7E' => chr(0x7E), // tilde
        );

        foreach ($tests as $encoded => $unencoded) {
            $this->assertEqual(UriNormalizer::decodeUnreservedChars($encoded), $unencoded);
        }
    }

    public function testDecodePercentEncodedUnreservedCharacters()
    {
        $tests = array(
            'http://www.example.com/%2Dusername/' => 'http://www.example.com/-username/',
            'http://www.example.com/%2Eusername/' => 'http://www.example.com/.username/',
            'http://www.example.com/%5Fusername/' => 'http://www.example.com/_username/',
            'http://www.example.com/%7Eusername/' => 'http://www.example.com/~username/'
        );

        foreach ($tests as $test => $expected) {
            $uri = new Uri($test);
            $this->assertEqual(UriNormalizer::normalize($uri), $expected);
        }
    }

    public function testRemoveDotSegments()
    {
        $this->markTestIncomplete('Implement me');
    }

    public function testAllQueryNormalizations()
    {
        $this->markTestIncomplete('Implement all the missing query normalization methods and tests');
    }

    public function testAllSemanticChangesNormalizations()
    {
        $this->markTestIncomplete('Implement all the missing semantic-changes methods and tests');
    }

}
