<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Joomla! System Remember Me Plugin
 *
 * @package		Joomla
 * @subpackage	System
 */
class plgSystemRemember extends JPlugin
{
	function onAfterInitialise()
	{
		global $mainframe;

		// No remember me for admin
		if ($mainframe->isAdmin()) {
			return;
		}

		$user = &JFactory::getUser();
		if ($user->get('guest'))
		{
			jimport('joomla.utilities.utility');
			$hash = JUtility::getHash('JLOGIN_REMEMBER');

			if ($str = JRequest::getString($hash, '', 'cookie', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM))
			{
				jimport('joomla.utilities.simplecrypt');

				//Create the encryption key, apply extra hardening using the user agent string
				$key = JUtility::getHash(@$_SERVER['HTTP_USER_AGENT']);

				$crypt	= new JSimpleCrypt($key);
				$str	= $crypt->decrypt($str);

				$options = array();
				$options['silent'] = true;
				if (!$mainframe->login(@unserialize($str), $options)) {
					// Clear the remember me cookie
					setcookie(JUtility::getHash('JLOGIN_REMEMBER'), false, time() - 86400, '/');
				}
			}
		}
	}
}