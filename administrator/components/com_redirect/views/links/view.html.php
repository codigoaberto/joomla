<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of redirection links.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_redirect
 * @since		1.6
 */
class RedirectViewLinks extends JView
{
	protected $state;
	protected $items;
	protected $pagination;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Assign data to the view.
		$this->assignRef('state', $state);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assign('enabled', RedirectHelper::isEnabled());

		parent::display($tpl);
		$this->_setToolbar();
	}

	/**
	 * Setup the Toolbar.
	 */
	protected function _setToolbar()
	{
		$state	= $this->get('State');
		$canDo	= RedirectHelper::getActions();

		JToolBarHelper::title(JText::_('Redir_Manager_Links'), 'redirect');
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('link.add');
		}
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('link.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::custom('links.publish', 'publish.png', 'publish_f2.png', 'JToolbar_Enable', true);
			JToolBarHelper::custom('links.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JToolbar_Disable', true);
			JToolBarHelper::divider();
			if ($state->get('filter.published') != -1) {
				JToolBarHelper::archiveList('links.archive');
			}
		}
		if ($state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'links.delete');
		}
		else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('links.trash');
		}
		if ($canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_redirect');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help('screen.redirect');
	}
}