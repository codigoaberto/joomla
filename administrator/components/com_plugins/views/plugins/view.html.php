<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	Config
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Plugins component
 *
 * @static
 * @package		Joomla.Administrator
 * @subpackage	Plugins
 * @since 1.0
 */
class PluginsViewPlugins extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		$db = &JFactory::getDbo();

		$client = JRequest::getWord('filter_client', 'site');

		$filter_order		= $mainframe->getUserStateFromRequest("$option.$client.filter_order",		'filter_order',		'p.folder',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest("$option.$client.filter_order_Dir",	'filter_order_Dir',	'',			'word');
		$filter_state		= $mainframe->getUserStateFromRequest("$option.$client.filter_state",		'filter_state',		'',			'word');
		$filter_type		= $mainframe->getUserStateFromRequest("$option.$client.filter_type", 		'filter_type',		1,			'cmd');
		$search				= $mainframe->getUserStateFromRequest("$option.$client.search",			'search',			'',			'string');
		$search				= JString::strtolower($search);

		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

		$where = '';
		if ($client == 'admin') {
			$where[] = 'p.client_id = 1';
			$client_id = 1;
		} else {
			$where[] = 'p.client_id = 0';
			$client_id = 0;
		}

		// used by filter
		if ($filter_type != 1) {
			$where[] = 'p.folder = '.$db->Quote($filter_type);
		}
		if ($search) {
			$where[] = 'LOWER(p.name) LIKE '.$db->Quote('%'.$db->getEscaped($search, true).'%', false);
		}
		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = 'p.published = 1';
			} else if ($filter_state == 'U') {
				$where[] = 'p.published = 0';
			}
		}

		$where 		= (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		if ($filter_order == 'p.ordering') {
			$orderby = ' ORDER BY p.folder, p.ordering '. $filter_order_Dir;
		} else {
			$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', p.ordering ASC';
		}


		// get the total number of records
		$query = 'SELECT COUNT(*)'
			. ' FROM #__plugins AS p'
			. $where
			;
		$db->setQuery($query);
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		$query = 'SELECT p.*, u.name AS editor, ag.title As groupname'
			. ' FROM #__plugins AS p'
			. ' LEFT JOIN #__users AS u ON u.id = p.checked_out'
			. ' LEFT JOIN #__access_assetgroups AS ag ON ag.id = p.access'
			. $where
			. ' GROUP BY p.id'
			. $orderby
			;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}


		// get list of Positions for dropdown filter
		$query = 'SELECT folder AS value, folder AS text'
			. ' FROM #__plugins'
			. ' WHERE client_id = '.(int) $client_id
			. ' GROUP BY folder'
			. ' ORDER BY folder'
			;
		$types[] = JHtml::_('select.option',  1, '- '. JText::_('Select Type') .' -');
		$db->setQuery($query);
		$types 			= array_merge($types, $db->loadObjectList());
		$lists['type']	= JHtml::_('select.genericlist',   $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $filter_type);

		// state filter
		$lists['state']	= JHtml::_('grid.state',  $filter_state);


		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;

		$this->assign('client',		$client);

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$rows);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}
}