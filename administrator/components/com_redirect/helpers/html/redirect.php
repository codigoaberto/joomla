<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Utility class for creating HTML Grids
 *
 * @static
 * @package 	Joomla.Administrator
 * @subpackage	com_redirect
 * @since		1.6
 */
class JHtmlRedirect
{
	/**
	 * @param	int $value	The state value.
	 * @param	int $i
	 * @param	string		An optional prefix for the task.
	 * @param	boolean		An optional setting for access control on the action.
	 */
	public static function published($value = 0, $i, $canChange = true)
	{
		// Array of image, task, title, action
		$states	= array(
			1	=> array('tick.png',		'links.unpublish',	'JState_Enabled',	'JState_Disable_Item'),
			0	=> array('publish_x.png',	'links.publish',		'JState_Disabled',	'JState_Enabled_Item'),
			2	=> array('disabled.png',	'links.unpublish',	'JState_Archived',	'JState_Disable_Item'),
			-2	=> array('trash.png',		'links.publish',		'JState_Trashed',	'JState_Enabled_Item'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[0]);
		$html	= JHtml::_('image.administrator', $state[0], '/templates/bluestork/images/admin/', null, '/templates/bluestork/admin/images/', JText::_($state[2]));
		if ($canChange) {
			$html	= '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'
					. $html.'</a>';
		}

		return $html;
	}
}