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
class AuctionApi extends WowApiAbstract {

	/**
	 * Constants for factions.
	 */
	const FAC_HORDE		= 'horde';
	const FAC_ALLIANCE	= 'alliance';
	const FAC_NEUTRAL	= 'neutral';

	/**
	 * Constants for auction time left.
	 */
	const TIME_VERY_LONG	= 'VERY_LONG';
	const TIME_LONG			= 'LONG';
	const TIME_MEDIUM		= 'MEDIUM';
	const TIME_SHORT		= 'SHORT';

	/**
	 * Auction data structure.
	 *
	 *		timeleft <string> - Remaining time for the auction.
	 *		faction <string> - Horde, Alliance, or Neutral.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_schema = array(
		'timeleft' => array(
			self::TIME_VERY_LONG,
			self::TIME_LONG,
			self::TIME_MEDIUM,
			self::TIME_SHORT
		),
		'faction' => array(
			self::FAC_HORDE,
			self::FAC_ALLIANCE,
			self::FAC_NEUTRAL,
		),
	);

	/**
	 * Last modified time for auctions.
	 * 
	 * @access protected
	 * @var protected
	 */
	protected $_lastModified;

	/**
	 * Append guild and realm to API URL.
	 *
	 * @access public
	 * @param array $config
	 * @return void
	 * @constructor
	 * @throws WoWApiException
	 */
	public function __construct(array $config = array()) {
		if (empty($config['realm'])) {
			throw new WowApiException('Please provide a realm name.');
		}

		parent::__construct($config);

		$this->setApiUrl($this->getApiUrl() . sprintf('auction/data/%s/', $this->_config['realm']));
	}

	/**
	 * Return the auction data, and set the lastModified property.
	 *
	 * @access protected
	 * @return void
	 * @throws WoWApiException
	 */
	public function getAuctions(){
		$results = $this->results();
		if(!isset($results['files'])){
			throw new WowApiException('No results were returned. Check for usage of If-Modified-Since.');
		}
		$this->setApiUrl($results['files'][0]['url']);
		
		$engine = $this->getCacheEngine();
		$key = $engine->key($this->_cacheKey . 'auctions');

		if ($engine->has($key)) {
			return $engine->get($key);
		}
		
		$request = $this->request();
		$results = $request->response();

		if (!empty($results)) {
			$engine->set($key, $results);
		}

		return $results;
	}

	/**
	 * Get auction(s) by faction
	 *
	 * @access public
	 * @param string|array $faction
	 * @return array
	 */
	public function filterByFaction($faction) {
		if (!in_array($faction, $this->schema('faction'))) {
			throw new WowApiException(sprintf('Invalid faction for %s.', __METHOD__));
		}

		$this->cache();
		$engine = $this->getCacheEngine();
		$results = $engine->get($this->_cacheKey);

		if(is_array($faction)){
			$resultArray = array();
			foreach($faction as $single){
				$resultArray[] = $results[$single];
			}
			return $resultsArray;
		}else{
			return $results[$faction]['auctions'];
		}
	}

	/**
	 * Get auction(s) based on owner name.
	 *
	 * @access public
	 * @param string|array $name
	 * @return array
	 */
	public function filterByName($name) {
		if (empty($name)) {
			throw new WowApiException(sprintf('Name required for %s.', __METHOD__));
		}
		
		return $this->filterAuctionsBy(__METHOD__, 'owner', $name);
	}

	/**
	 * Get auction(s) based on item id.
	 *
	 * @access public
	 * @param string|array $item
	 * @return array
	 */
	public function filterByItem($item) {
		if (empty($item)) {
			throw new WowApiException(sprintf('ItemId required for %s.', __METHOD__));
		}

		return $this->filterAuctionsBy(__METHOD__, 'item', $item);
	}

	/**
	 * Get auction(s) based on time left.
	 *
	 * @access public
	 * @param string|array $timeleft
	 * @return array
	 */
	public function filterByTimeLeft($timeleft) {
		if (!in_array($timeleft, $this->schema('timeleft'))) {
			throw new WowApiException(sprintf('Invalid time left type for %s.', __METHOD__));
		}
		
		return $this->filterAuctionsBy(__METHOD__, 'timeLeft', $timeleft);
	}

	/**
	 * Used internally to filter auctions from the cache.
	 * Used because the structure of auction data is not able to be
	 * passed into the main filterBy() method. The alliance, horde, and
	 * neutral auction data needs to be merged, then filtered on.
	 *
	 * @access protected
	 * @param string $method The method called from, used as the cache key
	 * @param string $field The field upon which to search for $filter
	 * @param string|array $filter The data to be searched for
	 * @return array
	 */
	protected function filterAuctionsBy($method, $field, $filter){
		$this->getAuctions();
		$engine = $this->getCacheEngine();

		$key = $engine->key($method, $filter);

		if ($engine->has($key)) {
			return $engine->get($key);
		}

		$fullResults = $engine->get($this->_cacheKey . 'auctions');

		$filterOn = array_merge($fullResults['alliance']['auctions'], $fullResults['horde']['auctions'], $fullResults['neutral']['auctions']);

		$filteredResults = $this->filter($filterOn, $field, $filter);
		$engine->set($key, $filteredResults);
		return $filteredResults;
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