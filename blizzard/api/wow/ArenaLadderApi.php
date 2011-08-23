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
class ArenaLadderApi extends WowApiAbstract {

	/**
	 * Constants for team size.
	 */
	const SIZE_2V2 = '2v2';
	const SIZE_3V3 = '3v3';
	const SIZE_5V5 = '5v5';

	/**
	 * Constants for factions.
	 */
	const FAC_HORDE		= 'horde';
	const FAC_ALLIANCE	= 'alliance';

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
		'faction' => array(
			self::FAC_HORDE,
			self::FAC_ALLIANCE,
		),
	);

	/**
	 * Add team size, and battlegroup to the URL.
	 *
	 * @access public
	 * @param array $config
	 * @return void
	 * @constructor
	 */
	public function __construct(array $config = array()) {
		if (empty($config['teamsize']) || empty($config['battlegroup'])) {
			throw new WowApiException('Please provide a team size and battlegroup.');
		}
		
		if (!in_array($config['teamsize'], $this->schema('size'))) {
			throw new WowApiException(sprintf('Invalid arena team size, %s.', $config['teamsize']));
		}

		parent::__construct($config);

		$this->setApiUrl($this->getApiUrl() . sprintf('pvp/arena/%s/%s/', $this->_config['battlegroup'], $this->_config['teamsize']));
	}

	/**
	 * Get teams(s) based on realm.
	 *
	 * @access public
	 * @param string|array $realm
	 * @return array
	 */
	public function filterByRealm($realm) {
		if (empty($realm)) {
			throw new WowApiException(sprintf('Realm required for %s.', __METHOD__));
		}

		return $this->filterBy(__METHOD__, 'realm', $realm);
	}

	/**
	 * Get teams(s) based on name.
	 *
	 * @access public
	 * @param string|array $name
	 * @return array
	 */
	public function filterByName($name) {
		if (empty($name)) {
			throw new WowApiException(sprintf('Name required for %s.', __METHOD__));
		}

		return $this->filterBy(__METHOD__, 'name', $name);
	}

	/**
	 * Get teams(s) based on faction.
	 *
	 * @access public
	 * @param string|array $faction
	 * @return array
	 */
	public function filterByFaction($faction) {
		if (!in_array($faction, $this->schema('faction'))) {
			throw new WowApiException(sprintf('Invalid faction, %s.', $config['teamsize']));
		}

		return $this->filterBy(__METHOD__, 'side', $faction);
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
			$this->getCacheEngine()->set(self::CACHE_KEY, $results['arenateam']);
			return true;
		}

		return false;
	}
}