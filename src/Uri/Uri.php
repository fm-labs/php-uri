<?php
declare(strict_types=1);

namespace FmLabs\Uri;

use ArrayAccess;
use Psr\Http\Message\UriInterface;

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
 * @property string $userinfo
 * @property string $hostinfo
 * @property string $authority
 * @property array $querydata
 */
class Uri implements UriInterface, ArrayAccess
{
    protected const SCHEME = 'scheme';
    protected const USER = 'user';
    protected const PASS = 'pass';
    protected const HOST = 'host';
    protected const PORT = 'port';
    protected const PATH = 'path';
    protected const FRAG = 'fragment';
    protected const QUERY = 'query';

    protected const HOSTINFO = 'hostinfo';
    protected const USERINFO = 'userinfo';
    protected const AUTHORITY = 'authority';
    protected const QUERYDATA = 'querydata';

    /**
     * @var array List of URI component names
     */
    protected $components = [
        self::SCHEME => null,
        self::USER => null,
        self::PASS => null,
        self::HOST => null,
        self::PORT => null,
        self::PATH => null,
        self::FRAG => null,
        self::QUERY => null,
    ];

    /**
     * @var array
     */
    protected $queryData = [];

    /**
     * @param string $userinfo Userinfo URI component
     * @return array|null[]
     */
    public static function splitUserInfo(string $userinfo): array
    {
        $parts = explode(':', $userinfo);
        switch (count($parts)) {
            case 1:
                return [$parts[0], null];
            case 2:
                return [$parts[0], $parts[1]];
            default:
                return [null, null];
        }
    }

    /**
     * @param array $components URI components
     */
    public function __construct(array $components = [])
    {
        foreach ($components as $key => $val) {
            if (array_key_exists($key, $this->components)) {
                $this->components[$key] = $val;
            }
        }
        if (isset($components[self::QUERY])) {
            parse_str($components[self::QUERY], $this->queryData);
        }
    }

    /**
     * Magic getter access to URI components
     *
     * @param string $key Component key
     * @return array|mixed|string|null
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * @param string $key Component key
     * @return bool
     */
    protected function has(string $key)
    {
        return array_key_exists($key, $this->components)
            || in_array($key, [self::AUTHORITY, self::HOSTINFO, self::USERINFO, self::QUERYDATA]);
    }

    /**
     * @param string $key Component key
     * @return array|mixed|string|null
     */
    protected function get(string $key)
    {
        switch ($key) {
            case self::AUTHORITY:
                return $this->getAuthority();
            case self::USERINFO:
                return $this->getUserInfo();
            case self::HOSTINFO:
                return $this->getHostInfo();
            case self::QUERYDATA:
                return $this->getQueryData();
        }

        return $this->components[$key] ?? null;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->components[self::SCHEME];
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->components[self::HOST];
    }

    /**
     * @return int|string|null
     */
    public function getPort()
    {
        return $this->components[self::PORT];
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->components[self::PATH];
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->components[self::QUERY];
    }

    /**
     * @return string
     */
    public function getFragment()
    {
        return $this->components[self::FRAG];
    }

    /**
     * Returns the Userinfo URI subcomponent part
     * in the format of "[username][:userpass]"
     *
     * @return string
     */
    public function getUserInfo()
    {
        $info = '';
        if ($this->user && $this->pass) {
            $info = $this->user . ':' . $this->pass;
        } elseif ($this->user) {
            $info = $this->user;
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
        $auth = '';
        if ($this->getUserInfo()) {
            $auth .= $this->getUserInfo() . '@';
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
        return [
            self::SCHEME => $this->scheme,
            self::USER => $this->user,
            self::PASS => $this->pass,
            self::HOST => $this->host,
            self::PORT => $this->port,
            self::PATH => $this->path,
            self::FRAG => $this->fragment,
            self::QUERY => $this->query,
        ];
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->components[self::USER];
    }

    /**
     * @return string
     */
    public function getUserPass()
    {
        return $this->components[self::PASS];
    }

    /**
     * Returns the host subcomponent including optional port number,
     * in the format of `host[:port]
     *
     * @return string
     */
    public function getHostInfo(): ?string
    {
        $info = $this->getHost();
        if ($this->getPort()) {
            $info .= ':' . $this->getPort();
        }

        return $info;
    }

    /**
     * @param null|string $key Query data key. If NULL, returns array of all query data.
     * @return array|mixed|null
     */
    public function getQueryData(?string $key = null)
    {
        if ($key === null) {
            return $this->queryData;
        }

        return $this->queryData[$key] ?? null;
    }

    /**
     * @param array $components URI components
     * @return \FmLabs\Uri\Uri|\Psr\Http\Message\UriInterface
     */
    protected function with(array $components): self
    {
        $components = array_merge($this->components, $components);

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
        return $this->with([self::SCHEME => $scheme]);
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
        return $this->with([self::USER => $user, self::PASS => $password]);
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
        return $this->with([self::HOST => $host]);
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
        return $this->with([self::PORT => $port]);
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
        return $this->with([self::PATH => $path]);
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
        return $this->with([self::QUERY => $query]);
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
        return $this->with([self::FRAG => $fragment]);
    }

    /**
     * Returns URI components as string
     *
     * @return string
     */
    public function __toString(): string
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
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('Uri object is read-only');
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException('Uri object is read-only');
    }
}
