<?php
/**
 * Copyright (c) 2010-2011 Blizzard Entertainment
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace blizzard\cache;

use \blizzard\cache\CacheInterface;

/**
 * Basic caching engine that stores all data in memory for the duration of the request.
 *
 * @author		Miles Johnson
 * @copyright	Blizzard Entertainment
 * @version		0.1.0
 * @package		blizzard.cache
 */
class CacheEngine implements CacheInterface {

	/**
	 * Stored items.
	 *
	 * @access public
	 * @var array
	 */
	protected $_storage = array();

	/**
	 * Get a cached item.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		if ($this->has($key)) {
			return $this->_storage[$key];
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
		return isset($this->_storage[$key]);
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
		$key = (string)$key;

		if (!empty($args) || $args == 0) {
			if (is_array($args)) {
				$key .= '-'. implode('-', $args);
			} else {
				$key .= '-'. $args;
			}
		}

		return preg_replace('/[^a-z0-9\-\_]/is', '_', $key);
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
		$this->_storage[$key] = $value;
	}
	
}