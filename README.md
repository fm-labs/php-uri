# php-uri

Simple URI library for PHP. Compliant to PSR-7 UriInterface specification.
Includes RFC3968-compliant URI normalizer.

[![Build Status](https://travis-ci.org/fm-labs/php-uri.svg?branch=master)](https://travis-ci.org/fm-labs/php-uri)

## Requirements

- php 7.1+

## Installation

```console
$ composer require fm-labs/php-uri
```

## Classes

### Uri
```php
// > Create new Uri
$uri = \FmLabs\Uri\UriFactory::create();
$uri = $uri
    ->withScheme('https')
    ->withHost('example.org')
    ->withPort(8080)
    ->withPath('/my/path')
    ->withQuery('foo=bar&hello=world')
    ->withFragment('top')
    ->withUserInfo('user', 's3cret');

echo (string)$uri;
// https://user:s3cret@example.org:8080/my/path?foo=bar&hello=world#top
```

```php
// > Create Uri from string
$uri = \FmLabs\Uri\UriFactory::fromString('http://user:s3cret@www.example.org/test?q=hello#world');

// PSR-7 interface methods
$schema = $uri->getScheme(); // "http"
$host = $uri->getHost(); // "www.example.org"
$path = $uri->getPath(); // "/test"
$frag = $uri->getFragment(); // "world"
$userinfo = $uri->getUserInfo(); // "user:s3cret"
$authority = $uri->getAuthority(); // "user:s3cret@www.example.org"

// Convenience methods
$user = $uri->getUser(); // "user"
$pass = $uri->getUserPass(); // "s3cret"
$queryData = $uri->getQueryData(); // ['q' => 'hello']
$queryData = $uri->getQueryData('q'); // 'hello'

// Array access (read-only)
$host = $uri['host'];

// Property access (read-only)
$host = $uri->host;
```

#### PSR-7 UriInterface methods

- `getScheme()`
- `getHost()`
- `getPort()`
- `getPath()`
- `getQuery()`
- `getFragment()`
- `getUserInfo()`
- `getAuthority()`
- `__toString()`
  
- `withScheme()`
- `withHost()`
- `withPort()`
- `withUserInfo()`
- `withPath()`
- `withQuery()`
- `withFragment()`

#### Convenience methods

- `getHostInfo()` - Returns hostinfo string
- `getUser()` - Returns userinfo username
- `getUserPass()` - Returns userinfo password
- `getQueryData(?string $key = null)` - Returns query data as array OR value for specific key
- `getComponents()` - Returns components as array


#### Array and Property Access

Available Keys: 
`scheme`, `host`, `port`, `path`, `query`, `fragment`, `user`, `pass`, `userinfo`, `authority`, `hostinfo`, `querydata`

```php
/** @var \FmLabs\Uri\Uri $uri **/

// Array access
$uri['KEY_NAME'];

// Property access
$uri->KEY_NAME;
```




### UriFactory

Create Uri object with UriFactory class

#### `UriFactory::create()`
```php
// Examples
$uri = \FmLabs\Uri\UriFactory::create();
$uri = $uri
    ->withScheme('https')
    ->withHost('example.org');
```


#### `UriFactory::fromString(string $uriString)`
```php
// Examples
\FmLabs\Uri\UriFactory::fromString('http://www.example.org');
\FmLabs\Uri\UriFactory::fromString('https://user:secret@www.example.org/my/path?some=query#frag');
\FmLabs\Uri\UriFactory::fromString('https://john.doe@www.example.com:123/forum/questions/?tag=networking&order=newest#top');
\FmLabs\Uri\UriFactory::fromString('mailto:John.Doe@example.com');
\FmLabs\Uri\UriFactory::fromString('tel:+1-816-555-1212');
\FmLabs\Uri\UriFactory::fromString('ldap://[2001:db8::7]/c=GB?objectClass?one');
\FmLabs\Uri\UriFactory::fromString('urn:oasis:names:specification:docbook:dtd:xml:4.1.2');
```

#### `UriFactory::fromComponents(array $components)`
```php
// Examples
// http://www.example.org
\FmLabs\Uri\UriFactory::fromComponents(['scheme' => 'http', 'host' => 'www.example.org']);
// tel:+123456789
\FmLabs\Uri\UriFactory::fromComponents(['scheme' => 'tel', 'path' => '+123456789']);
```

#### `UriFactory::fromUri(UriInterface $uri)`
Create `\FmLabs\Uri\Uri` object from any `UriInterface`. 

```php
/** @var \Psr\Http\Message\UriInterface $anyObjectThatImplementsUriInterface */
$uri = \FmLabs\Uri\UriFactory::fromUri($anyObjectThatImplementsUriInterface);
```

### UriNormalizer

#### `static normalize()`
Normalize URI. See [RFC3968](http://tools.ietf.org/html/rfc3986)
and [here](http://en.wikipedia.org/wiki/URL_normalization).

Returns a new `Uri` object with normalized components. 

```php
$uri = \FmLabs\Uri\UriNormalizer::normalize('hTTp://www.eXample.org:80/test/./../foo/../bar');
// http://www.example.org/test/foo/bar;
```


## Usage

## Run tests
```console
$ composer run-script test
// or
$ composer run-script test-verbose
// or
$ ./vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/
```

## TODO

- UriNormalizer: normalize: Removing dot-segments
- UriNormalizer: normalize: Removing directory index
- UriNormalizer: normalize: Removing the fragment
- UriNormalizer: normalize: Replacing IP with domain name
- UriNormalizer: normalize: Limiting protocols
- UriNormalizer: normalize: Removing duplicate slashes
- UriNormalizer: normalize: Removing or adding “www” as the first domain label
- UriNormalizer: normalize: Sorting the query parameters
- UriNormalizer: normalize: Removing unused query variables
- UriNormalizer: normalize: Removing the "?" when the query is empty

## Changelog
[0.5]
- Changed Uri class: Removed constructor
- Changed Uri class: Implemented etter method to create new modified instances
- Changed Uri class: Use class properties instead of property array
- Added PHP8 annotations
- Added TravisCI build targets php7.4 & php8.0
- Added Tests support for PHPUnit9

[0.4]
- Dropped UriBuilder in favor of UriFactory
- Added UriFactory  
- Changed Uri constructor
- Changed PHP language level to 7.1
- Changed license to MIT license
- Added: Tests support for PHPUnit8
- Fixed: Code style

[0.3.1]
- Added PSR-7 compatibility. Uri now implements PSR UriInterface
- Added TravisCI build targets php7.2 & php7.3

[0.3]
- Changed namespace to '\FmLabs\Uri'
- Changed project name to 'php-uri'
- Removed UrlExpander class
- Refactored Url to Uri class
- Refactored UrlNormalizer to UriNormalizer
- Added UriBuilder class
- Upgraded unit tests to PHPUnit6
- Set min PHP version to 7.0

[0.2]
- Added UrlExpander util class (requires curl)

## License

See LICENSE file



