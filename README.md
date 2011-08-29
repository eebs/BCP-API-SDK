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
	use \blizzard\Blizzard;
	use \blizzard\api\wow\RealmApi;

	Blizzard::setApiKey('yourPublicApiKey', 'yourPrivateApiKey');
	Blizzard::setRegion('us');

	// Instance
	$realm = new RealmApi(array(
		'publicKey'		=> 'yourPublicApiKey',
		'privateKey'	=> 'yourPrivateApiKey',
		'region'		=> 'us'
	));

### 2 - Using the source APIs ###

Each type of API call will have an associated class: realm, character, guild, etc. You may instantiate any of these classes to fetch the data you desire. Once instantiated, use the results() method to return the default result set.

	use \blizzard\api\wow\RealmApi;

	$realm = new RealmApi();
	$results = $realm->results();
	
### 3 - Adding addition query parameters ###

Some classes have optional query parameters that may be set. The character resource has some optional fields like guild, stats, talents, items. (For the full list see the official documentation: http://blizzard.github.com/api-wow-docs/)
You may add these options fields to their respective classes.

	use \blizzard\api\wow\GuildApi;

	$guild = new GuildApi(array(
		'guild' => 'guildName',
		'realm' => 'realmName'
	));
	$guild->setQueryParam('fields', 'members,achievements');
	$results = $guild->results();

### 4 - Filtering the results ###

Each class will have a set of filter methods built in that you may use to filter down the result set. Additionally, you can use the other built in methods to modify the result set to your needs.

	use \blizzard\api\wow\RealmApi;

	$realm = new RealmApi();
	$results = $realm->filterByStatus(RealmApi::STATUS_DOWN);
	$results = $realm->filterByName(array('Lightbringer', 'Tichondrius'));

### 5 - Caching your data ###

By default, every API call will be cached in memory depending on the filter parameters provided. This speeds up the data mining process by not triggering the same HTTP request over and over for the exact same data. Cached items will last for the duration of the HTTP request. If you want to keep an indefinite cache, you can provide your own caching engine. Your custom caching engine must implement the blizzard\cache\CacheInterface.

	// Custom cache engine
	class MemcacheEngine extends \blizzard\cache\CacheInterface { 
		// Overwrite get(), set(), has(), key()
	}

	// Use your class
	$realm = new RealmApi();
	$realm->setCacheEngine(new MemcacheEngine());

### 6 - Using the Last Modified Header ###

In order to limit the number of requests you make to the API server, you can use the If-Modified-Since header. You set this header (or any other header) in the config array that is passed to the API class when it is instantiated. If the data has not changed since the time you sent in the header, rather than returning the data an HTTP 304 response is sent back. If the data has changed, it is returned as usual.

	use \blizzard\api\wow\CharacterApi;
	
	$config = array(
		'character'	=> 'Nissel',
		'realm'		=> 'nerzhul',
		'headers'	=> 'If-Modified-Since: ' . date(DATE_RFC2822, 1314634561),
	);

	$character = new CharacterApi($config);
	$results = $character->results();
	
	if($character->isModified()){
		// Do something with $results
	}else{
		// Fetch from your database
		// or maybe do nothing
	}
	
You can check to see if any results were returned when using the If-Modified-Since header by using the isModified() method. It simply returns true or false. Note that you should not try to use the results without performing this check, as there may not be any results. If you want to force a return of the data, simply do not include the If-Modified-Since header. Also note that the header must be assigned to the 'headers' key of the configuration array, or it must be part of an array of header strings assigned to the 'headers' key.
	
## Examples ##

### Arena Ladder API ###

	use \blizzard\api\wow\ArenaLadderApi;

	$config = array(
		'teamsize'		=> ArenaLadderApi::SIZE_2V2,
		'battlegroup'	=> 'Bloodlust',
	);
	$arena = new ArenaLadderApi($config);

Valid values for team size are:

* SIZE_2V2
* SIZE_3V3
* SIZE_5V5

Returns all ladder results, defaults to the first 50 entries.
Note that for filterBy methods, you do not need to call results() first, it is automatically called.

	$allArenaLadderResults = $arena->results();

Returns all alliance arena teams in the result set.

	$arenaFactionResults = $arena->filterByFaction(ArenaLadderApi::FAC_ALLIANCE);

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
		'teamsize'		=> ArenaTeamApi::SIZE_2V2,
		'teamname'		=> 'Dragonslayer Dispels',
	);
	$arena = new ArenaTeamApi($config);

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

	$auction = new AuctionApi($config);

Returns the meta info for the realm Ner'zhul.

	$metaAuctionResults = $auction->results();
	
Returns all auctions for the realm Ner'zhul.
Note that for filterBy methods, you do not need to call results() first, it is automatically called.

	$allAuctionResults = $auction->getAuctions();

