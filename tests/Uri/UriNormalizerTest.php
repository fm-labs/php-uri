<?php
namespace FmLabs\Test\Uri;

use FmLabs\Uri\Uri;
use FmLabs\Uri\UriFactory;
use FmLabs\Uri\UriNormalizer;

class UriNormalizerTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function assertEqual($actual, $expected): void
    {
        $this->assertEquals($expected, $actual);
    }

    public function testLowercaseScheme(): void
    {
        $uri = UriFactory::fromString('HTTP://example.org/');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/');
    }

    public function testLowercaseHost(): void
    {
        $uri = UriFactory::fromString('http://eXamPLE.oRg/');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/');
    }

    public function testRemoveDefaultPorts(): void
    {
        $uri = UriFactory::fromString('http://example.org:80/');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/');

        $uri = UriFactory::fromString('http://example.org:8080/');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org:8080/');

        $uri = UriFactory::fromString('https://example.org:443/');
        $this->assertEqual(UriNormalizer::normalize($uri), 'https://example.org/');

        $uri = UriFactory::fromString('https://example.org:9443/');
        $this->assertEqual(UriNormalizer::normalize($uri), 'https://example.org:9443/');
    }

    public function testAddTrailingSlash(): void
    {
        $uri = UriFactory::fromString('http://example.org/');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/');

        $uri = UriFactory::fromString('http://example.org');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/');

        $uri = UriFactory::fromString('http://example.org/test');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/test/');

        $uri = UriFactory::fromString('http://example.org/test?q=1');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/test/?q=1');

        $uri = UriFactory::fromString('http://example.org/test?q=1#frag');
        $this->assertEqual(UriNormalizer::normalize($uri), 'http://example.org/test/?q=1#frag');
    }

    public function testStaticCapitalizeEscapeSequences() : void
    {
        $url = 'http://www.example.com/a%c2%b1b/';
        $expected = 'http://www.example.com/a%C2%B1b/';

        $this->assertEqual(UriNormalizer::capitalizeEscapeSequences($url), $expected);
    }

    public function testCapitalizeEscapeSequences(): void
    {
        $url = 'http://www.example.com/a%c2%b1b/';
        $expected = 'http://www.example.com/a%C2%B1b/';

        $uri = UriFactory::fromString($url);
        $this->assertEqual(UriNormalizer::normalize($uri), $expected);
    }

    public function testStaticDecodeUnreservedChars(): void
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

    public function testDecodePercentEncodedUnreservedCharacters(): void
    {
        $tests = array(
            'http://www.example.com/%2Dusername/' => 'http://www.example.com/-username/',
            'http://www.example.com/%2Eusername/' => 'http://www.example.com/.username/',
            'http://www.example.com/%5Fusername/' => 'http://www.example.com/_username/',
            'http://www.example.com/%7Eusername/' => 'http://www.example.com/~username/'
        );

        foreach ($tests as $url => $expected) {
            $uri = UriFactory::fromString($url);
            $this->assertEqual(UriNormalizer::normalize($uri), $expected);
        }
    }

    public function testRemoveDotSegments(): void
    {
        $this->markTestIncomplete('Implement all the dot segment removal methods and tests');
    }

    public function testAllQueryNormalizations(): void
    {
        $this->markTestIncomplete('Implement all the missing query normalization methods and tests');
    }

    public function testAllSemanticChangesNormalizations(): void
    {
        $this->markTestIncomplete('Implement all the missing semantic-changes methods and tests');
    }
}
