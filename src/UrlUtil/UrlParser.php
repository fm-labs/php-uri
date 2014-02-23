<?php
namespace UrlUtil;

/**
 * Class UrlParser
 */
class UrlParser implements \ArrayAccess {

    protected $components = array('scheme', 'user', 'pass', 'host', 'port', 'path', 'fragment', 'query');

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $pass;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $query;

    /**
     * @var array
     */
    protected $queryData = array();

    /**
     * @var string
     */
    protected $fragment;

    /**
     * @param string $url
     */
    public function __construct($url = null)
    {
        if ($url) {
            $this->setUrl($url);
        }
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        $this->parseUrl();
        return $this;
    }

    /**
     * Component Setter
     *
     * @param string $key
     * @param mixed $val
     * @return void
     */
    protected function set($key, $val)
    {
        $this->{$key} = $val;
    }

    /**
     * Parse Url components
     *
     * @return void
     */
    protected function parseUrl()
    {
        $components = parse_url($this->url);

        foreach ($this->components as $c) {
            if (isset($components[$c])) {
                $this->set($c, $components[$c]);
            } else {
                $this->set($c, '');
            }
        }

        if (isset($components['query'])) {
            parse_str($components['query'], $this->queryData);
        }
    }

    public function getComponents()
    {
        return array(
            'scheme' => $this->scheme,
            'user' => $this->user,
            'pass' => $this->pass,
            'host' => $this->host,
            'port' => $this->port,
            'path' => $this->path,
            'fragment' => $this->fragment,
            'query' => $this->query
        );
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getQueryData($key = null)
    {
        if ($key === null) {
            return $this->queryData;
        }

        if ($this->hasQueryData($key)) {
            return $this->queryData[$key];
        }

        return null;
    }

    public function hasQueryData($key)
    {
        return isset($this->queryData[$key]);
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function getAuthUser()
    {
        return $this->user;
    }

    public function getAuthPass()
    {
        return $this->pass;
    }

    public function getHostTld()
    {
        // @todo Implement me
        return '';
    }

    public function getHostIp()
    {
        // @todo Implement me
        return '127.0.0.1';
    }

    /**
     * Get Url
     *
     * @return string
     */
    public function getUrl()
    {
        // scheme
        $scheme = '';
        if ($this->scheme) {
            $scheme = $this->scheme . '://';
        }

        // auth
        $auth = '';
        if ($this->user && $this->pass) {
            $auth = $this->user . ':' . $this->pass . '@';
        } elseif ($this->user) {
            $auth = $this->user . '@';
        }

        // host
        $host = $this->host;
        if ($this->port) {
            $host .= ':' . $this->port;
        }

        // path
        $path = $this->path;

        // query
        $query = '';
        if ($this->query) {
            $query = '?' . $this->query;
        }

        // fragment
        $fragment = '';
        if ($this->fragment) {
            $fragment = '#' . $this->fragment;
        }

        return $scheme . $auth . $host . $path . $query . $fragment;
    }

    /**
     * @return string
     */
    public function getRawUrl()
    {
        return $this->url;
    }

    public function offsetGet($offset)
    {

    }

    public function offsetSet($offset, $value)
    {

    }

    public function offsetExists($offset)
    {

    }

    public function offsetUnset($offset)
    {

    }

    public function __toString()
    {
        return $this->getUrl();
    }
}