<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.controller' );

/**
 * The Menu List Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @since		1.6
 */
class MenusControllerMenus extends JController
{
	/**
	 * Display the view
	 */
	public function display()
	{
	}

	/**
	 * Proxy for getModel
	 */
	function &getModel($name = 'Menu', $prefix = 'MenusModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
	 * Removes an item
	 */
	public function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Get items to remove from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_('JError_No_items_selected'));
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if (!$model->delete($cid)) {
				$this->setMessage($model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_menus&view=menus');
	}

	/**
	 * Rebuild the menu tree.
	 *
	 * @return	bool	False on failure or error, true on success.
	 */
	public function rebuild()
	{
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$this->setRedirect('index.php?option=com_menus&view=menus');

		// Initialise variables.
		$model = &$this->getModel('Item');

		if ($model->rebuild()) {
			// Reorder succeeded.
			$this->setMessage(JText::_('Menus_Rebuild_success'));
			return true;
		} else {
			// Rebuild failed.
			$this->setMessage(JText::sprintf('Menus_Rebuild_failed', $model->getMessage()));
			return false;
		}
	}

	/**
	 * Temporary method. This should go into the 1.5 to 1.6 upgrade routines.
	 */
	public function resync()
	{
		// Initialise variables.
		$db = JFactory::getDbo();
		$parts = null;

		// Load a lookup table of all the component id's.
		$components = $db->setQuery(
			'SELECT element, extension_id' .
			' FROM #__extensions' .
			' WHERE type = '.$db->quote('component')
		)->loadAssocList('element', 'extension_id');

		if ($error = $db->getErrorMsg()) {
			return JError::raiseWarning(500, $error);
		}

		// Load all the component menu links
		$items = $db->setQuery(
			'SELECT id, link, component_id' .
			' FROM #__menu' .
			' WHERE type = '.$db->quote('component')
		)->loadObjectList();

		if ($error = $db->getErrorMsg()) {
			return JError::raiseWarning(500, $error);
		}

		foreach ($items as $item) {
			// Parse the link.
			parse_str(parse_url($item->link, PHP_URL_QUERY), $parts);

			// Tease out the option.
			if (isset($parts['option'])) {
				$option = $parts['option'];

				// Lookup the component ID
				if (isset($components[$option])) {
					$componentId = $components[$option];
				} else {
					// Mismatch. Needs human intervention.
					$componentId = -1;
				}

				// Check for mis-matched component id's in the menu link.
				if ($item->component_id != $componentId) {
					// Update the menu table.
					$log = "Link $item->id refers to $item->component_id, converting to $componentId ($item->link)";
					echo "<br/>$log";

					$db->setQuery(
						'UPDATE #__menu' .
						' SET component_id = '.$componentId.
						' WHERE id = '.$item->id
					)->query();
					//echo "<br>".$db->getQuery();

					if ($error = $db->getErrorMsg()) {
						return JError::raiseWarning(500, $error);
					}
				}
			}
		}
	}
}