<?php
/**
* @version $Id$
* @package Joomla
* @subpackage Weblinks
* @copyright Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved.
* @license GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

jimport( 'joomla.application.view');

/**
 * HTML View class for the WebLinks component
 *
 * @static
 * @package Joomla
 * @subpackage Weblinks
 * @since 1.0
 */
class WeblinksViewCategories extends JView
{
	function __construct()
	{
		$this->setViewName('categories');
		$this->setTemplatePath(dirname(__FILE__).DS.'tmpl');
	}
	
	function display( )
	{
		global $Itemid;
		
		// Define image tag attributes
		if ($this->params->get('image') != -1)
		{
			$attribs['align'] = '"'. $this->params->get('image_align').'"';
			$attribs['hspace'] = '"6"';

			// Use the static HTML library to build the image tag
			$this->data->image = mosHTML::Image('/images/stories/'.$this->params->get('image'), JText::_('Web Links'), $attribs);
		}
		
		for($i = 0; $i < count($this->categories); $i++)
		{
			$category =& $this->categories[$i];
			$category->link = sefRelToAbs('index.php?option=com_weblinks&amp;task=category&amp;catid='. $category->catid .'&amp;Itemid='. $Itemid);
		}
		
		$this->_loadTemplate('list');
	}
}
?>