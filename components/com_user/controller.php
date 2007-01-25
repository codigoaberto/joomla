<?php
/**
 * @version		$Id: controller.php 6138 2007-01-02 03:44:18Z eddiea $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

jimport('joomla.application.component.controller');

/**
 * User Component Controller
 *
 * @package		Joomla
 * @subpackage	Weblinks
 * @since 1.5
 */
class UserController extends JController
{
	/**
	 * Method to display a user
	 *
	 * @access	public
	 * @since	1.5
	 */
	function display()
	{
		global $mainframe;

		parent::display();
	}

	function edit()
	{
		global $mainframe, $Itemid, $option;

		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();

		if ( $user->get('guest')) {
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return;
		}

		JRequest::setVar('layout', 'form');

		parent::display();
	}

	function save( )
	{
		//preform token check (prevent spoofing)
		$token	= JUtility::getToken();
		if(!JRequest::getVar( $token, 0, 'post' )) {
			JError::raiseError(403, 'Request Forbidden');
		} 

		$user	 =& JFactory::getUser();
		$userid = JRequest::getVar( 'id', 0, 'post', 'int' );

		// preform security checks
		if ($user->get('id') == 0 || $userid == 0 || $userid <> $user->get('id')) {
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return;
		}

		//clean request
		$post = JRequest::get( 'post' );
		$post['password']	= JRequest::getVar('password', '', 'post', 'string');
		$post['verifyPass']	= JRequest::getVar('verifyPass', '', 'post', 'string');

		// do a password safety check
		if(strlen($post['password'])) { // so that "0" can be used as password e.g.
			if($post['password'] != $post['verifyPass']) {
				JError::raiseError(500, JText::_( 'Passwords do not match', true ) );
			}
		}
		
		// store data
		$model = $this->getModel('user');

		if ($model->store($post)) {
			$msg = JText::_( 'Your settings have been saved.' );
		} else {
			$msg = JText::_( 'Error saving your settings' );
		}

		$this->setRedirect( $_SERVER['HTTP_REFERER'], $msg );
	}

	function cancel() {
		$this->setRedirect( 'index.php' );
	}
}
?>