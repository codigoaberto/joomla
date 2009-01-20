<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @subpackage	Banners
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * @package		Joomla
 * @subpackage	Banners
 */
class BannerTableClient extends JTable
{
	var	$cid				= null;
	var $name				= '';
	var $contact			= '';
	var $email				= '';
	var $extrainfo			= '';
	var $checked_out		= 0;
	var $checked_out_time	= 0;
	var $editor				= '';

	function __construct(&$_db) {
		parent::__construct('#__bannerclient', 'cid', $_db);
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check()
	{
		// check for valid client name
		if (trim($this->name == '')) {
			$this->setError(JText::_('BNR_CLIENT_NAME'));
			return false;
		}

		// check for valid client contact
		if (trim($this->contact == '')) {
			$this->setError(JText::_('BNR_CONTACT'));
			return false;
		}

		// check for valid client email
		jimport('joomla.mail.helper');
		if (!JMailHelper::isEmailAddress($this->email)) {
			$this->setError(JText::_('BNR_VALID_EMAIL'));
			return false;
		}

		return true;
	}
}