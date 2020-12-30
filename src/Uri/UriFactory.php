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
     * @param array $components URI components
     * @return \FmLabs\Uri\Uri|\Psr\Http\Message\UriInterface
     */
    public static function fromComponents(array $components): UriInterface
    {
        return (new Uri())
            ->with($components);
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
            //'authority' => $uri->getAuthority(),
            //'userinfo' => $uri->getUserInfo(),
            'scheme' => $uri->getScheme(),
            'user' => $user,
            'pass' => $pass,
            'host' => $uri->getHost(),
            'port' => $uri->getPort(),
            'path' => $uri->getPath(),
            'fragment' => $uri->getFragment(),
            'query' => $uri->getQuery(),
        ];

        return self::fromComponents($components);
    }
}
