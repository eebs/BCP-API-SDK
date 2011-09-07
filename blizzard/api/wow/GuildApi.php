<?php

namespace blizzard\api\wow;

use \blizzard\api\wow\WowApiAbstract;
use \blizzard\api\wow\WowApiException;

include_once 'WowApiAbstract.php';
include_once 'WowApiException.php';

/**
 * @todo
 *
 * @author		Ebrahim Kobeissi
 * @copyright	Ebrahim Kobeissi
 * @package		blizzard.api.wow
 */
class GuildApi extends WowApiAbstract {

	/**
	 * Append guild and realm to API URL.
	 *
	 * @access public
	 * @param array $config
	 * @return void
	 * @constructor
	 */
	public function __construct(array $config = array()) {
		if (empty($config['guild']) || empty($config['realm'])) {
			throw new WowApiException('Please provide a guild name and realm.');
		}

		$config['guild'] = utf8_encode(str_replace(' ', '%20', $config['guild']));

		parent::__construct($config);

		$this->setApiUrl($this->getApiUrl() . sprintf('guild/%s/%s/', $this->_config['realm'], $this->_config['guild']));
	}

	/**
	 * Store a cache of the base results. We will use this cache to filter upon
	 * instead of doing subsequent HTTP requests.
	 *
	 * @access public
	 * @return boolean
	 * @final
	 */
	final public function cache() {
		if ($this->getCacheEngine()->has($this->_cacheKey)) {
			return true;
		}

		$request = $this->request();
		$results = $request->response();

		if (!empty($results)) {
			$this->getCacheEngine()->set($this->_cacheKey, $results);
			return true;
		}

		return false;
	}

}