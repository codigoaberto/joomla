<?php
/**
 * Library View
 * 
 * @package Joomla
 * @subpackage Installer
 * @author Sam Moffatt <pasamio@gmail.com>
 * @copyright	Copyright (C) 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *  See COPYRIGHT.php for copyright notices and details.
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/** ensure the installerviewdefault is available */
include_once(dirname(__FILE__).DS.'..'.DS.'default'.DS.'view.php');

/**
 * Libraries view
 *
 * @package    Joomla
 * @subpackage Installer
 */
class InstallerViewPackages extends InstallerViewDefault
{
	/**
	 * Display the view
	 * @access public
	 */
    function display($tpl = null)
    {
    	/*
		 * Set toolbar items for the page
		 */
		JToolBarHelper::deleteList( '', 'remove', 'Uninstall' );
		JToolBarHelper::help( 'screen.installer2' );

		// Get data from the model
		$state		= &$this->get('State');
		$items		= &$this->get('Items');
		$pagination	= &$this->get('Pagination');
    	
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('lists',		$lists);
		
        parent::display($tpl);
    }
    
    /**
     * Loads data for an individual item and sets some variables
     * @access public
     */
	function loadItem($index=0)
	{
		$item =& $this->items[$index];
		$item->index	= $index;
/*		$item->img		= $item->enabled ? 'tick.png' : 'publish_x.png';
		$item->task 	= $item->enabled ? 'disable' : 'enable';
		$item->alt 		= $item->enabled ? JText::_( 'Enabled' ) : JText::_( 'Disabled' );
		$item->action	= $item->enabled ? JText::_( 'disable' ) : JText::_( 'enable' );
*/
		if ($item->packagename == 'joomla') {
			$item->cbd		= 'disabled';
			$item->style	= 'style="color:#999999;"';
		} else {
			$item->cbd		= null;
			$item->style	= null;
		}
		$item->author_info = @$item->authorEmail .'<br />'. @$item->authorUrl;

		$this->assignRef('item', $item);
	}    
}
?>