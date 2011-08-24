# BCP-API-SDK #

An unofficial Blizzard PHP SDK to interact with the World of Warcraft API. This is a continuation of the BlizzardSDK-PHP library which has been discontinued (to my knowledge).
I cloned the Blizzard repo before it disappeared and I am continuing developement on this SDK.

## Requirements ##

* PHP 5.3.x
* JSON - http://php.net/manual/book.json.php
* cURL - http://php.net/manual/book.curl.php

### Manual Installation ###

Download and extract the contents and the resulting "blizzard" folder to your servers vendors directory.

	https://github.com/eebs/BCP-API-SDK/zipball/master

### GIT Installation ###

Clone the repo into your servers vendors directory.

	git clone git://github.com/eebs/BCP-API-SDK.git bcp-api-sdk

## Usage ##

### 1 - Setting your region and API key ###

You may set your API key (optional) and region (defaults to "us") globally by using the Blizzard class or you can overwrite on a per instance basis. These settings will be used for all API calls.

	// Globally
	\blizzard\Blizzard::setApiKey('yourPublicApiKey', 'yourPrivateApiKey');
	\blizzard\Blizzard::setRegion('us');

	// Instance
	$realm = new \blizzard\api\wow\RealmApi(array(
		'publicKey' => 'yourPublicApiKey',
		'privateKey' => 'yourPrivateApiKey',
		'region' => 'us'
	));

### 2 - Using the source APIs ###

Each type of API call will have an associated class: realm, character, guild, etc. You may instantiate any of these classes to fetch the data you desire. Once instantiated, use the results() method to return the default result set.

	$realm = new \blizzard\api\wow\RealmApi();
	$results = $realm->results();
	
### 3 - Adding addition query parameters ###

