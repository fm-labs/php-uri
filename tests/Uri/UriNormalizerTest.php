<?php

namespace FmLabs\Test\Uri;

use FmLabs\Uri\UriFactory;
use FmLabs\Uri\UriNormalizer;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriNormalizerTest extends TestCase
{
    public function testGetUri(): void
    {
        $normalizer = new UriNormalizer(UriFactory::fromString('http://eXample.org'));
        $this->assertInstanceOf(UriInterface::class, $normalizer->getUri());
        $this->assertEquals('http://eXample.org', $normalizer->getUri());
    }

    public function testNormalizeScheme(): void
    {
        $uri = UriFactory::fromString('HTTP://example.org/');
        $this->assertEquals('http://example.org/', UriNormalizer::normalize($uri));

        // with normalizer instance
        $uri = UriFactory::fromString('HTTP://example.org/');
        $normalized = (new UriNormalizer($uri))
            ->normalizeScheme()
            ->getUri();
        $this->assertEquals('http://example.org/', $normalized);
    }

    public function testNormalizeHost(): void
    {
        $uri = UriFactory::fromString('http://eXamPLE.oRg/');
        $this->assertEquals('http://example.org/', UriNormalizer::normalize($uri));

        // with normalizer instance
        $uri = UriFactory::fromString('http://eXamPLE.oRg/');
        $normalized = (new UriNormalizer($uri))
            ->normalizeHost()
            ->getUri();
        $this->assertEquals('http://example.org/', $normalized);
    }

    public function testNormalizeDefaultPorts(): void
    {
        $uri = UriFactory::fromString('http://example.org:80/');
        $this->assertEquals('http://example.org/', UriNormalizer::normalize($uri));

        $uri = UriFactory::fromString('http://example.org:8080/');
        $this->assertEquals('http://example.org:8080/', UriNormalizer::normalize($uri));

        $uri = UriFactory::fromString('https://example.org:443/');
        $this->assertEquals('https://example.org/', UriNormalizer::normalize($uri));

        $uri = UriFactory::fromString('https://example.org:9443/');
        $this->assertEquals('https://example.org:9443/', UriNormalizer::normalize($uri));

        // with normalizer instance
        $uri = UriFactory::fromString('http://example.org:80/');
        $normalized = (new UriNormalizer($uri))
            ->normalizeDefaultPorts()
            ->getUri();
        $this->assertEquals('http://example.org/', $normalized);
    }

    public function testNormalizeTrailingSlash(): void
    {
        $uri = UriFactory::fromString('http://example.org/');
        $this->assertEquals('http://example.org/', UriNormalizer::normalize($uri));

        $uri = UriFactory::fromString('http://example.org');
        $this->assertEquals('http://example.org/', UriNormalizer::normalize($uri));

        $uri = UriFactory::fromString('http://example.org/test');
        $this->assertEquals('http://example.org/test/', UriNormalizer::normalize($uri));

        $uri = UriFactory::fromString('http://example.org/test?q=1');
        $this->assertEquals('http://example.org/test/?q=1', UriNormalizer::normalize($uri));

        $uri = UriFactory::fromString('http://example.org/test?q=1#frag');
        $this->assertEquals('http://example.org/test/?q=1#frag', UriNormalizer::normalize($uri));

        // with normalizer instance
        $uri = UriFactory::fromString('http://example.org/test?q=1#frag');
        $normalized = (new UriNormalizer($uri))
            ->normalizeTrailingSlash()
            ->getUri();
        $this->assertEquals('http://example.org/test/?q=1#frag', $normalized);
    }

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
            $this->assertEquals($expected, UriNormalizer::normalize($uri));
        }

        // with normalizer instance
        $uri = UriFactory::fromString('http://www.example.com/%7Eusername/');
        $normalized = (new UriNormalizer($uri))
            ->normalizeUnreservedChars()
            ->getUri();
        $this->assertEquals('http://www.example.com/~username/', $normalized);
    }

    public function testNormalizeEscapeSequences(): void
    {
        $url = 'http://www.example.com/a%c2%b1b/';
        $expected = 'http://www.example.com/a%C2%B1b/';

        $uri = UriFactory::fromString($url);
        $this->assertEquals($expected, UriNormalizer::normalize($uri));

        // with normalizer instance
        $uri = UriFactory::fromString($url);
        $normalized = (new UriNormalizer($uri))
            ->normalizeEscapeSequences()
            ->getUri();
        $this->assertEquals($expected, $normalized);
    }

    public function testNormalizeProtocols(): void
    {
        $this->markTestIncomplete();
    }

    public function testNormalizeFragment(): void
    {
        $this->markTestIncomplete();
    }

    public function testNormalizeQuerySorting(): void
    {
        $this->markTestIncomplete();
    }

    public function testNormalizeNonEmptyPath(): void
    {
        $this->markTestIncomplete();
    }

    public function testNormalizeDirectoryIndex(): void
    {
        $this->markTestIncomplete();
    }

    public function testNormalizeDuplicateSlashes(): void
    {
        $this->markTestIncomplete();
    }

    public function testNormalizeHostIp(): void
    {
        $this->markTestIncomplete();
    }

    public function testNormalizeWwwDomain(): void
    {
        $this->markTestIncomplete();
    }

    public function testNormalizeEmptyQuery(): void
    {
        $this->markTestIncomplete();
    }

    public function testNormalizeDotSegements(): void
    {
        $this->markTestIncomplete();
    }

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

    public function testStaticCapitalizeEscapeSequences(): void
    {
        $path = '/a%c2%b1b/';
        $expected = '/a%C2%B1b/';

        $this->assertEquals($expected, UriNormalizer::capitalizeEscapeSequences($path));
    }

    public function testStaticRemoveDotSegments(): void
    {
        $this->markTestIncomplete();
    }

    public function testStaticNormalize(): void
    {
        $this->markTestIncomplete();
    }


    public function testStaticWithAllNormalizations(): void
    {
        $this->markTestIncomplete('Implement all the missing normalization methods and tests');
    }

    public function testStaticWithPreservedSemantics(): void
    {
        $this->markTestIncomplete('Implement all the missing semantic-preserving methods and tests');
    }

    public function testStaticWithChangedSemantics(): void
    {
        $this->markTestIncomplete('Implement all the missing semantic-changing methods and tests');
    }
}
