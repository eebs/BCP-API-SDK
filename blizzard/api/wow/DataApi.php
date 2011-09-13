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

namespace blizzard\api\wow;

use \blizzard\api\wow\WowApiAbstract;

include_once 'WowApiAbstract.php';

/**
 * API for generic data results: classes, races, guild perks, guild rewards and items.
 *
 * @author		Miles Johnson
 * @copyright	Blizzard Entertainment
 * @version		0.1.0
 * @package		blizzard.api.wow
 */
class DataApi extends WowApiAbstract {
	
	/**
	 * Base WoW API URL.
	 * 
	 * @access protected
	 * @var protected
	 */
	protected $_baseUrl;

	/**
	 * Save the current API url.
	 *
	 * @access public
	 * @param array $config
	 * @return void
	 * @constructor
	 */
	public function __construct(array $config = array()) {
		parent::__construct($config);

		$this->_baseUrl = $this->getApiUrl();
	}

	/**
	 * Return the character classes.
	 * 
	 * @access public
	 * @return array
	 */
	public function getClasses() {
		$this->setApiUrl($this->_baseUrl .'data/character/classes');
		
		return $this->requestData(__METHOD__);
	}

	/**
	 * Return the character races.
	 *
	 * @access public
	 * @return array
	 */
	public function getRaces() {
		$this->setApiUrl($this->_baseUrl .'data/character/races');

		return $this->requestData(__METHOD__);
	}

	/**
	 * Return the guild perks.
	 * 
	 * @access public
	 * @return array
	 */
	public function getGuildPerks() {
		$this->setApiUrl($this->_baseUrl .'data/guild/perks');
		
		return $this->requestData(__METHOD__);
	}

	/**
	 * Return the guild rewards.
	 * 
	 * @access public
	 * @return array
	 */
	public function getGuildRewards() {
		$this->setApiUrl($this->_baseUrl .'data/guild/rewards');
		
		return $this->requestData(__METHOD__);
	}

	/**
	 * Return an item based on ID.
	 * 
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getItem($id) {
		if (empty($id) || !is_numeric($id)) {
			throw new WowApiException(sprintf('Item ID %s invalid for %s.', $id, __METHOD__));
		}
		
		$this->setApiUrl($this->_baseUrl .'item/'. $id);
		
		return $this->requestData(__METHOD__, $id);
	}

	/**
	 * Return the item classes.
	 * 
	 * @access public
	 * @return array
	 */
	public function getItemClasses() {
		$this->setApiUrl($this->_baseUrl .'data/item/classes');
		
		return $this->requestData(__METHOD__);
	}

	/**
	 * Return the battlegroups
	 *
	 * @access public
	 * @return array
	 */
	public function getBattlegroups() {
		$this->setApiUrl($this->_baseUrl .'data/battlegroups/');

		return $this->requestData(__METHOD__);
	}

	/**
	 * Request specific API data and cache the result.
	 * 
	 * @access protected
	 * @param string $method
	 * @param mixed $args
	 * @return array
	 */
	protected function requestData($method, $args = null) {
		$engine = $this->getCacheEngine();
		$key = $engine->key($method, $args);

		if ($engine->has($key)) {
			return $engine->get($key);
		}

		$request = $this->request();
		$results = $request->response();

		$engine->set($key, $results);

		return $results;
	}
	
}