<?php
namespace FmLabs\Uri;

/**
 * Class UriBuilder
 */
class UriBuilder extends Uri
{
    /**
     * @param string $url
     */
    public function __construct($uri)
    {
        parent::__construct($uri);
    }

    /**
     * Magic setter
     *
     * @param string $key
     * @param mixed $val
     */
    public function __set($key, $val)
    {
        if ($this->has($key)) {
            $this->set($key, $val);
        }
    }

    /**
     * Component Setter
     *
     * @param string $key
     * @param mixed $val
     * @return $this
     */
    protected function set($key, $val)
    {
        $this->components[$key] = $val;

        return $this;
    }

    public function setScheme($val)
    {
        return $this->set('scheme', $val);
    }

    public function setUser($val)
    {
        return $this->set('user', $val);
    }

    public function setUserPass($val)
    {
        return $this->set('pass', $val);
    }

    public function setHost($val)
    {
        return $this->set('host', $val);
    }

    public function setPort($val)
    {
        return $this->set('port', $val);
    }

    public function setPath($val)
    {
        return $this->set('path', $val);
    }

    public function setQuery($val)
    {
        return $this->set('query', $val);
    }

    /*
    public function setQueryData($key = null, $val = null)
    {
        if ($key === null && is_array($val)) {
            $this->queryData = $val;
        } else {
            $this->queryData[$key] = $val;
        }

        $this->set('query', $this->buildQueryString($this->queryData));

        return $this;
    }

    protected function buildQueryString($query)
    {
        return http_build_query($query);
    }
    */

    public function setFragment($val)
    {
        return $this->set('fragment', $val);
    }

    public function toUri()
    {
        $uri = new Uri($this->getComponents());

        return $uri;
    }


    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }
}