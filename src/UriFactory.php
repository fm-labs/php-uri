<?php
declare(strict_types=1);

namespace FmLabs\Uri;

use Psr\Http\Message\UriInterface;

/**
 * Class UriFactory
 */
class UriFactory
{
    /**
     * @var string Uri class name
     */
    protected static $className = Uri::class;

    /**
     * @param string $className Default Uri class name
     * @return void
     */
    public static function setClassName(string $className): void
    {
        if (!class_exists($className)) {
            throw new \RuntimeException(sprintf(
                "Uri class '%s' does not exist",
                $className
            ));
        }

        static::$className = $className;
    }

    /**
     * @return \FmLabs\Uri\Uri|\Psr\Http\Message\UriInterface
     */
    public static function create(): UriInterface
    {
        $uri = new static::$className();
        if (!($uri instanceof UriInterface)) {
            throw new \RuntimeException(sprintf(
                "Uri class '%s' does not implement UriInterface",
                static::$className
            ));
        }

        return $uri;
    }

    /**
     * @param array $components URI components
     * @return \FmLabs\Uri\Uri|\Psr\Http\Message\UriInterface
     * @todo Refactor with UriInterface methods only
     */
    public static function fromComponents(array $components): UriInterface
    {
        $uri = static::create();
        /*
        if ($uri instanceof Uri) {
            return $uri->with($components);
        }

        $uri = $uri
            ->withScheme($components['scheme'] ?? null)
            ->withUserInfo($components['user'] ?? null, $components['pass'] ?? null)
            ->withHost($components['host'] ?? null)
            ->withPort($components['port'] ?? null)
            ->withPath($components['path'] ?? null)
            ->withQuery($components['query'] ?? null)
            ->withFragment($components['fragment'] ?? null);

        return $uri;
        */

        return $uri->with($components);
    }

    /**
     * @param string $uriStr URI string
     * @return \FmLabs\Uri\Uri|\Psr\Http\Message\UriInterface
     */
    public static function fromString(string $uriStr): UriInterface
    {
        return static::fromComponents(parse_url($uriStr));
    }

    /**
     * @param \Psr\Http\Message\UriInterface $uri URI object
     * @return \FmLabs\Uri\Uri|\Psr\Http\Message\UriInterface
     */
    public static function fromUri(UriInterface $uri): UriInterface
    {
        [$user, $pass] = Uri::splitUserInfo($uri->getUserInfo());
        $components = [
            'scheme' => $uri->getScheme(),
            'user' => $user,
            'pass' => $pass,
            'host' => $uri->getHost(),
            'port' => $uri->getPort(),
            'path' => $uri->getPath(),
            'query' => $uri->getQuery(),
            'fragment' => $uri->getFragment(),
        ];

        return self::fromComponents($components);
    }
}
