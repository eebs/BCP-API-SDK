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

use \blizzard\Exception;
use \blizzard\api\ApiException;
use \blizzard\api\wow\DataApi;
use \blizzard\api\wow\WowApiException;

include_once '../../tests.php';
include_once '../../../api/wow/DataApi.php';

try {
	// Instantiate the Data API
	$data = new DataApi();

	// Get classes
	debug($data->getClasses());

	// Get guild perks
	debug($data->getGuildPerks());

	// Get guild rewards
	debug($data->getGuildRewards());

	// Get races
	debug($data->getRaces());

	// Get item by ID
	debug($data->getItem(49623));
	
} catch (WowApiException $e) {
	debug($e->getMessage());
	
} catch (ApiException $e) {
	debug($e->getMessage());

} catch (Exception $e) {
	debug($e->getMessage());
}