Some classes have optional query parameters that may be set. The character resource has some optional fields like guild, stats, talents, items. (For the full list see the official documentation: http://blizzard.github.com/api-wow-docs/)
You may add these options fields to their respective classes.

	use \blizzard\api\wow\GuildApi;

	$guild = new \blizzard\api\wow\GuildApi(array(
		'guild' => 'guildName',
		'realm' => 'realmName'
	));
	$guild->setQueryParam('fields', 'members,achievements');
	$results = $guild->results();

### 4 - Filtering the results ###

Each class will have a set of filter methods built in that you may use to filter down the result set. Additionally, you can use the other built in methods to modify the result set to your needs.

	use \blizzard\api\wow\RealmApi;

	$realm = new \blizzard\api\wow\RealmApi();
	$results = $realm->filterByStatus(RealmApi::STATUS_DOWN);
	$results = $realm->filterByName(array('Lightbringer', 'Tichondrius'));

### 5 - Caching your data ###

By default, every API call will be cached in memory depending on the filter parameters provided. This speeds up the data mining process by not triggering the same HTTP request over and over for the exact same data. Cached items will last for the duration of the HTTP request. If you want to keep an indefinite cache, you can provide your own caching engine. Your custom caching engine must implement the blizzard\cache\CacheInterface.

	// Custom cache engine
	class MemcacheEngine extends \blizzard\cache\CacheInterface { 
		// Overwrite get(), set(), has(), key()
	}

	// Use your class
	$realm = new \blizzard\api\wow\RealmApi();
	$realm->setCacheEngine(new MemcacheEngine());

## Examples ##

### Arena Ladder API ###

	use \blizzard\api\wow\ArenaLadderApi;

	$config = array(
		'teamsize'		=> \blizzard\api\wow\ArenaLadderApi::SIZE_2V2,
		'battlegroup'	=> 'Bloodlust',
	);
	$arena = new blizzard\api\wow\ArenaLadderApi($config);

Valid values for team size are:

* SIZE_2V2
* SIZE_3V3
* SIZE_5V5

Returns all ladder results, defaults to the first 50 entries.
Note that for filterBy methods, you do not need to call results() first, it is automatically called.

	$allArenaLadderResults = $arena->results();

Returns all alliance arena teams in the result set.

	$arenaFactionResults = $arena->filterByFaction(\blizzard\api\wow\ArenaLadderApi::FAC_ALLIANCE);

Valid values for factions are:

* FAC_ALLIANCE
* FAC_HORDE

Returns all arena teams on the realm Ner'zhul.

	$arenaRealmResults = $arena->filterByRealm('nerzhul');

Returns all arena teams with the name 'Team Name'.

	$arenaNameResults = $arena->filterByName('Team Name');

### Arena Team API ###

	use \blizzard\api\wow\ArenaTeamApi;

	$config = array(
		'realm'			=> 'eredar',
		'teamsize'		=> \blizzard\api\wow\ArenaTeamApi::SIZE_2V2,
		'teamname'		=> 'Dragonslayer Dispels',
	);
	$arena = new blizzard\api\wow\ArenaTeamApi($config);

Valid values for team size are:

* SIZE_2V2
* SIZE_3V3
* SIZE_5V5

Returns the 2v2 arena team 'Dragonslayer Dispels' on the realm Eredar.

	$arenaTeamResults = $arena->results();

### Auction API ###

	use \blizzard\api\wow\AuctionApi;

	$config = array(
		'realm'		=> 'nerzhul',
	);

	$auction = new blizzard\api\wow\AuctionApi($config);

Returns all auctions for the realm Ner'zhul.
Note that for filterBy methods, you do not need to call results() first, it is automatically called.

	$allAuctionResults = $auction->results();

Returns all horde auctions.

	$factionResults = $auction->filterByFaction(\blizzard\api\wow\AuctionApi::FAC_HORDE);

Valid values for factions are:

* FAC_ALLIANCE
* FAC_HORDE
* FAC_NEUTRAL

Returns all auctions owned by 'Nissel' on the realm Ner'zhul.

	$nameResults = $auction->filterByName('Nissel');

Returns all auctions for item id 59219.

	$itemResults = $auction->filterByItem('59219');

Returns all auctions with a time left of Long.

	$timeResults = $auction->filterByTimeLeft(\blizzard\api\wow\AuctionApi::TIME_LONG);

Valid values for time left are:

* TIME_VERY_LONG
* TIME_LONG
* TIME_MEDIUM
* TIME_SHORT

Returns the last modified time for the realm's auctions.

	$lastModified = $auction->getLastModified();

### Character API ###

	use \blizzard\api\wow\CharacterApi;

	$config = array(
		'character'	=> 'Nissel',
		'realm'		=> 'nerzhul',
	);

	$character = new blizzard\api\wow\CharacterApi($config);

You can set addition query parameters by using the setQueryParam() method.
This will return the character's guild info, what items they are wearing, and additional stats.
For a full list of character fields see the [Blizzard Documentation](http://blizzard.github.com/api-wow-docs/#id3380301).

	$character->setQueryParam('fields', 'items,guild,stats');

Returns character data for the character Nissel on the realm Ner'zhul with any additional data specified by setQueryParam().

	$characterResults = $character->results();

### Data API ###

	use \blizzard\api\wow\DataApi;

	$data = new blizzard\api\wow\DataApi();

Returns the character classes.

	$data->getClasses();

Returns the character races.

	$data->getRaces();

Returns the guild perks.

	$data->getGuildPerks();

Returns the guild rewards.

	$data->getGuildRewards();

Returns an item based on ID.

	$data->getItem('59219');

Returns the item classes.

	$data->getItemClasses();

Returns the battlegroups.

	$data->getBattlegroups();

### Guild API ###

	use \blizzard\api\wow\GuildApi;

	$config = array(
		'guild'	=> 'Mìdnight Chaos',
		'realm'	=> 'nerzhul',
	);

	$guild = new blizzard\api\wow\GuildApi($config);

Returns guild information for the guild Mìdnight Chaos on the realm Ner'zhul.

	$guildResults = $guild->results();

### Realm API ###

	use \blizzard\api\wow\RealmApi;

	$realm = new blizzard\api\wow\RealmApi();

Returns all realm info.

	$realmResults = $realm->results();

Returns realm information for the realm Ner'zhul.

	$nameResults = $realm->filterByName("Ner'zhul");

Returns realm information for the realm Ner'zhul.

	$slugResults = $realm->filterBySlug('nerzhul');

Returns all realms with a High population.

	$populationResults = $realm->filterByPopulation(\blizzard\api\wow\RealmApi::POP_HIGH);

Valid values for population are:

* POP_LOW
* POP_MEDIUM
* POP_HIGH

Returns all realms with a queue.

	$queueResults = $realm->filterByQueue(\blizzard\api\wow\RealmApi::QUEUE_YES);

Valid values for queue are:

* QUEUE_YES
* QUEUE_NO

Returns all realms with a status of up.

	$statusResults = $realm->filterByStatus(\blizzard\api\wow\RealmApi::STATUS_UP);

Valid values for status are:

* STATUS_UP
* STATUS_DOWN

Returns all realms with a type of PvE.

	$typeResults = $realm->filterByType(\blizzard\api\wow\RealmApi::TYPE_PVE);

Valid values for type are:

* TYPE_PVE
* TYPE_PVP
* TYPE_RP
* TYPE_RPPVP

## Todo ##

* Support Last Modified Headers (I currently have very limited understanding of this)
* Extend Auction API to allow for additional filtering
* Any API

## License ##

Copyright (c) 2010-2011 Blizzard Entertainment

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
