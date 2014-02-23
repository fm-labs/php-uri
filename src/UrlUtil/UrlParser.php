<?php
namespace UrlUtil;

/**
 * Class UrlParser
 */
class UrlParser {

/**
 * @var string
 */
	protected $_url;

/**
 * @var string
 */
	protected $_scheme;

/**
 * @var string
 */
	protected $_user;

/**
 * @var string
 */
	protected $_pass;

/**
 * @var string
 */
	protected $_host;

/**
 * @var int
 */
	protected $_port;

/**
 * @var string
 */
	protected $_path;

/**
 * @var string
 */
	protected $_query;

/**
 * @var array
 */
	protected $_queryData = array();

/**
 * @var string
 */
	protected $_fragment;

/**
 * @param string $url
 */
	public function __construct($url = null) {
		if ($url) {
			$this->setUrl($url);
		}
	}

/**
 * @param string $url
 * @return $this
 */
	public function setUrl($url) {
		$this->_url = $url;
		$this->_parseUrl();
		return $this;
	}

/**
 * Component Setter
 *
 * @param string $key
 * @param mixed $val
 * @return void
 */
	protected function _set($key, $val) {
		$prop = '_' . $key;
		$this->{$prop} = $val;
	}

/**
 * Parse Url components
 *
 * @return void
 */
	protected function _parseUrl() {
		$components = parse_url($this->_url);

		foreach (array('scheme', 'user', 'pass', 'host', 'port', 'path', 'fragment', 'query') as $c) {
			if (isset($components[$c])) {
				$this->_set($c, $components[$c]);
			} else {
				$this->_set($c, '');
			}
		}

		if (isset($components['query'])) {
			parse_str($components['query'], $this->_queryData);
		}
	}

	public function getComponents() {
		return array(
			'scheme' => $this->_scheme,
			'user' => $this->_user,
			'pass' => $this->_pass,
			'host' => $this->_host,
			'port' => $this->_port,
			'path' => $this->_path,
			'fragment' => $this->_fragment,
			'query' => $this->_query
		);
	}

	public function getScheme() {
		return $this->_scheme;
	}

	public function getHost() {
		return $this->_host;
	}

	public function getPort() {
		return $this->_port;
	}

	public function getPath() {
		return $this->_path;
	}

	public function getQuery() {
		return $this->_query;
	}

	public function getQueryData($key = null) {
		if ($key === null) {
			return $this->_queryData;
		}

		if ($this->hasQueryData($key)) {
			return $this->_queryData[$key];
		}

		return null;
	}

	public function hasQueryData($key) {
		return isset($this->_queryData[$key]);
	}

	public function getFragment() {
		return $this->_fragment;
	}

	public function getAuthUser() {
		return $this->_user;
	}

	public function getAuthPass() {
		return $this->_pass;
	}

	public function getHostTld() {
		// @todo Implement me
		return '';
	}

	public function getHostIp() {
		// @todo Implement me
		return '127.0.0.1';
	}

/**
 * Get Url
 *
 * @return string
 */
	public function getUrl() {
		// scheme
		$scheme = '';
		if ($this->_scheme) {
			$scheme = $this->_scheme . '://';
		}

		// auth
		$auth = '';
		if ($this->_user && $this->_pass) {
			$auth = $this->_user . ':' . $this->_pass . '@';
		} elseif ($this->_user) {
			$auth = $this->_user . '@';
		}

		// host
		$host = $this->_host;
		if ($this->_port) {
			$host .= ':' . $this->_port;
		}

		// path
		$path = $this->_path;

		// query
		$query = '';
		if ($this->_query) {
			$query = '?' . $this->_query;
		}

		// fragment
		$fragment = '';
		if ($this->_fragment) {
			$fragment = '#' . $this->_fragment;
		}

		return $scheme . $auth . $host . $path . $query . $fragment;
	}

/**
 * @return string
 */
	public function getRawUrl() {
		return $this->_url;
	}

	public function __toString() {
		return $this->getUrl();
	}
}