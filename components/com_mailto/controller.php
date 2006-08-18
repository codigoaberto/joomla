<?php
/**
 * @version $Id$
 * @package Joomla
 * @subpackage MailTo
 * @copyright Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved.
 * @license GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

jimport('joomla.application.controller');

/**
 * @package Joomla
 * @subpackage MailTo
 */
class mailtoController extends JController {
	/**
	 * Mail the link
	 */
	function send() {
		$db			= &$this->getDBO();
		$mainframe	= &$this->getApplication();

		jimport( 'joomla.utilities.mail' );

		$SiteName 	= $mainframe->getCfg('sitename');
		$MailFrom 	= $mainframe->getCfg('mailfrom');
		$FromName 	= $mainframe->getCfg('fromname');

		$link 		= urldecode( JRequest::getVar( 'link', '', 'post' ) );

		// probably a spoofing attack
		if (!JUtility::spoofCheck()) {
			JError::raiseWarning( 403, JText::_( 'E_SESSION_TIMEOUT' ) );
			return false;
		}

		/*
		 * Protect against simple spoofing attacks
		 */
		if (!JUtility::spoofCheck()) {
			header("HTTP/1.0 403 Forbidden");
			die( JText::_( 'E_SESSION_TIMEOUT' ) );
		}

		// An array of e-mail headers we do not want to allow as input
		$headers = array ('Content-Type:',
						  'MIME-Version:',
						  'Content-Transfer-Encoding:',
						  'bcc:',
						  'cc:');

		// An array of the input fields to scan for injected headers
		$fields = array ('mailto',
						 'sender',
						 'from',
						 'subject',
						 );

		/*
		 * Here is the meat and potatoes of the header injection test.  We
		 * iterate over the array of form input and check for header strings.
		 * If we fine one, send an unauthorized header and die.
		 */
		foreach ($fields as $field)
		{
			foreach ($headers as $header)
			{
				if (strpos($_POST[$field], $header) !== false)
				{
					header("HTTP/1.0 403 Forbidden");
					die( JText::_( 'ALERTNOTAUTH' ) );
					exit;
				}
			}
		}

		/*
		 * Free up memory
		 */
		unset ($headers, $fields);

		$email 				= JRequest::getVar( 'mailto', '', 'post' );
		$sender 			= JRequest::getVar( 'sender', '', 'post' );
		$from 				= JRequest::getVar( 'from', '', 'post' );
		$subject_default 	= sprintf(JText::_('Item sent by'), $sender);
		$subject 			= JRequest::getVar( 'subject', $subject_default, 'post' );

		if (!$email || !$from || (JMailHelper::isEmailAddress($email) == false) || (JMailHelper::isEmailAddress($from) == false))
		{
			JContentView :: userInputError(JText :: _('EMAIL_ERR_NOINFO'));
		}

		/*
		 * Build the link to send in the email
		 */
		$link = sefRelToAbs($link);

		/*
		 * Build the message to send
		 */
		$msg = sprintf(JText :: _('_EMAIL_MSG'), $SiteName, $sender, $from, $link);

		/*
		 * Send the email
		 */
		mosMail($from, $sender, $email, $subject, $msg);

		$this->setViewName( 'sent' );
		$this->display();
	}
}
?>