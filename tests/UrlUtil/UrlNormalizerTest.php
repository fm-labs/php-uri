<?php
require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class UrlNormalizerTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		parent::setUp();
	}

	public function assertEqual($actual, $expected) {
		$this->assertEquals($expected, $actual);
	}

	public function testLowercaseScheme() {
		$n = new TestUrlNormalizer('HTTP://example.org/');
		$this->assertEqual($n->normalize()->getUrl(), 'http://example.org/');
	}

	public function testLowercaseHost() {
		$n = new TestUrlNormalizer('http://eXamPLE.oRg/');
		$this->assertEqual($n->normalize()->getUrl(), 'http://example.org/');
	}

	public function testRemoveDefaultPorts() {
		$n = new TestUrlNormalizer('http://example.org:80/');
		$this->assertEqual($n->normalize()->getUrl(), 'http://example.org/');

		$n = new TestUrlNormalizer('http://example.org:8080/');
		$this->assertEqual($n->normalize()->getUrl(), 'http://example.org:8080/');

		$n = new TestUrlNormalizer('https://example.org:443/');
		$this->assertEqual($n->normalize()->getUrl(), 'https://example.org/');

		$n = new TestUrlNormalizer('https://example.org:9443/');
		$this->assertEqual($n->normalize()->getUrl(), 'https://example.org:9443/');
	}

	public function testAddTrailingSlash() {
		$n = new TestUrlNormalizer('http://example.org/');
		$this->assertEqual($n->normalize()->getUrl(), 'http://example.org/');

		$n = new TestUrlNormalizer('http://example.org');
		$this->assertEqual($n->normalize()->getUrl(), 'http://example.org/');

		$n = new TestUrlNormalizer('http://example.org/test');
		$this->assertEqual($n->normalize()->getUrl(), 'http://example.org/test/');

		$n = new TestUrlNormalizer('http://example.org/test?q=1');
		$this->assertEqual($n->normalize()->getUrl(), 'http://example.org/test/?q=1');

		$n = new TestUrlNormalizer('http://example.org/test?q=1#frag');
		$this->assertEqual($n->normalize()->getUrl(), 'http://example.org/test/?q=1#frag');
	}

	public function testCapitalizeEscapeSequences() {
		$url = 'http://www.example.com/a%c2%b1b/';
		$expected = 'http://www.example.com/a%C2%B1b/';

		$n = new TestUrlNormalizer($url);
		$this->assertEqual($n->normalize()->getUrl(), $expected);
	}

	public function testDecodeUnreservedCharsHelperMethod() {
		$tests = array(
			'%41' => 'A',
			'%42' => 'B',
			'%5A' => 'Z',
			'%61' => 'a',
			'%62' => 'b',
			'%7A' => 'z',
			'%30' => 0,
			'%39' => 9,
			'%2D' => chr(0x2D), // hyphen
			'%2E' => chr(0x2E), // period
			'%5F' => chr(0x5F), // underscore
			'%7E' => chr(0x7E), // tilde
		);

		foreach ($tests as $encoded => $unencoded) {
			$n = new TestUrlNormalizer();
			$this->assertEqual($n->decodeUnreservedChars($encoded), $unencoded);
		}
	}

	public function testDecodePercentEncodedUnreservedCharacters() {
		$tests = array(
			'http://www.example.com/%2Dusername/' => 'http://www.example.com/-username/',
			'http://www.example.com/%2Eusername/' => 'http://www.example.com/.username/',
			'http://www.example.com/%5Fusername/' => 'http://www.example.com/_username/',
			'http://www.example.com/%7Eusername/' => 'http://www.example.com/~username/'
		);

		foreach ($tests as $test => $expected) {
			$n = new TestUrlNormalizer($test);
			$this->assertEqual($n->normalize()->getUrl(), $expected);
		}
	}

	public function testRemoveDotSegments() {
		//$this->skipIf(true, 'Implement me');
	}

	public function testAllQueryNormalizations() {
		//$this->skipIf(true, 'Implement all the missing query normalization methods and tests');
	}

	public function testAllSemanticChangesNormalizations() {
		//$this->skipIf(true, 'Implement all the missing semantic-changes methods and tests');
	}

}

class TestUrlNormalizer extends \UrlUtil\UrlNormalizer {

	public function decodeUnreservedChars($string) {
		return $this->_decodeUnreservedChars($string);
	}
}