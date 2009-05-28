<?php
/**
 * @version		$Id: weblink.php 11875 2009-05-28 08:34:48Z eddieajau $
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

/**
 * Utility class for creating HTML Grids
 *
 * @static
 * @package 	Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class JHtmlCategories
{
	/**
	 * @param	int $value	The state value
	 * @param	int $i
	 */
	function state($value = 0, $i)
	{
		// Array of image, task, title, action
		$states	= array(
			1	=> array('tick.png',		'categories.unpublish',	'JState_Published',			'JState_UnPublish_Item'),
			0	=> array('publish_x.png',	'categories.publish',	'JState_UnPublished',		'JState_Publish_Item'),
			-2	=> array('trash.png',		'categories.publish',	'JState_Trashed',			'JState_Publish_Item'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[0]);
		$html	= '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'
				. JHtml::_('image.administrator', $state[0], '/images/', null, '/images/', JText::_($state[2])).'</a>';

		return $html;
	}

	/**
	 * Display an HTML select list of state filters
	 *
	 * @param	int $selected	The selected value of the list
	 * @return	string			The HTML code for the select tag
	 * @since	1.6
	 */
	function filterstate($selected)
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '*', JText::_('JSelect_Any'));
		$options[]	= JHtml::_('select.option', '1', JText::_('JState_Published'));
		$options[]	= JHtml::_('select.option', '0', JText::_('JState_UnPublished'));
		$options[]	= JHtml::_('select.option', '-2', JText::_('JState_Trashed'));

		return JHTML::_('select.genericlist', $options, 'filter_state',
			array(
				'list.attr' => 'class="inputbox" onchange="this.form.submit();"',
				'list.select' => $selected
			)
		);
	}
}
