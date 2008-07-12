<?php
/**
* @version		$Id: sef.php 9764 2007-12-30 07:48:11Z ircmaxell $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin');
jimport( 'joomla.user.authorization');
JLoader::register('JAuthorization', JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'user'.DS.'authorization.php');

/**
* Joomla! SEF Plugin
*
* @package 		Joomla
* @subpackage	System
*/
class plgSystemjacl extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param	object		$subject The object to observe
	  * @param 	array  		$config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemJacl(&$subject, $config)  {
		parent::__construct($subject, $config);
	}

	/**
     * Converting the site URL to fit to the HTTP request
     */
	function onAfterInitialise()
	{
		$config =& JFactory::getConfig();
		$config->setValue('config.aclservice', 'jacl');
		return true;
	}
}

/**
 * Class that handles all access authorization
 *
 * @package 	Joomla.Framework
 * @subpackage	Application
 * @since		1.5
 */
class JAuthorizationJACL extends JAuthorization
{
	var $_rights = array();

	var $_ugroups = array();

	var $_cgroups = array();

	/**
	 * Constructor
	 * @param array An arry of options to oeverride the class defaults
	 */
	function __construct( $options = NULL ) 
	{
		if(!count($this->_rights))
		{
			$this->_getAllowedActions();
		}
	}

	/**
	 * Hands the access query over to the actual ACL engine
	 *
	 * This is the function that is used for the access check
	 * @param string The extension the action belongs to [optional]
	 * @param string The action to check for [optional]
	 * @param string The extension that the XObject belongs to [optional]
	 * @param string The XObject [optional]
	 * @param string The user to check for. If not provided, the current user is used [optional]
	 * @return boolean
	 */
	function authorize( $extension, $action, $contentitem = null, $user = null)
	{
		if($user == null) {
			$user = JFactory::getUser();
			$user = $user->get('id');
		}

		if(is_null($contentitem)) {
			if(isset($this->_rights[$user][$extension][$action])) {
				return true;
			} else {
				return false;
			}
		} else {
			if(!is_array($this->_rights[$user][$extension][$action])) {
 				$this->_getAllowedContent($extension, $action);
			} else {
				if(isset($this->_rights[$user][$extension][$action][$contentitem])) {
					return true;
				} else {
					return false;
				}
			}
		}		
	}

	function getAllowedContent($extension, $action, $user = null)
	{
		$content = array();
		if($user == null) {
			$user = JFactory::getUser();
			$user = $user->get('id');
		}
		if(!is_array($this->_rights[$user][$extension][$action])) {
			$this->_getAllowedContent($extension, $action, $user);
		}
		if(count($this->_rights[$user][$extension][$action])) {
			foreach($this->_rights[$user][$extension][$action] as $name => $value)
			{
				$content[] = $value;
			}
		}
		return $content;
	}

	/**
	 * Grabs all groups mapped to a user
	 *
	 * @param integer root-group-ID to start from/group ID to get informations from
	 * @param string The user whose group to return. If not provided, current user is used [optional]
	 * @return array
	 */
	function getUserGroups( $user )
	{
		if(!count($this->_ugroups[$user]))
		{
			$db = JFactory::getDBO();
			$query = 'SELECT DISTINCT g2.id'
					.' FROM #__core_acl_aro o,'
					.' #__core_acl_groups_aro_map gm,'
					.' #__core_acl_aro_groups g1,'
					.' #__core_acl_aro_groups g2'
					.' WHERE (o.section_value=\'users\' AND o.value=\''.$user.'\')'
					.' AND gm.aro_id=o.id AND g1.id=gm.group_id AND (g2.lft <= g1.lft AND g2.rgt >= g1.rgt)';
			$db->setQuery($query);
			$this->_ugroups[$user] = $db->loadResultArray();
		}

		return $this->_ugroups[$user];
	}



	function _getAllowedActions($user = null)
	{
		if($user == null) {
			$user = JFactory::getUser();
			$user = $user->get('id');
		}
		$db =& JFactory::getDBO();
		$groups = $this->getUserGroups($user);
	
		if (is_array($groups) AND !empty($groups)) {
			$groups = implode(',', $groups);
		}
		$query = 'SELECT aco_map.section_value as extension, aco_map.value as action FROM #__core_acl_aco_map aco_map'
				.' LEFT JOIN #__core_acl_acl acl ON aco_map.acl_id = acl.id'
				.' LEFT JOIN #__core_acl_aro_groups_map aro_group ON acl.id = aro_group.acl_id'
				.' LEFT JOIN #__core_acl_axo_map axo ON axo.acl_id = acl.id'
				.' LEFT JOIN #__core_acl_axo_groups_map axo_group ON acl.id = axo_group.acl_id'
				.' WHERE (aro_group.group_id IN ('.$groups.')) && (acl.allow = 1) && (axo.section_value IS NULL AND axo.value IS NULL)';

		$db->setQuery($query);
		$results = $db->loadObjectList();

		foreach($results as $result)
		{
			$this->_rights[$user][$result->extension][$result->action] = true;
		}
	}

