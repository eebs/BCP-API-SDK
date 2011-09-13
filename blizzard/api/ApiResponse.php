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

namespace blizzard\api;

/**
 * Instantiated upon every API request to process the current response.
 * Will convert JSON strings into usable arrays.
 *
 * @author		Miles Johnson
 * @copyright	Blizzard Entertainment
 * @version		0.1.0
 * @package		blizzard.api
 */
class ApiResponse {

	/**
	 * Headers returned from the cURL request.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_headers;

	/**
	 * The raw response; usually a JSON string.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_raw;

	/**
	 * The processed response; usually an array.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_response;

	/**
	 * Load and parse the response and headers.
	 *
	 * @access public
	 * @param string $response
	 * @param array $headers
	 * @return void
	 * @constructor
	 */
	public function __construct($response, $headers) {
		$this->_raw = $response;
		$this->_headers = $headers;

		if (strpos($headers['content_type'], 'application/json') !== false) {
			$this->_response = json_decode($response, true);
		} else {
			$this->_response = (array) $response;
		}
	}

	/**
	 * Return a header value.
	 *
	 * @access public
	 * @param string $key
	 * @return string|null
	 */
	public function header($key) {
		return isset($this->_headers[$key]) ? $this->_headers[$key] : null;
	}

	/**
	 * Return all headers.
	 *
	 * @access public
	 * @return array
	 */
	public function headers() {
		return $this->_headers;
	}

	/**
	 * Return the raw response.
	 *
	 * @access public
	 * @return string
	 */
	public function raw() {
		return $this->_raw;
	}

	/**
	 * Return the processed response.
	 *
	 * @access public
	 * @return array
	 */
	public function response() {
		return $this->_response;
	}

}