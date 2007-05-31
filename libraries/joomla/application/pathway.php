<?php
/**
* @version		$Id$
* @package		Joomla.Framework
* @subpackage	Application
* @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.filter.output');

/**
 * Class to maintain a pathway.
 *
 * Main example of use so far is the mod_breadcrumbs module that keeps track of
 * the user's navigated path within the Joomla application.
 *
 * @author		Louis Landry <louis.landry@joomla.org>
 * @package 	Joomla.Framework
 * @subpackage	Application
 * @since		1.5
 */
class JPathWay extends JObject
{

	/**
	 * Array to hold the pathway item objects
	 * @access private
	 */
	var $_pathway = null;

	/**
	 * Integer number of items in the pathway
	 * @access private
	 */
	var $_count = 0;

	/**
	 * Class constructor
	 */
	function __construct() {
	}

	/**
	 * Return the JPathWay items array
	 *
	 * @access public
	 * @return array Array of pathway items
	 * @since 1.5
	 */
	function getPathWay()
	{
		$pw = $this->_pathway;

		// Use array_values to reset the array keys numerically
		return array_values($pw);
	}

	/**
	 * Create and return an array of the pathway names.
	 *
	 * @access public
	 * @return array Array of names of pathway items
	 * @since 1.5
	 */
	function getPathWayNames()
	{
		// Initialize variables
		$names = array (null);

		// Build the names array using just the names of each pathway item
		foreach ($this->_pathway as $item) {
			$names[] = $item->name;
		}

		//Use array_values to reset the array keys numerically
		return array_values($names);
	}

	/**
	 * Create and add an item to the pathway.
	 *
	 * @access public
	 * @param string $name
	 * @param string $link
	 * @return boolean True on success
	 * @since 1.5
	 */
	function addItem($name, $link='')
	{
		// Initalize variables
		$ret = false;

		if ($this->_pathway[] = $this->_makeItem($name, $link)) {
			$ret = true;
			$this->_count++;
		}

		return $ret;
	}

	/**
	 * Set item name.
	 *
	 * @access public
	 * @param integer $id
	 * @param string $name
	 * @return boolean True on success
	 * @since 1.5
	 */
	function setItemName($id, $name)
	{
		// Initalize variables
		$ret = false;

		if (isset($this->_pathway[$id])) {
			$this->_pathway[$id]->name = $name;
			$ret = true;
		}

		return $ret;
	}

	/**
	 * Create and return a new pathway object.
	 *
	 * @access private
	 * @param string $name Name of the item
	 * @param string $link Link to the item
	 * @return object Pathway item object
	 * @since 1.5
	 */
	function _makeItem($name, $link)
	{
		$item = new stdClass();
		$item->name = JOutputFilter::ampReplace(html_entity_decode($name));
		$item->link = $link;
		
		return $item;
	}
}