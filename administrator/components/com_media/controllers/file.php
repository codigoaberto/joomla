<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Weblinks Weblink Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since 1.5
 */
class MediaControllerFile extends MediaController
{

	/**
	 * Upload a file
	 *
	 * @since 1.5
	 */
	function upload()
	{

		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JInvalid_Token'));

		$app	= &JFactory::getApplication();
		$file 	= JRequest::getVar('Filedata', '', 'files', 'array');
		$folder	= JRequest::getVar('folder', '', '', 'path');
		$format	= JRequest::getVar('format', 'html', '', 'cmd');
		$return	= JRequest::getVar('return-url', null, 'post', 'base64');
		$err	= null;

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name']	= JFile::makeSafe($file['name']);

		if (isset($file['name'])) {
			$filepath = JPath::clean(COM_MEDIA_BASE.DS.$folder.DS.strtolower($file['name']));

			if (!MediaHelper::canUpload($file, $err)) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'Invalid: '.$filepath.': '.$err));
					header('HTTP/1.0 415 Unsupported Media Type');
					jexit('Error. Unsupported Media Type!');
				} else {
					JError::raiseNotice(100, JText::_($err));
					// REDIRECT
					if ($return) {
						$app->redirect(base64_decode($return).'&folder='.$folder);
					}
					return;
				}
			}

			if (JFile::exists($filepath)) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'File already exists: '.$filepath));
					header('HTTP/1.0 409 Conflict');
					jexit('Error. File already exists');
				} else {
					JError::raiseNotice(100, JText::_('Error. File already exists'));
					// REDIRECT
					if ($return) {
						$app->redirect(base64_decode($return).'&folder='.$folder);
					}
					return;
				}
			}

			if (!JFile::upload($file['tmp_name'], $filepath)) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'Cannot upload: '.$filepath));
					header('HTTP/1.0 400 Bad Request');
					jexit('Error Unable to upload file');
				} else {
					JError::raiseWarning(100, JText::_('ERROR_UNABLE_TO_UPLOAD_FILE'));
					// REDIRECT
					if ($return) {
						$app->redirect(base64_decode($return).'&folder='.$folder);
					}
					return;
				}
			} else {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance();
					$log->addEntry(array('comment' => $folder));
					jexit('Upload complete');
				} else {
					$app->enqueueMessage(JText::_('UPLOAD_COMPLETE'));
					// REDIRECT
					if ($return) {
						$app->redirect(base64_decode($return).'&folder='.$folder);
					}
					return;
				}
			}
		} else {
			$app->redirect('index.php', 'Invalid Request', 'error');
		}
	}

	/**
	 * Deletes paths from the current path
	 *
	 * @param string $listFolder The image directory to delete a file from
	 * @since 1.5
	 */
	function delete()
	{
		JRequest::checkToken('request') or jexit(JText::_('JInvalid_Token'));

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Get some data from the request
		$app	= &JFactory::getApplication();
		$tmpl	= JRequest::getCmd('tmpl');
		$paths	= JRequest::getVar('rm', array(), '', 'array');
		$folder = JRequest::getVar('folder', '', '', 'path');

		// Initialise variables.
		$msg = array();
		$ret = true;

		if (count($paths)) {
			foreach ($paths as $path)
			{
				if ($path !== JFile::makeSafe($path)) {
					JError::raiseWarning(100, JText::_('Unable to delete:').htmlspecialchars($path, ENT_COMPAT, 'UTF-8').' '.JText::_('WARNFILENAME'));
					continue;
				}

				$fullPath = JPath::clean(COM_MEDIA_BASE.DS.$folder.DS.$path);
				if (is_file($fullPath)) {
					$ret |= !JFile::delete($fullPath);
				} else if (is_dir($fullPath)) {
					$files = JFolder::files($fullPath, '.', true);
					$canDelete = true;
					foreach ($files as $file) {
						if ($file != 'index.html') {
							$canDelete = false;
						}
					}
					if ($canDelete) {
						$ret |= !JFolder::delete($fullPath);
					} else {
						JError::raiseWarning(100, JText::_('Unable to delete:').$fullPath.' '.JText::_('Not Empty!'));
					}
				}
			}
		}
		if ($tmpl == 'component') {
			// We are inside the iframe
			$app->redirect('index.php?option=com_media&view=mediaList&folder='.$folder.'&tmpl=component');
		} else {
			$app->redirect('index.php?option=com_media&folder='.$folder);
		}
	}
}
