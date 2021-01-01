<?php

namespace FmLabs\Uri\Test\TestCase;

use FmLabs\Uri\UriFactory;
use FmLabs\Uri\UriNormalizer;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriNormalizerTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetUri(): void
    {
        $normalizer = new UriNormalizer(UriFactory::fromString('http://eXample.org'));
        $this->assertInstanceOf(UriInterface::class, $normalizer->getUri());
        $this->assertEquals('http://eXample.org', $normalizer->getUri());
    }

    /**
     * @return void
     */
    public function testNormalizeScheme(): void
    {
        $uri = UriFactory::fromString('HTTP://example.org/');
        $this->assertEquals('http://example.org/', (string)UriNormalizer::normalize($uri));

        // with normalizer instance
        $uri = UriFactory::fromString('HTTP://example.org/');
        $normalized = (new UriNormalizer($uri))
            ->normalizeScheme()
            ->getUri();
        $this->assertEquals('http://example.org/', (string)$normalized);
    }

    /**
     * @return void
     */
    public function testNormalizeHost(): void
    {
        $uri = UriFactory::fromString('http://eXamPLE.oRg/');
        $this->assertEquals('http://example.org/', (string)UriNormalizer::normalize($uri));

        // with normalizer instance
        $uri = UriFactory::fromString('http://eXamPLE.oRg/');
        $normalized = (new UriNormalizer($uri))
            ->normalizeHost()
            ->getUri();
        $this->assertEquals('http://example.org/', (string)$normalized);
    }

    /**
     * @return void
     */
    public function testNormalizeDefaultPorts(): void
    {
        $uri = UriFactory::fromString('http://example.org:80/');
        $this->assertEquals('http://example.org/', (string)UriNormalizer::normalize($uri));

        $uri = UriFactory::fromString('http://example.org:8080/');
        $this->assertEquals('http://example.org:8080/', (string)UriNormalizer::normalize($uri));

        $uri = UriFactory::fromString('https://example.org:443/');
        $this->assertEquals('https://example.org/', (string)UriNormalizer::normalize($uri));

        $uri = UriFactory::fromString('https://example.org:9443/');
        $this->assertEquals('https://example.org:9443/', (string)UriNormalizer::normalize($uri));

        // with normalizer instance
        $uri = UriFactory::fromString('http://example.org:80/');
        $normalized = (new UriNormalizer($uri))
            ->normalizeDefaultPorts()
            ->getUri();
        $this->assertEquals('http://example.org/', (string)$normalized);
    }

    /**
     * @return void
     */
    public function testNormalizeTrailingSlash(): void
    {
        $uri = UriFactory::fromString('http://example.org/');
        $this->assertEquals('http://example.org/', (string)UriNormalizer::normalize($uri));

        $uri = UriFactory::fromString('http://example.org');
        $this->assertEquals('http://example.org/', (string)UriNormalizer::normalize($uri));

        $this->markTestIncomplete();
        $uri = UriFactory::fromString('http://example.org/test');
        $this->assertEquals('http://example.org/test/', (string)UriNormalizer::normalize($uri));

        $uri = UriFactory::fromString('http://example.org/test?q=1');
        $this->assertEquals('http://example.org/test/?q=1', (string)UriNormalizer::normalize($uri));

        $uri = UriFactory::fromString('http://example.org/test?q=1#frag');
        $this->assertEquals('http://example.org/test/?q=1#frag', (string)UriNormalizer::normalize($uri));

        $uri = UriFactory::fromString('http://example.org/test.html?q=1#frag');
        $this->assertEquals('http://example.org/test.html?q=1#frag', (string)UriNormalizer::normalize($uri));


        // with normalizer instance
        $uri = UriFactory::fromString('http://example.org/test?q=1#frag');
        $normalized = (new UriNormalizer($uri))
            ->normalizeTrailingSlash()
            ->getUri();
        $this->assertEquals('http://example.org/test/?q=1#frag', (string)$normalized);
    }

    /**
     * @return void
     */
    public function testNormalizeUnreservedChars(): void
    {
        $tests = [
            'http://www.example.com/%2Dusername/' => 'http://www.example.com/-username/',
            'http://www.example.com/%2Eusername/' => 'http://www.example.com/.username/',
            'http://www.example.com/%5Fusername/' => 'http://www.example.com/_username/',
            'http://www.example.com/%7Eusername/' => 'http://www.example.com/~username/',
        ];

        foreach ($tests as $url => $expected) {
            $uri = UriFactory::fromString($url);
            $this->assertEquals($expected, (string)UriNormalizer::normalize($uri));
        }

        // with normalizer instance
        $uri = UriFactory::fromString('http://www.example.com/%7Eusername/');
        $normalized = (new UriNormalizer($uri))
            ->normalizeUnreservedChars()
            ->getUri();
        $this->assertEquals('http://www.example.com/~username/', (string)$normalized);
    }

    /**
     * @return void
     */
    public function testNormalizeEscapeSequences(): void
    {
        $url = 'http://www.example.com/a%c2%b1b/';
        $expected = 'http://www.example.com/a%C2%B1b/';

        $uri = UriFactory::fromString($url);
        $this->assertEquals($expected, (string)UriNormalizer::normalize($uri));

        // with normalizer instance
        $uri = UriFactory::fromString($url);
        $normalized = (new UriNormalizer($uri))
            ->normalizeEscapeSequences()
            ->getUri();
        $this->assertEquals($expected, (string)$normalized);
    }

    /**
     * @return void
     */
    public function testForceHttps(): void
    {
        $url = 'http://www.example.com/';
        $expected = 'https://www.example.com/';
        $uri = UriFactory::fromString($url);
        $this->assertEquals($expected, (string)UriNormalizer::normalize($uri, ['force_ssl' => true]));

        // with normalizer instance
        $uri = UriFactory::fromString($url);
        $normalized = (new UriNormalizer($uri))
            ->normalizeForceHttps()
            ->getUri();
        $this->assertEquals($expected, (string)$normalized);
    }

    /**
     * @return void
     */
    public function testNormalizeFragment(): void
    {
        $url = 'http://example.com/bar.html#section1';
        $expected = 'http://example.com/bar.html';
        $uri = UriFactory::fromString($url);
        $this->assertEquals($expected, (string)UriNormalizer::normalize($uri, ['no_frag' => true]));

        // with normalizer instance
        $uri = UriFactory::fromString($url);
        $normalized = (new UriNormalizer($uri))
            ->normalizeFragment()
            ->getUri();
        $this->assertEquals($expected, (string)$normalized);
    }

    /**
     * @return void
     */
    public function testNormalizeQuerySorting(): void
    {
        $url = 'http://example.com/display?lang=en&article=fred';
        $expected = 'http://example.com/display?article=fred&lang=en';
        $uri = UriFactory::fromString($url);
        $this->assertEquals($expected, (string)UriNormalizer::normalize($uri, ['query_sort' => true]));

        // with normalizer instance
        $uri = UriFactory::fromString($url);
        $normalized = (new UriNormalizer($uri))
            ->normalizeQuerySorting()
            ->getUri();
        $this->assertEquals($expected, (string)$normalized);
    }

    /**
     * @return void
     */
    public function testNormalizeDuplicateSlashes(): void
    {
        $url = 'http://example.com/foo//bar.html';
        $expected = 'http://example.com/foo/bar.html';
        $uri = UriFactory::fromString($url);
        $this->assertEquals($expected, (string)UriNormalizer::normalize($uri));

        // with normalizer instance
        $uri = UriFactory::fromString($url);
        $normalized = (new UriNormalizer($uri))
            ->normalizeDuplicateSlashes()
            ->getUri();
        $this->assertEquals($expected, (string)$normalized);
    }

    /**
     * @return void
     * @deprecated
     */
    public function testNormalizeEmptyQuery(): void
    {
        $url = 'http://example.com/bar.html?';
        $expected = 'http://example.com/bar.html';
        $uri = UriFactory::fromString($url);
        $this->assertEquals($expected, (string)UriNormalizer::normalize($uri));

        // with normalizer instance
        $uri = UriFactory::fromString($url);
        $normalized = (new UriNormalizer($uri))
            ->normalizeEmptyQuery()
            ->getUri();
        $this->assertEquals($expected, (string)$normalized);
    }

    /**
     * @return void
     */
    public function testNormalizeDotSegements(): void
    {
        $url = 'http://example.com/foo/./bar/baz/../qux';
        $expected = 'http://example.com/foo/bar/qux';
        $uri = UriFactory::fromString($url);
        $this->assertEquals($expected, (string)UriNormalizer::normalize($uri));

        // with normalizer instance
        $uri = UriFactory::fromString($url);
        $normalized = (new UriNormalizer($uri))
            ->normalizeDotSegements()
            ->getUri();
        $this->assertEquals($expected, (string)$normalized);
    }

    /**
     * @return void
     */
    public function testNormalizeNonEmptyPath(): void
    {
        $this->markTestIncomplete();
    }

    /**
     * @return void
     */
    public function testNormalizeDirectoryIndex(): void
    {
        $this->markTestIncomplete();
    }

    /**
     * @return void
     */
    public function testNormalizeHostIp(): void
    {
        $this->markTestIncomplete();
    }

    /**
     * @return void
     */
    public function testNormalizeWwwDomain(): void
    {
        $this->markTestIncomplete();
    }

    /**
     * @return void
     */
    public function testStaticDecodeUnreservedChars(): void
    {
        $tests = [
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
        ];

        foreach ($tests as $encoded => $unencoded) {
            $this->assertEquals($unencoded, UriNormalizer::decodeUnreservedChars($encoded));
        }
    }

    /**
     * @return void
     */
    public function testStaticCapitalizeEscapeSequences(): void
    {
        $path = '/a%c2%b1b/';
        $expected = '/a%C2%B1b/';

        $this->assertEquals($expected, UriNormalizer::capitalizeEscapeSequences($path));
    }

    /**
     * @return void
     */
    public function testStaticRemoveDotSegments(): void
    {
        $this->markTestIncomplete();
    }

    /**
     * @return void
     */
    public function testStaticNormalize(): void
    {
        $this->markTestIncomplete();
    }

    /**
     * @return void
     */
    public function testStaticWithAllNormalizations(): void
    {
        $this->markTestIncomplete('Implement all the missing normalization methods and tests');
    }

    /**
     * @return void
     */
    public function testStaticWithPreservedSemantics(): void
    {
        $this->markTestIncomplete('Implement all the missing semantic-preserving methods and tests');
    }

    /**
     * @return void
     */
    public function testStaticWithChangedSemantics(): void
    {
        $this->markTestIncomplete('Implement all the missing semantic-changing methods and tests');
    }
}