	function _getAllowedContent($extension, $action, $user = null)
	{
		if($user == null) {
			$user = JFactory::getUser();
			$user = $user->get('id');
		}
		$db =& JFactory::getDBO();
		$groups = $this->getUserGroups($user);

		if (is_array($groups) AND !empty($groups)) {
			$groups = implode(',', $groups);
		} else {
			$groups = array('2');
		}

		$query = 'SELECT aco_map.section_value as extension, aco_map.value as action, axo.value as contentitem'
			.' FROM #__core_acl_aco_map aco_map'
			.' LEFT JOIN #__core_acl_acl acl ON aco_map.acl_id = acl.id'
			.' LEFT JOIN #__core_acl_aro_groups_map aro_group ON acl.id = aro_group.acl_id'
			.' LEFT JOIN #__core_acl_axo_map axo ON axo.acl_id = acl.id'
			.' LEFT JOIN #__core_acl_axo_groups_map axo_group ON acl.id = axo_group.acl_id'
			.' WHERE (aro_group.group_id IN ('.$groups.')) && (acl.allow = 1) && (axo.section_value = \''.$extension.'\') && (aco_map.value = \''.$action.'\')';
		$db->setQuery($query);
		$results = $db->loadObjectList();
		if(count($results))
		{
			foreach($results as $result)
			{
				$this->_rights[$user][$result->extension][$result->action][$result->contentitem] = true;
			}
		} else {
			$this->_rights[$user][$extension][$action] = array();
		}
	}
}

class JAuthorizationJACLUsergroup
{
	function __construct()
	{

	}

	function getParent()
	{
		
	}

	function getChildren()
	{

	}

	function addChild()
	{

	}

	function setName()
	{

	}

	function getName()
	{

	}

	function getID()
	{

	}

	function setID()
	{

	}

	function removeChild()
	{

	}

	function load()
	{

	}

	function store()
	{

	}

	function remove()
	{

	}

	function getMembers()
	{

	}

	function addMember()
	{

	}

	function removeMember()
	{

	}

	function getUsergroups()
	{

	}
}

class JAuthorizationJACLRule
{
	function __construct()
	{

	}

	function load()
	{

	}

	function store()
	{

	}

	function remove()
	{

	}

	function getRules()
	{

	}

	function getGroups()
	{
		
	}

	function addGroup()
	{

	}

	function removeGroup()
	{

	}

	function getActions()
	{
		
	}

	function addAction()
	{

	}

	function removeAction()
	{

	}

	function getContentItems()
	{

	}

	function addContentItem()
	{

	}

	function removeContentItem()
	{

	}


	function getID()
	{

	}

	function setID()
	{

	}

	function allow()
	{

	}
}

class JAuthorizationJACLAction
{
	function __construct()
	{

	}

	function load()
	{

	}

	function store()
	{

	}

	function remove()
	{

	}

	function getExtension()
	{
		
	}

	function setExtension()
	{

	}

	function getName()
	{

	}

	function setName()
	{

	}

	function getValue()
	{

	}

	function setValue()
	{

	}

	function getActions()
	{

	}
}

class JAuthorizationJACLContentItem
{
	function __construct()
	{

	}

	function load()
	{

	}

	function store()
	{

	}

	function remove()
	{

	}

	function getExtension()
	{
		
	}

	function setExtension()
	{

	}

	function getName()
	{

	}

	function setName()
	{

	}

	function getValue()
	{

	}

	function setValue()
	{

	}

	function getContentItems()
	{

	}
}

class JAuthorizationJACLUser
{
	function __construct()
	{

	}

	function load()
	{

	}

	function store()
	{

	}

	function remove()
	{

	}

	function getName()
	{

	}

	function setName()
	{

	}

	function getUserID()
	{

	}

	function setUserID()
	{

	}

	function getUsers()
	{

	}
}

class JAuthorizationJACLExtension
{
	function __construct()
	{

	}

	function load()
	{

	}

	function store()
	{

	}

	function remove()
	{

	}

	function getName()
	{

	}

	function setName()
	{

	}

	function getValue()
	{

	}

	function setValue()
	{

	}

	function getExtensions()
	{

	}
}