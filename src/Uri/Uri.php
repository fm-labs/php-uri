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
class Uri implements \Psr\Http\Message\UriInterface, \ArrayAccess {

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
        } elseif ($uri) {
            throw new \InvalidArgumentException("Invalid URI input");
        } else {
            $this->apply([]);
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

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->toString();
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

    public function getUser()
    {
        return $this->user;
    }

    public function getUserPass()
    {
        return $this->pass;
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
     * @param string|array $key
     * @param string|null $value
     * @return Uri|\Psr\Http\Message\UriInterface
     */
    protected function with($key, $value = null)
    {
        $components = $this->getComponents();
        if (is_array($key)) {
            $components = array_merge($components, $key);
        } else {
            $components[$key] = $value;
        }

        return new self($components);
    }

    /**
     * Return an instance with the specified scheme.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified scheme.
     *
     * Implementations MUST support the schemes "http" and "https" case
     * insensitively, and MAY accommodate other schemes if required.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return static A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        return $this->with('scheme', $scheme);
    }

    /**
     * Return an instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * @return static A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        return $this->with([
            'user' => $user,
            'pass' => $password
        ]);
    }

    /**
     * Return an instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     * @return static A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host)
    {
        return $this->with('host', $host);
    }

    /**
     * Return an instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     * @return static A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        return $this->with('port', $port);
    }

    /**
     * Return an instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified path.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path The path to use with the new instance.
     * @return static A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        return $this->with('path', $path);
    }

    /**
     * Return an instance with the specified query string.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified query string.
     *
     * Users can provide both encoded and decoded query characters.
     * Implementations ensure the correct encoding as outlined in getQuery().
     *
     * An empty query string value is equivalent to removing the query string.
     *
     * @param string $query The query string to use with the new instance.
     * @return static A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        return $this->with('query', $query);
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified URI fragment.
     *
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in getFragment().
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * @return static A new instance with the specified fragment.
     */
    public function withFragment($fragment)
    {
        return $this->with('fragment', $fragment);
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
}
