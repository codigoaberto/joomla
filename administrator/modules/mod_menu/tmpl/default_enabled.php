<?php
/**
 * @version		$Id:mod_menu.php 2463 2006-02-18 06:05:38Z webImagery $
 * @package		Joomla.Administrator
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

//
// Site SubMenu
//
$menu->addChild(
	new JMenuNode(JText::_('Mod_Menu_Site'), '#'), true
);
$menu->addChild(
	new JMenuNode(JText::_('Mod_Menu_Control_Panel'), 'index.php', 'class:cpanel')
);

$menu->addSeparator();

if ($user->authorise('core.admin')) {
	$menu->addChild(new JMenuNode(JText::_('Configuration'), 'index.php?option=com_config', 'class:config'));
	$menu->addSeparator();
}

$chm = $user->authorise('core.manage', 'com_checkin');
$cam = $user->authorise('core.manage', 'com_cache');

if ($chm || $cam )
{
	$menu->addChild(
		new JMenuNode(JText::_('Mod_Menu_Site_Maintenance'), '#', 'class:maintenance'), true
	);

	if ($chm)
	{
		$menu->addChild(new JMenuNode(JText::_('Mod_Menu_Global_Checkin'), 'index.php?option=com_checkin', 'class:checkin'));
		$menu->addSeparator();
	}
	if ($cam)
	{
		$menu->addChild(new JMenuNode(JText::_('Mod_Menu_Clear_Cache'), 'index.php?option=com_cache', 'class:clear'));
		$menu->addChild(new JMenuNode(JText::_('Mod_Menu_Purge_Expired_Cache'), 'index.php?option=com_cache&view=purge', 'class:purge'));
	}

	$menu->getParent();
}

$menu->addSeparator();
	$menu->addChild(
	new JMenuNode(JText::_('Mod_Menu_System_Information'), 'index.php?option=com_admin&view=sysinfo', 'class:info')
);
$menu->addSeparator();

$menu->addChild(new JMenuNode(JText::_('Mod_Menu_Logout'), 'index.php?option=com_login&task=logout', 'class:logout'));

$menu->getParent();


//
// Users Submenu
//
if ($user->authorise('core.manage', 'com_users'))
{
	$menu->addChild(
		new JMenuNode(JText::_('Mod_menu_Com_users_Users'), '#'), true
	);
	$menu->addChild(
		new JMenuNode(JText::_('Mod_menu_Com_users_User_Manager'), 'index.php?option=com_users&view=users', 'class:user')
	);
	$menu->addChild(
		new JMenuNode(JText::_('Mod_menu_Com_users_Groups'), 'index.php?option=com_users&view=groups', 'class:groups')
	);
	$menu->addChild(
		new JMenuNode(JText::_('Mod_menu_Com_users_Levels'), 'index.php?option=com_users&view=levels', 'class:levels')
	);

	$menu->addSeparator();
	$menu->addChild(
		new JMenuNode(JText::_('Mod_menu_Com_users_Add_User'), 'index.php?option=com_users&task=user.add', 'class:newuser')
	);
	$menu->addChild(
		new JMenuNode(JText::_('Mod_menu_Com_users_Add_Group'), 'index.php?option=com_users&task=group.add', 'class:newgroup')
	);
	$menu->addChild(
		new JMenuNode(JText::_('Mod_menu_Com_users_Add_Level'), 'index.php?option=com_users&task=level.add', 'class:newlevel')
	);

	$menu->addSeparator();

	$menu->addChild(
		new JMenuNode(JText::_('Mod_menu_Mass_Mail_Users'), 'index.php?option=com_users&view=mail', 'class:massmail')
	);

	$menu->getParent();
}

//
// Menus Submenu
//
if ($user->authorise('core.manage', 'com_menus'))
{
	$menu->addChild(
		new JMenuNode(JText::_('Mod_Menu_Menus'), '#'), true
	);
	$menu->addChild(
		new JMenuNode(JText::_('Mod_Menu_Menu_Manager'), 'index.php?option=com_menus&view=menus', 'class:menumgr')
	);
	$menu->addSeparator();

	// Menu Types
	foreach (ModMenuHelper::getMenus() as $menuType)
	{
		$menu->addChild(
			new JMenuNode(
				$menuType->title.($menuType->home ? ' <span>'.JHTML::_('image', 'menu/icon-16-default.png', NULL, NULL, true).'</span>' : ''),
				'index.php?option=com_menus&view=items&menutype='.$menuType->menutype, 'class:menu'
			)
		);
	}
	$menu->getParent();
}

//
// Content Submenu
//
if ($user->authorise('core.manage', 'com_content'))
{
	$menu->addChild(
		new JMenuNode(JText::_('Mod_Menu_Com_Content'), '#'), true
	);
	$menu->addChild(
		new JMenuNode(JText::_('Mod_Menu_Com_Content_Article_Manager'), 'index.php?option=com_content', 'class:article')
	);

	$menu->addChild(
		new JMenuNode(JText::_('Mod_Menu_Com_content_Category_Manager'), 'index.php?option=com_categories&extension=com_content', 'class:category')
	);
	$menu->addChild(
		new JMenuNode(JText::_('Mod_Menu_Com_content_Featured'), 'index.php?option=com_content&view=featured', 'class:featured')
	);
	$menu->addSeparator();
	$menu->addChild(
		new JMenuNode(JText::_('Mod_Menu_Com_content_New_article'), 'index.php?option=com_content&task=article.add', 'class:newarticle')
	);
	$menu->addChild(
		new JMenuNode(JText::_('Mod_Menu_Com_content_New_category'), 'index.php?option=com_categories&task=category.add&extension=com_content', 'class:newcategory')
	);

	$menu->addSeparator();
	if ($user->authorise('core.manage', 'com_media')) {
		$menu->addChild(new JMenuNode(JText::_('Mod_Menu_Media_Manager'), 'index.php?option=com_media', 'class:media'));
	}

	$menu->getParent();
}

//
// Components Submenu
//
$menu->addChild(new JMenuNode(JText::_('Mod_Menu_Components'), '#'), true);

// Get the authorised components and sub-menus.
$components = ModMenuHelper::getComponents( true );

foreach ($components as &$component)
{
	$text = $lang->hasKey($component->title) ? JText::_($component->title) : $component->alias;

	if (!empty($component->submenu))
	{
		// This component has a db driven submenu.
		$menu->addChild(new JMenuNode($text, $component->link, $component->img), true);
		foreach ($component->submenu as $sub)
		{
			$text = $lang->hasKey($sub->title) ? JText::_($sub->title) : $sub->alias;
			$menu->addChild(new JMenuNode($text, $sub->link, $sub->img));
		}
		$menu->getParent();
	}
	else {
		$menu->addChild(new JMenuNode($text, $component->link, $component->img));
	}
}
$menu->getParent();

//
// Extensions Submenu
//
$im = $user->authorise('core.manage', 'com_installer');
$mm = $user->authorise('core.manage', 'com_modules');
$pm = $user->authorise('core.manage', 'com_plugins');
$tm = $user->authorise('core.manage', 'com_templates');
$lm = $user->authorise('core.manage', 'com_languages');

if ($im || $mm || $pm || $tm || $lm)
{
	$menu->addChild(new JMenuNode(JText::_('Mod_Menu_Extensions_Extensions'), '#'), true);

	if ($im)
	{
		$menu->addChild(new JMenuNode(JText::_('Mod_Menu_Extensions_Extension_Manager'), 'index.php?option=com_installer', 'class:install'));
		$menu->addSeparator();
	}
	if ($mm) {
		$menu->addChild(new JMenuNode(JText::_('Mod_Menu_Extensions_Module_Manager'), 'index.php?option=com_modules', 'class:module'));
	}
	if ($pm) {
		$menu->addChild(new JMenuNode(JText::_('Mod_Menu_Extensions_Plugin_Manager'), 'index.php?option=com_plugins', 'class:plugin'));
	}
	if ($tm) {
		$menu->addChild(new JMenuNode(JText::_('Mod_Menu_Extensions_Template_Manager'), 'index.php?option=com_templates', 'class:themes'));
	}
	if ($lm) {
		$menu->addChild(new JMenuNode(JText::_('Mod_Menu_Extensions_Language_Manager'), 'index.php?option=com_languages', 'class:language'));
	}
	$menu->getParent();
}

//
// Help Submenu
//
$menu->addChild(
	new JMenuNode(JText::_('Mod_Menu_Help'), '#'), true
);
$menu->addChild(
	new JMenuNode(JText::_('Mod_Menu_Help_Joomla'), 'index.php?option=com_admin&view=help', 'class:help')
);
$menu->addSeparator();

$menu->addChild(
	new JMenuNode(JText::_('Mod_Menu_Help_Support_Forum'), 'http://forum.joomla.org', 'class:help-forum', false, '_blank')
);
$menu->addChild(
	new JMenuNode(JText::_('Mod_Menu_Help_Documentation'), 'http://docs.joomla.org', 'class:help-docs', false, '_blank')
);
$menu->addSeparator();
$menu->addChild(
	new JMenuNode(JText::_('Mod_Menu_Help_Extensions'), 'http://extensions.joomla.org', 'class:help-jed', false, '_blank')
);
$menu->addChild(
	new JMenuNode(JText::_('Mod_Menu_Help_Translations'), 'http://community.joomla.org/translations.html', 'class:help-trans', false, '_blank')
);
$menu->addChild(
	new JMenuNode(JText::_('Mod_Menu_Help_Resources'), 'http://resources.joomla.org', 'class:help-jrd', false, '_blank')
);
$menu->addChild(
	new JMenuNode(JText::_('Mod_Menu_Help_Community'), 'http://community.joomla.org', 'class:help-community', false, '_blank')
);
$menu->addChild(
	new JMenuNode(JText::_('Mod_Menu_Help_Security'), 'http://developer.joomla.org/security.html', 'class:help-security', false, '_blank')
);
$menu->addChild(
	new JMenuNode(JText::_('Mod_Menu_Help_Developer'), 'http://developer.joomla.org', 'class:help-dev', false, '_blank')
);
$menu->addChild(
	new JMenuNode(JText::_('Mod_Menu_Help_Shop'), 'http://shop.joomla.org', 'class:help-shop', false, '_blank')
);
$menu->getParent();

