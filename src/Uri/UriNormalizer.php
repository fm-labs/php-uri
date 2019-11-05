<?php
namespace FmLabs\Uri;

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
    const PERCENT_OCTET_REGEX = '|(\%[a-zA-Z0-9]{2})|';

    /**
     * @var array
     */
    static protected $defaultPorts = array(
        'http' => 80,
        'https' => 443,
    );

    /**
     * Normalize Uri
     *
     * @param \FmLabs\Uri\Uri $uri
     * @param array $options
     * @return \FmLabs\Uri\Uri
     */
    static public function normalize(\FmLabs\Uri\Uri $uri, array $options = [])
    {
        $builder = new UriBuilder($uri);
        /*
         * PRESERVE SEMANTICS
         */

        // Converting the scheme and host to lower case
        if ($uri->getScheme()) {
            $builder->setScheme(strtolower($uri->getScheme()));
        }
        if ($uri->getHost()) {
            $builder->setHost(strtolower($uri->getHost()));
        }

        if ($uri->getPath()) {
            $normPath = $uri->getPath();
            
            // Capitalizing letters in escape sequences
            $normPath = self::capitalizeEscapeSequences($normPath);

            // Decoding percent-encoded octets of unreserved characters
            $normPath = self::decodeUnreservedChars($normPath);

            $builder->setPath($normPath);
        }

        // Removing default ports
        if ($uri->getScheme() && isset(self::$defaultPorts[$uri->getScheme()])) {
            if ($uri->getPort() && $uri->getPort() == self::$defaultPorts[$uri->getScheme()]) {
                $builder->setPort(null);
            }
        }

        // Add trailing slash
        if (!$uri->getPath()) {
            $builder->setPath('/');
        } elseif (substr($uri->getPath(), -1) != '/') {
            $builder->setPath($uri->getPath() . '/');
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

        return $builder->toUri();
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