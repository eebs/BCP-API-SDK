<?php

namespace blizzard\cache;

use \blizzard\cache\CacheInterface;

class MemcacheEngine implements CacheInterface {

	/**
	 * Memcached instance
	 *
	 * @access protected
	 * @var Memcached
	 */
	protected $_memcache;

	/**
	 * Timeout value in seconds, or unix time.
	 * Defaults to 300 seconds.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_timeout = 300;

	/**
	 * Temporary variable to hold value fetched from has()
	 *
	 * @access protected
	 * @var mixed
	 */
	protected $_temp;

	public function __construct(){
		$this->_memcache = new \Memcached;
	}

	/**
	 * Get a cached item.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		if ($this->has($key)) {
			return $this->_temp;
		}

		return null;
	}

	/**
	 * Check if a cached item exists.
	 *
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		$this->_temp = $this->_memcache->get($key);
		return (false !== $this->_temp);
	}

	/**
	 * Format the cache key.
	 *
	 * @access public
	 * @param string $key
	 * @param mixed $args
	 * @return string
	 */
	public function key($key, $args = null) {
		$key = str_replace('::', '.', (string) $key);

		if (!empty($args) || $args === 0) {
			if (is_array($args)) {
				$key .= '-'. implode('-', $args);
			} else {
				$key .= '-'. $args;
			}
		}
		return $key;
		return preg_replace('/[^a-z0-9\-\_]/is', '.', $key);
	}

	/**
	 * Set a cached item.
	 *
	 * @access public
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public function set($key, $value) {
		$this->_memcache->set($key, $value, $this->_timeout);
	}

	/**
	 * Sets the timeout to be used for storing values.
	 * Defaults to 300 seconds
	 *
	 * @access public
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public function setTimeout($timeout){
		if(!is_int($timeout)){
			throw new WowApiException('Timeout must be an integer');
		}
		
		$this->_timeout = $timeout;
	}

    /**
     * Proxy method calls to the memcached instance, thing like flush() etc
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, array $arguments){
        return call_user_func_array(array($this->_memcache, $method), $arguments);
    }
}