Returns all horde auctions.

	$factionResults = $auction->filterByFaction(AuctionApi::FAC_HORDE);

Valid values for factions are:

* FAC_ALLIANCE
* FAC_HORDE
* FAC_NEUTRAL

Returns all auctions owned by 'Nissel' on the realm Ner'zhul.

	$nameResults = $auction->filterByName('Nissel');

Returns all auctions for item id 59219. This will return items for all auction houses, with no differentiation between factions.
To only return items from a specific faction, see the section titled "Additional Filtering".

	$itemResults = $auction->filterByItem('59219');

Returns all auctions with a time left of Long.

	$timeResults = $auction->filterByTimeLeft(AuctionApi::TIME_LONG);

Valid values for time left are:

* TIME_VERY_LONG
* TIME_LONG
* TIME_MEDIUM
* TIME_SHORT

#### Additional Filtering ####

The base ApiAbstract class contains a method for performing additional filtering on an array.
If you were interested in a specific item, but only in horde auction houses, you would need to do the following.

	use \blizzard\api\wow\AuctionApi;

	$config = array(
		'realm'		=> 'nerzhul',
	);

	$auction = new AuctionApi($config);
	$hordeResults = $auction->filterByFaction(AuctionApi::FAC_HORDE);
	$hordeItemResults = $auction->filter($hordeResults, 'item', '52987');

ApiAbstract::filter($results, $key, $filter) takes in three parameters. $results is the array to filter on. Typically this will be an array of arrays, with the inner arrays representing items, auctions, character, realms, etc. $key is the key you want to search , and $filter is the value to match. For example, auctions have the key 'item' that dictates which item is being auctioned. If you specify 'item' as the key, it will match $filter against the value in the 'item' element.
Because AuctionApi extends ApiAbstract, filter can be called from AuctionApi.

If you wanted to filter based on a range, or check if a value is higher or lower than a specific number, you can use a callback function passed into ApiAbstract::filter()'s $filter parameter.
Here is an example of returning all auctions for item '52987' with a buyout greater than 649900.

	$itemResults = $auction->filterByItem('52987');

	$callback = function($value){
		return ($value > 649900);
	};
	$callbackResults = $auction->filter($itemResults, 'buyout', $callback);

If you wanted to return all auctions in a range you could do the following.

	$itemResults = $auction->filterByItem('52987');

	$low = 500000;
	$high = 700000;
	$callback = function($value) use ($low, $high){
		return ($low < $value && $value < $high);
	};
	$callbackResults = $auction->filter($itemResults, 'buyout', $callback);

Note that the callback function must return true or false. It should return true if the value passed in should be in the result set, and false if it should not. Also note that this additional filtering can be done on any result set from any API class, not just AuctionApi.

### Character API ###

	use \blizzard\api\wow\CharacterApi;

	$config = array(
		'character'	=> 'Nissel',
		'realm'		=> 'nerzhul',
	);

	$character = new CharacterApi($config);

You can set addition query parameters by using the setQueryParam() method.
This will return the character's guild info, what items they are wearing, and additional stats.
For a full list of character fields see the [Blizzard Documentation](http://blizzard.github.com/api-wow-docs/#id3380301).

	$character->setQueryParam('fields', 'items,guild,stats');

Returns character data for the character Nissel on the realm Ner'zhul with any additional data specified by setQueryParam().

	$characterResults = $character->results();

### Data API ###

	use \blizzard\api\wow\DataApi;

	$data = new DataApi();

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

	$guild = new GuildApi($config);

Returns guild information for the guild Mìdnight Chaos on the realm Ner'zhul.

	$guildResults = $guild->results();

### Realm API ###

	use \blizzard\api\wow\RealmApi;

	$realm = new RealmApi();

Returns all realm info.

	$realmResults = $realm->results();

Returns realm information for the realm Ner'zhul.

	$nameResults = $realm->filterByName("Ner'zhul");

Returns realm information for the realm Ner'zhul.

	$slugResults = $realm->filterBySlug('nerzhul');

Returns all realms with a High population.

	$populationResults = $realm->filterByPopulation(RealmApi::POP_HIGH);

Valid values for population are:

* POP_LOW
* POP_MEDIUM
* POP_HIGH

Returns all realms with a queue.

	$queueResults = $realm->filterByQueue(RealmApi::QUEUE_YES);

Valid values for queue are:

* QUEUE_YES
* QUEUE_NO

Returns all realms with a status of up.

	$statusResults = $realm->filterByStatus(RealmApi::STATUS_UP);

Valid values for status are:

* STATUS_UP
* STATUS_DOWN

Returns all realms with a type of PvE.

	$typeResults = $realm->filterByType(RealmApi::TYPE_PVE);

Valid values for type are:

* TYPE_PVE
* TYPE_PVP
* TYPE_RP
* TYPE_RPPVP

## Todo ##

* Any other API as it becomes available

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
