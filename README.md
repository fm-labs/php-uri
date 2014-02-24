php-urlutil
===========

URL Toolbox for PHP


Requirements
------------

- php 5.3


Included
--------

UrlParser
Object-oriented equivalent of parse_str

UrlNormalizer


Usage
------

UrlParser

$url = new UrlParser('http://www.example.org/test?q=hello#world);
$schema = $url->getSchema(); // http
$host = $url->getHost(); // www.example.org
$path = $url->getPath(); // /test
...


UrlNormalizer

$normalizer = new UrlNormalizer('hTTp://www.eXample.org:80/test./../foo/../bar);
$normalizedUrl = $normalizer->normalize()->getUrl(); // http://www.example.org/test/foo/bar);



Licence
-------
Free to use (@TODO set proper licence info)



