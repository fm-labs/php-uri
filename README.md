# php-uri

Simple URI library for PHP

[![Build Status](https://travis-ci.org/fm-labs/php-uri.svg?branch=master)](https://travis-ci.org/fm-labs/php-uri)

## Requirements

- php 7.0+

## Installation

```console
$ cd /my/project/dir
$ composer require fm-labs/php-uri ^0.3
```

## Components

### Uri

- getScheme()
- getUser()
- getUserPass()
- getHost()
- getPort()
- getPath()
- getQuery()
- getFragment()
- getUserInfo()
- getHostInfo()
- getAuthority()
- toString()
- toArray()

### UriBuilder

- setScheme()
- setUser()
- setUserPass()
- setHost()
- setPort()
- setPath()
- setQuery()
- setFragment()
- toUri()

### UriNormalizer

- static normalize() - Returns Uri object with normalized component values


## Usage


### Uri

```php
$uri = new \FmLabs\Uri\Uri('http://www.example.org/test?q=hello#world);
$schema = $uri->getSchema(); // "http"
$host = $uri->getHost(); // "www.example.org"
$path = $uri->getPath(); // "/test"
$frag = $uri->getFragment(); // "world"
```

### UriBuilder

```php
$builder = new \FmLabs\Uri\UriBuilder();
$builder
    ->setSchema('http')
    ->setHost('www.example.org')
    ->setPort(8080)
    ->setPath('/my/path')
;
$uri = $builder->toUri();
$uri->toString(); // http://www.example.org:8080/my/path
```

### UrlNormalizer

```php
$uri = \FmLabs\Uri\UriNormalizer::normalize('hTTp://www.eXample.org:80/test./../foo/../bar);
$uri->toString(); // http://www.example.org/test/foo/bar);
```

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
- UriNormalizer: Add more known default ports
- UriBuilder: Build and set query string from query data array
- PHP: Upgrade to 7.1
- PHPUnit: Upgrade to PHPUnit7
- Project: Add LICENSE

## Changelog

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

Free to use



