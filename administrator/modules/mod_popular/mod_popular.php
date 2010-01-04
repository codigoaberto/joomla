<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Include the mod_online functions only once.
require_once dirname(__FILE__).'/helper.php';

// Get the logged-in users.
if ($rows = modPopularHelper::getList($params)) {
	// Render the module
	require JModuleHelper::getLayoutPath('mod_popular', $params->get('layout', 'default'));
}