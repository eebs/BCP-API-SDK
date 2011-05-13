<?php
/**
 * Copyright (c) 2010-2011 Blizzard Entertainment
 *
 * Permission is herefilterBy granted, free of charge, to any person obtaining a copy
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
use \blizzard\api\wow\WowApiException;

include_once 'WowApiAbstract.php';
include_once 'WowApiException.php';

/**
 * @todo
 *
 * @author		Miles Johnson
 * @copyright	Blizzard Entertainment
 * @version		0.1.0
 * @package		blizzard.api.wow
 */
class CharacterApi extends WowApiAbstract {

	/**
	 * Append character and realm to API URL.
	 *
	 * @access public
	 * @param array $config
	 * @return void
	 * @constructor
	 */
	public function __construct(array $config = array()) {
		if (empty($config['character']) || empty($config['realm'])) {
			throw new WowApiException('Please provide a character name and realm.');
		}

		parent::__construct($config);

		$this->setApiUrl($this->getApiUrl() . sprintf('character/%s/%s/', $config['realm'], $config['character']));
	}

}