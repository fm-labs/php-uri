<?php
declare(strict_types=1);

namespace FmLabs\Uri;

use Psr\Http\Message\UriInterface;

/**
 * UriNormalizer
 *
 * @link http://tools.ietf.org/html/rfc3986
 * @link http://en.wikipedia.org/wiki/URL_normalization
 */
class UriNormalizer
{
    /**
     * Regex pattern for percent-encoded octets
     *
     * @link http://en.wikipedia.org/wiki/Percent-encoding
     * @const string
     */
    protected const PERCENT_OCTET_REGEX = '|(\%[a-zA-Z0-9]{2})|';

    /**
     * @var array
     */
    protected static $defaultPorts = [
        'http' => 80,
        'https' => 443,
    ];

    /**
     * @var \Psr\Http\Message\UriInterface
     */
    private $uri;

    /**
     * @var array
     */
    private $options;

    /**
     * UriNormalizer constructor.
     *
     * @param \Psr\Http\Message\UriInterface $uri Uri object
     * @param array $options
     */
    public function __construct(UriInterface $uri, array $options = [])
    {
        $this->uri = $uri;
    }

    /**
     * @return \Psr\Http\Message\UriInterface
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * Converting the scheme and host to lower case.
     *
     * @return $this
     */
    public function normalizeScheme()
    {
        $uri = &$this->uri;
        if ($uri->getScheme()) {
            $uri = $uri->withScheme(strtolower($uri->getScheme()));
        }

        return $this;
    }

