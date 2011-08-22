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
class ArenaTeamApi extends WowApiAbstract {

	/**
	 * Constants for team size.
	 */
	const SIZE_2V2 = '2v2';
	const SIZE_3V3 = '3v3';
	const SIZE_5V5 = '5v5';

	/**
	 * Arena Team data structure.
	 *
	 *		size <string> - URL friendly version of the team size.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_schema = array(
		'size' => array(
			self::SIZE_2V2,
			self::SIZE_3V3,
			self::SIZE_5V5,
		),
	);

	/**
	 * Add realm, team size, and team name to the URL.
	 *
	 * @access public
	 * @param array $config
	 * @return void
	 * @constructor
	 */
	public function __construct(array $config = array()) {
		if (empty($config['teamsize']) || empty($config['teamname']) || empty($config['realm'])) {
			throw new WowApiException('Please provide a team name, team size, and realm.');
		}
		
		if (!in_array($config['teamsize'], $this->schema('size'))) {
			throw new WowApiException(sprintf('Invalid arena team size, %s.', $config['teamsize']));
		}

		$config['teamname'] = utf8_encode(str_replace(' ', '%20', $config['teamname']));

		parent::__construct($config);

		$this->setApiUrl($this->getApiUrl() . sprintf('arena/%s/%s/%s/', $this->_config['realm'], $this->_config['teamsize'], $this->_config['teamname']));
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
		if ($this->getCacheEngine()->has(self::CACHE_KEY)) {
			return true;
		}

		$request = $this->request();
		$results = $request->response();

		if (!empty($results)) {
			$this->getCacheEngine()->set(self::CACHE_KEY, $results);
			return true;
		}

		return false;
	}

}