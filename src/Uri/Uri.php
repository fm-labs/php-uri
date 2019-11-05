<?php
namespace FmLabs\Uri;

/**
 * Class Uri
 *
 * @property string $scheme
 * @property string $user
 * @property string $pass
 * @property string $host
 * @property string $port
 * @property string $path
 * @property string $query
 * @property string $fragment
 * @property string user_info Composite subcomponent
 * @property string host_info Composite subcomponent
 * @property string authority Composite subcomponent
 */
class Uri implements \ArrayAccess {

    /**
     * @var array List of URI component names
     */
    protected $components = ['scheme' => null, 'user' => null, 'pass' => null, 'host' => null, 'port' => null,
        'path' => null, 'fragment' => null, 'query' => null];

    /**
     * @var array
     */
    protected $queryData = array();

    /**
     * @param string $uri
     */
    public function __construct($uri = null)
    {
        if (is_string($uri)) {
            $components = $this->parse($uri);
            $this->apply($components);
        } elseif (is_array($uri)) {
            $this->apply($uri);
        } elseif ($uri instanceof self) {
            $this->apply($uri->getComponents());
        } else {
            throw new \InvalidArgumentException("Invalid URI input");
        }
    }

    /**
     * Magic getter access to URI components
     *
     * @param $key
     * @return array|mixed|string|null
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    protected function has($key)
    {
        return array_key_exists($key, $this->components)
            || in_array($key, ['host_info', 'user_info', 'authority', 'query_data', 'url']);
    }

    protected function get($key)
    {
        if ($key == "authority") {
            return $this->getAuthority();
        } elseif ($key == "user_info") {
            return $this->getUserInfo();
        } elseif ($key == "host_info") {
            return $this->getHostInfo();
        } elseif ($key == "query_data") {
            return $this->getQueryData();
        } elseif ($key == "url") {
            return $this->toString();
        } elseif ($this->has($key)) {
            return $this->components[$key];
        }

        return null;
    }

    /**
     * @param array $components
     * @return $this
     */
    protected function apply(array $components)
    {
        $components += ['scheme' => null, 'user' => null, 'pass' => null, 'host' => null, 'port' => null,
            'path' => null, 'fragment' => null, 'query' => null];

        $this->components = $components;

        if (isset($components['query'])) {
            parse_str($components['query'], $this->queryData);
        }

        return $this;
    }

    /**
     * Parse URI components from string
     *
     * @param string $uriStr URI string
     * @return array
     */
    protected function parse($uriStr)
    {
        $components = parse_url($uriStr);

        return $components;
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

        if (isset($this->queryData[$key])) {
            return $this->queryData[$key];
        }

        return null;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getUserPass()
    {
        return $this->pass;
    }

    /**
     * Returns the Userinfo URI subcomponent part
     * in the format of "[username][:userpass]"
     *
     * @return string
     */
    public function getUserInfo()
    {
        $info = "";
        if ($this->user && $this->pass) {
            $info = $this->user . ":" . $this->pass;
        } elseif ($this->user) {
            $info = $this->user;
        }

        return $info;
    }

    /**
     * Returns the host subcomponent including optional port number,
     * in the format of `host[:port]
     * @return string
     */
    public function getHostInfo()
    {
        $info = $this->host;
        if ($this->port) {
            $info .= ':' . $this->port;
        }

        return $info;
    }

    /**
     * Returns the Authority URI component, consisting of the userinfo- and
     * the host-subcomponent, in the format [userinfo@]
     *
     * @return string
     */
    public function getAuthority()
    {
        $auth = "";
        if ($this->getUserInfo()) {
            $auth .= $this->getUserInfo() . "@";
        }

        $auth .= $this->getHostInfo();

        return $auth;
    }

    /**
     * Returns the URI components
     *
     * @return array
     */
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
            'query' => $this->query,
            '_authority' => $this->getAuthority(),
            '_host_info' => $this->getHostInfo(),
            '_user_info' => $this->getUserInfo(),
            '_url' => $this->toString()
        );
    }

    /**
     * Returns URI components as string
     *
     * @return string
     */
    public function toString()
    {
        // scheme
        $scheme = $this->scheme;
        if ($scheme) {
            $scheme .= ':';
        }

        // authority
        $authority = $this->getAuthority();
        if ($authority) {
            $authority = '//' . $authority;
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

        return $scheme . $authority . $path . $query . $fragment;
    }

    /**
     * Returns URI components as array.
     * Alias for `getComponents()`
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getComponents();
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetSet($offset, $value)
    {
        // not supported
        return;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        // not supported
        return;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->toString();
    }
}
