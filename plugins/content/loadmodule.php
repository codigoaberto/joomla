<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgContentLoadmodule extends JPlugin
{
	/**
	* Plugin that loads module positions within content
	*/
	public function onPrepareContent(&$row, &$params, $page=0)
	{
		$db = JFactory::getDbo();
		// simple performance check to determine whether bot should process further
		if (JString::strpos($row->text, 'loadposition') === false) {
			return true;
		}

	 	// expression to search for
	 	$regex = '/{loadposition\s*.*?}/i';

		// check whether plugin has been unpublished
		if (!$this->params->get('enabled', 1)) {
			$row->text = preg_replace($regex, '', $row->text);
			return true;
		}

	 	// find all instances of plugin and put in $matches
		preg_match_all($regex, $row->text, $matches);

		// Number of plugins
	 	$count = count($matches[0]);

	 	// plugin only processes if there are any instances of the plugin in the text
	 	if ($count) {
			// Get plugin parameters
		 	$style	= $pluginParams->def('style', -2);
	 		$this->_process($row, $matches, $count, $regex, $style);
		}
	}

	protected function _process(&$row, &$matches, $count, $regex, $style)
	{
	 	for ($i=0; $i < $count; $i++)
		{
	 		$load = str_replace('loadposition', '', $matches[0][$i]);
	 		$load = str_replace('{', '', $load);
	 		$load = str_replace('}', '', $load);
 			$load = trim($load);

			$modules	= $this->_load($load, $style);
			$row->text 	= preg_replace('{'. $matches[0][$i] .'}', $modules, $row->text);
	 	}

	  	// removes tags without matching module positions
		$row->text = preg_replace($regex, '', $row->text);
	}

	protected function _load($position, $style=-2)
	{
		$document	= &JFactory::getDocument();
		$renderer	= $document->loadRenderer('module');
		$params		= array('style'=>$style);

		$contents = '';
		foreach (JModuleHelper::getModules($position) as $mod)  {
			$contents .= $renderer->render($mod, $params);
		}
		return $contents;
	}
}
