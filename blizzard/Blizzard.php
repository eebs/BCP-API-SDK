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

namespace blizzard;

use \blizzard\Exception;

include_once 'Exception.php';

/**
 * Blizzard package and API manager.
 *
 * @author		Miles Johnson
 * @copyright	Blizzard Entertainment
 * @version		0.1.0
 * @package		blizzard
 */
class Blizzard {

	/**
	 * API key.
	 *
	 * @access private
	 * @var string
	 * @static
	 */
	private static $__apiKey = null;

	/**
	 * Region.
	 *
	 * @access private
	 * @var string
	 * @static
	 */
	private static $__region = 'us';

	/**
	 * Return the globally set API key.
	 *
	 * @access public
	 * @return string
	 * @static
	 * @final
	 */
	final public static function getApiKey() {
		return self::$__apiKey;
	}

	/**
	 * Return the globally set region.
	 *
	 * @access public
	 * @return string
	 * @static
	 * @final
	 */
	final public static function getRegion() {
		return self::$__region;
	}

	/**
	 * Return the officially supported regions.
	 *
	 * @access public
	 * @return string
	 * @static
	 * @final
	 */
	final public static function getSupportedRegions() {
		return array('us', 'eu', 'kr', 'tw', 'cn');
	}

	/**
	 * Set the API key.
	 *
	 * @access public
	 * @param string $key
	 * @return void
	 * @static
	 * @final
	 */
	final public static function setApiKey($key) {
		self::$__apiKey = (string)$key;
	}

	/**
	 * Set the region value; must be one of the supported regions.
	 *
	 * @access public
	 * @param string $region
	 * @return void
	 * @static
	 * @final
	 */
	final public static function setRegion($region) {
		$region = strtolower($region);

		if (!in_array($region, self::getSupportedRegions())) {
			throw new Exception(sprintf('The region %s is not supported.', $region));
		}

		self::$__region = $region;
	}

}