<?php
namespace UrlUtil;

use \UrlUtil\Exception\UrlExpanderException;

/**
 * Class UrlExpander
 *
 * @package UrlUtil
 */
class UrlExpander
{
    const HTTP_HEADER_REGEX = '|^(HTTP)/([0-9\.]+)\s([0-9]{3})\s(.*)$|';

    const ERR_INVALID_URL = 0;
    const ERR_BAD_REQUEST = 1;
    const ERR_LOOP_REDIRECT = 2;
    const ERR_MAX_REDIRECTS = 4;

    /**
     * Url to expand
     *
     * @var string|UrlParser
     */
    protected $inputUrl;

    /**
     * Current expanded url
     *
     * @var string|UrlParser
     */
    protected $url;

    /**
     * Number of maximum redirects
     *
     * @var int
     */
    protected $maxRedirects = 20;

    /**
     * Redirect trace
     *
     * @var array List of redirects
     */
    protected $trace = array();

    public function __construct($url = null)
    {
        if ($url) {
            $this->setUrl($url);
        }
    }

    /**
     * Set url and reset trace
     *
     * @param string $url Url to expand
     */
    public function setUrl($url)
    {
        $this->inputUrl = $url;
        $this->url = $url;
        $this->trace = array();
    }

    /**
     * Returns the current url
     *
     * @return string|UrlParser
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function setMaxRedirects($value)
    {
        $this->maxRedirects = $value;
    }

    /**
     * @return $this
     * @throws UrlExpanderException
     */
    public function expand()
    {
        $url = $this->url;

        try {

            // check for loop redirects
            if (isset($this->trace[md5($url)])) {
                throw new UrlExpanderException(sprintf("Loop redirect for url %s", $url), self::ERR_LOOP_REDIRECT);
            }

            // check max redirects
            if (count($this->trace) >= $this->maxRedirects) {
                throw new UrlExpanderException("Max redirects reached " . count($this->trace), self::ERR_MAX_REDIRECTS);
            }

            // fetch headers
            $headers = $this->fetchHeaders($url);
            if (!$headers) {
                throw new UrlExpanderException("Fetch error");
            }
            // parse http status
            $status = $this->parseHttpStatus($headers[0]);
            if (!$status) {
                throw new UrlExpanderException("Invalid http status: " . $headers[0]);
            }

            // check for errors
            if ($status['code'] >= 400) {
                throw new UrlExpanderException($status['code'] . " " . $status['text']);
            }

            // check for redirects
            if ($status['code'] >= 300 && $status['code'] < 400) {
                $redirect = false;
                if (isset($headers['Location'])) {
                    $redirect = $headers['Location'];
                } elseif (isset($headers['location'])) {
                    $redirect = $headers['location'];
                }

                if (!$redirect) {
                    throw new UrlExpanderException("No redirect location found");
                }

                // expand relative urls
                // @todo proper normalizing
                if ($redirect[0] == '/') {
                    //$_redirect = $redirect;
                    $scheme = parse_url($url, PHP_URL_SCHEME);
                    $host = parse_url($url, PHP_URL_HOST);
                    $port = parse_url($url, PHP_URL_PORT);
                    if (!$port) {
                        $port = ($scheme == 'https') ? 443 : 80;
                    }

                    $redirect = sprintf("%s://%s:%s%s", $scheme, $host, $port, $redirect);
                }

                $this->trace[md5($url)] = $redirect;
                $this->url = $redirect;
                return $this->expand();
            }
        } catch (\Exception $e) {
            // @todo Exception handling
            throw $e;
        }

        return $this;
    }

    public function getRedirectCount()
    {
        return count($this->trace);
    }

    /**
     * @param string $url
     * @return array
     */
    protected function fetchHeaders($url)
    {
        // configure stream context
        $context = array(
            'http' => array(
                'max_redirects' => 1,
                'method' => 'HEAD',
            )
        );
        stream_context_get_default($context);

        // @todo refactor with fsockopen
        $result = get_headers($url, 1);
        return $result;
    }

    /**
     * Parse a HTTP header string
     *
     * Parses a string like "HTTP/1.1 200 Found" into an array
     * array(
     *  'protocol' => 'HTTP',
     *  'protocol_version' => 1.1,
     *  'code' => 200,
     *  'text' => 'Found'
     *  )
     *
     * @param string $header
     * @return array
     */
    protected function parseHttpStatus($header)
    {
        if (preg_match(self::HTTP_HEADER_REGEX, $header, $matches)) {
            return array(
                'protocol' => $matches[1],
                'protocol_version' => $matches[2],
                'code' => (int) $matches[3],
                'text' => (isset($matches[4])) ? $matches[4] : '',
            );
        }
        return false;
    }
}
