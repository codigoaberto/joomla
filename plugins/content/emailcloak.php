<?php
/**
 * @version		$Id$
 * @package		oomla
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgContentEmailcloak extends JPlugin
{

	/**
	 * Plugin that cloaks all emails in content from spambots via Javascript.
	 *
	 * @param object|string An object with a "text" property or the string to be
	 * cloaked.
	 * @param array Additional parameters. See {@see plgEmailCloak()}.
	 * @param int Optional page number. Unused. Defaults to zero.
	 * @return boolean True on success.
	 */
	public function onPrepareContent(&$row, &$params, $page=0)
	{
		if (is_object($row)) {
			return $this->_cloak($row->text, $params);
		}
		return $this->_cloak($row, $params);
	}

	/**
	 * Genarate a search pattern based on link and text.
	 *
	 * @param string The target of an e-mail link.
	 * @param string The text enclosed by the link.
	 * @return string A regular expression that matches a link containing the
	 * parameters.
	 */
	protected function _getPatern ($link, $text) {
		$pattern = '~(?:<a [\w "\'=\@\.\-]*href\s*=\s*"mailto:'
			. $link . '"[\w "\'=\@\.\-]*)>' . $text . '</a>~i';
		return $pattern;
	}

	/**
	 * Cloak all emails in text from spambots via Javascript.
	 *
	 * @param string The string to be cloaked.
	 * @param array Additional parameters. Parameter "mode" (integer, default 1)
	 * replaces addresses with "mailto:" links if nonzero.
	 * @return boolean True on success.
	 */
	protected function _cloak(&$text, &$params)
	{
		/*
		 * Check for presence of {emailcloak=off} which is explicits disables this
		 * bot for the item.
		 */
		if (JString::strpos($text, '{emailcloak=off}') !== false) {
			$text = JString::str_ireplace('{emailcloak=off}', '', $text);
			return true;
		}

		// Simple performance check to determine whether bot should process further.
		if (JString::strpos($text, '@') === false) {
			return true;
		}

		$mode = $this->params->def('mode', 1);

		// any@email.address.com
		$searchEmail = '([\w\.\-]+\@(?:[a-z0-9\.\-]+\.)+(?:[a-z0-9\-]{2,4}))';
		// any@email.address.com?subject=anyText
		$searchEmailLink = $searchEmail . '([?&][\x20-\x7f][^"<>]+)';
		// anyText
		$searchText = '([\x20-\x7f][^<>]+)';

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com"
		 * >email@amail.com</a>
		 */
		$pattern = $this->_getPattern($searchEmail, $searchEmail);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
			$mail = $regs[1][0];
			$mailText = $regs[2][0];

			// Check to see if mail text is different from mail addy
			$replacement = JHtml::_('email.cloak', $mail, $mode, $mailText);

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com">
		 * anytext</a>
		 */
		$pattern = $this->_getPattern($searchEmail, $searchText);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
			$mail = $regs[1][0];
			$mailText = $regs[2][0];

			$replacement = JHtml::_('email.cloak', $mail, $mode, $mailText, 0);

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com?
		 * subject=Text">email@amail.com</a>
		 */
		$pattern = $this->_getPattern($searchEmailLink, $searchEmail);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
			$mail = $regs[1][0] . $regs[2][0];
			$mailText = $regs[3][0];
			// Needed for handling of Body parameter
			$mail = str_replace('&amp;', '&', $mail);

			// Check to see if mail text is different from mail addy
			$replacement = JHtml::_('email.cloak', $mail, $mode, $mailText);

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com?
		 * subject=Text">anytext</a>
		 */
		$pattern = $this->_getPattern($searchEmailLink, $searchText);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
			$mail = $regs[1][0] . $regs[2][0];
			$mailText = $regs[3][0];
			// Needed for handling of Body parameter
			$mail = str_replace('&amp;', '&', $mail);

			$replacement = JHtml::_('email.cloak', $mail, $mode, $mailText, 0);

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		// Search for plain text email@amail.com
		$pattern = '~' . $searchEmail . '([^a-z0-9]|$)~i';
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
			$mail = $regs[1][0];
			$replacement = JHtml::_('email.cloak', $mail, $mode);

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[1][1], strlen($mail));
		}
		return true;
	}
}