    /**
     * Converting the scheme and host to lower case.
     *
     * @return $this
     */
    public function normalizeHost()
    {
        $uri = &$this->uri;
        if ($uri->getHost()) {
            $uri = $uri->withHost(strtolower($uri->getHost()));
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function normalizeDotSegements()
    {
        $uri = &$this->uri;
        //@TODO Implement me
        return $this;
    }

    /**
     * @return $this
     */
    public function normalizeTrailingSlash()
    {
        $uri = &$this->uri;
        if (!$uri->getPath()) {
            $uri = $uri->withPath('/');
        } elseif (substr($uri->getPath(), -1) != '/') {
            $uri = $uri->withPath($uri->getPath() . '/');
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function normalizeNonEmptyPath()
    {
        $uri = &$this->uri;
        //@TODO Implement me
        return $this;
    }

    /**
     * @return $this
     */
    public function normalizeUnreservedChars()
    {
        $uri = &$this->uri;
        if ($uri->getPath()) {
            $uri = $uri->withPath(self::decodeUnreservedChars($uri->getPath()));
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function normalizeEscapeSequences()
    {
        $uri = &$this->uri;
        if ($uri->getPath()) {
            $uri = $uri->withPath(self::capitalizeEscapeSequences($uri->getPath()));
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function normalizeDefaultPorts()
    {
        $uri = &$this->uri;
        if ($uri->getScheme() && isset(self::$defaultPorts[$uri->getScheme()])) {
            if ($uri->getPort() && $uri->getPort() == self::$defaultPorts[$uri->getScheme()]) {
                $uri = $uri->withPort(null);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function normalizeFragment()
    {
        $uri = &$this->uri;
        //@TODO Implement me
        return $this;
    }

    /**
     * @return $this
     */
    public function normalizeProtocols()
    {
        $uri = &$this->uri;
        //@TODO Implement me
        return $this;
    }

    /**
     * @return $this
     */
    public function normalizeHostIp()
    {
        $uri = &$this->uri;
        //@TODO Implement me
        return $this;
    }

    /**
     * @return $this
     */
    public function normalizeDirectoryIndex()
    {
        $uri = &$this->uri;
        //@TODO Implement me
        return $this;
    }

    /**
     * @return $this
     */
    public function normalizeDuplicateSlashes()
    {
        $uri = &$this->uri;
        //@TODO Implement me
        return $this;
    }

    /**
     * @return $this
     */
    public function normalizeWwwDomain()
    {
        $uri = &$this->uri;
        //@TODO Implement me
        return $this;
    }

    /**
     * @return $this
     */
    public function normalizeQuerySorting()
    {
        $uri = &$this->uri;
        //@TODO Implement me
        return $this;
    }

    /**
     * @return $this
     */
    public function normalizeEmptyQuery()
    {
        $uri = &$this->uri;
        //@TODO Implement me
        return $this;
    }

    /**
     * Normalize Uri
     *
     * @param \FmLabs\Uri\Uri|\Psr\Http\Message\UriInterface $uri URI object
     * @param array $options Normalization options
     * @return \FmLabs\Uri\Uri|\Psr\Http\Message\UriInterface
     */
    public static function normalize(Uri $uri, array $options = []): UriInterface
    {
        $normalizer = new self($uri, $options);
        // preserve semantics
        $uri = $normalizer
            ->normalizeProtocols()
            ->normalizeScheme()
            ->normalizeHost()
            ->normalizeDefaultPorts()
            ->normalizeDotSegements()
            ->normalizeEscapeSequences()
            ->normalizeUnreservedChars()
            ->normalizeTrailingSlash()
            ->getUri();

        // change semantics
        $preserve = $options['preserve'] ?? true;
        if ($preserve === false) {
            $uri = $normalizer
                ->normalizeHostIp()
                ->normalizeWwwDomain()
                ->normalizeDirectoryIndex()
                ->normalizeDuplicateSlashes()
                ->normalizeNonEmptyPath()
                ->normalizeQuerySorting()
                ->normalizeFragment()
                ->normalizeEmptyQuery()
                ->getUri();
        }

        return $uri;
    }

    /**
     * @param string $string URI component
     * @return string
     */
    public static function capitalizeEscapeSequences(string $string): string
    {
        $regex = self::PERCENT_OCTET_REGEX;
        $string = preg_replace_callback($regex, function ($matches) {
            return strtoupper($matches[0]);
        }, $string);

        return $string;
    }

    /**
     * RFC3986 / Section 2.3
     * For consistency, percent-encoded octets in the ranges of ALPHA
     * (%41-%5A and %61-%7A), DIGIT (%30-%39), hyphen (%2D), period (%2E),
     * underscore (%5F), or tilde (%7E) should not be created by URI
     * producers and, when found in a URI, should be decoded to their
     * corresponding unreserved characters
     *
     * @param string $string URI component
     * @return string
     */
    public static function decodeUnreservedChars(string $string): string
    {
        $regex = self::PERCENT_OCTET_REGEX;
        $string = preg_replace_callback($regex, function ($matches) {
            $hex = substr($matches[0], 1);
            $dec = hexdec($hex);

            if (
                ($dec >= 0x41 && $dec <= 0x5a) || // ALPHA lowercase
                ($dec >= 0x61 && $dec <= 0x7a) || // ALPHA uppercase
                ($dec >= 0x30 && $dec <= 0x39) || // DIGIT
                (in_array($dec, [
                    0x2D, // hyphen (-)
                    0x2E, // period (.)
                    0x5F, // underscore (_)
                    0x7E, // tilde (~)
                ]))
            ) {
                return chr($dec);
            }

            return $matches[0];
        }, $string);

        return $string;
    }

    /**
     * RFC 3986 / Section 5.2.4. Remove Dot Segments.
     * Dot-segments `.` and `..` in the path component of the URI should be removed by applying
     * the remove_dot_segments algorithm to the path described in RFC 3986.
     * Example: http://example.com/foo/./bar/baz/../qux → http://example.com/foo/bar/qux
     *
     * @link https://tools.ietf.org/html/rfc3986#section-5.2.4
     * @param string $path URI component
     * @return string
     */
    public static function removeDotSegments(string $path): string
    {
        // @TODO Implement me
        return $path;
    }
}
