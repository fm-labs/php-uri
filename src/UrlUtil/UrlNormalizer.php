<?php
namespace UrlUtil;

/**
 * UrlNormalizer
 *
 * @link http://tools.ietf.org/html/rfc3986
 * @link http://en.wikipedia.org/wiki/URL_normalization
 */
class UrlNormalizer extends UrlParser
{

    /**
     * Regex pattern for percent-encoded octets
     *
     * @link http://en.wikipedia.org/wiki/Percent-encoding
     * @const string
     */
    const PERCENT_OCTET_REGEX = '|(\%[a-zA-Z0-9]{2})|';

    /**
     * @var array
     */
    protected $defaultPortMap = array(
        'http' => 80,
        'https' => 443
    );

    /**
     * Normalize Url
     */
    public function normalize()
    {
        /*
         * PRESERVE SEMANTICS
         */

        // Converting the scheme and host to lower case
        if ($this->scheme) {
            $this->scheme = strtolower($this->scheme);
        }
        if ($this->host) {
            $this->host = strtolower($this->host);
        }

        if ($this->path) {
            // Capitalizing letters in escape sequences
            $this->path = self::capitalizeEscapeSequences($this->path);

            // Decoding percent-encoded octets of unreserved characters
            $this->path = self::decodeUnreservedChars($this->path);
        }

        // Removing default ports
        if ($this->scheme && isset($this->defaultPortMap[$this->scheme])) {
            if ($this->port && $this->port == $this->defaultPortMap[$this->scheme]) {
                $this->port = null;
            }
        }

        // Add trailing slash
        if (!$this->path) {
            $this->path = '/';
        } elseif (substr($this->path, -1) != '/') {
            $this->path .= '/';
        }

        // @todo Removing dot-segments

        /*
         * CHANGE SEMANTICS
         */

        // @todo Removing directory index
        // @todo Removing the fragment
        // @todo Replacing IP with domain name
        // @todo Limiting protocols
        // @todo Removing duplicate slashes
        // @todo Removing or adding “www” as the first domain label
        // @todo Sorting the query parameters
        // @todo Removing unused query variables
        // @todo Removing the "?" when the query is empty

        return $this;
    }

    public static function capitalizeEscapeSequences($string)
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
     * @param $string
     * @return string
     */
    public static function decodeUnreservedChars($string)
    {
        $regex = self::PERCENT_OCTET_REGEX;
        $string = preg_replace_callback($regex, function ($matches) {
            $hex = substr($matches[0], 1);
            $dec = hexdec($hex);

            if (($dec >= 0x41 && $dec <= 0x5a) || // ALPHA lowercase
                ($dec >= 0x61 && $dec <= 0x7a) || // ALPHA uppercase
                ($dec >= 0x30 && $dec <= 0x39) || // DIGIT
                (in_array($dec, array(
                    0x2D, // hyphen (-)
                    0x2E, // period (.)
                    0x5F, // underscore (_)
                    0x7E, // tilde (~)
                )))
            ) {
                return chr($dec);
            }

            return $matches[0];
        }, $string);

        return $string;
    }

}