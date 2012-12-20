<?php
/**
* YOOlogin Joomla! Module
*
* @author    yootheme.com
* @copyright Copyright (C) 2007 YOOtheme Ltd. & Co. KG. All rights reserved.
* @license	 GNU/GPL
*/
/**
 * @version SVN: $Id: builder.php 469 2011-07-29 19:03:30Z mustaqs $
 * @package    superlogin
 * @subpackage C:
 * @author     Mustaq Sheikh {@link http://www.herdboy.com} 
 * @author     Created on 07-Sep-2011
 * @license    GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// count instances
if (!isset($GLOBALS['super_logins'])) {
	$GLOBALS['super_logins'] = 1;
} else {
	$GLOBALS['super_logins']++;
}

// Include the syndicate functions only once
//require_once (dirname(__FILE__).DS.'helper.php');
require_once dirname(__FILE__).'/helper.php';

$params->def('greeting', 1);

$type 	= modSuperloginHelper::getType();
$return	= modSuperloginHelper::getReturnURL($params, $type);

$user =& JFactory::getUser();

// init vars
$style                 = $params->get('style', 'default');
$direct               = $params->get('direct', 'default');
$pretext               = $params->get('pretext', '');
$posttext              = $params->get('posttext', '');
$text_mode             = $params->get('text_mode', 'input');
$login_button          = $params->get('login_button', 'icon');
$logout_button         = $params->get('logout_button', 'text');
$auto_remember         = $params->get('auto_remember', '1');
$lost_password         = $params->get('lost_password', '1');
$lost_username         = $params->get('lost_username', '1');
$registration          = $params->get('registration', '1');

// css parameters
$superlogin_id           = $GLOBALS['super_logins'];

$module_base           = JURI::base() . 'modules/mod_super_login/';

switch ($direct) {
	case "jomsocial":
		$reglink = ('index.php?option=com_community&view=register');
   		break;
	case "kunena":
		$reglink = ('index.php?option=com_users&view=registration');
   		break;
	case "sandbox":
		$reglink = ('#');
   		break;
	default:
	/*	$reglink = ('index.php?option=com_users&view=registration');*/
	$reglink = ('index.php?option=com_virtuemart&view=user&layout=edit');
}

switch ($style) {
	case "niftydefault":
		require(JModuleHelper::getLayoutPath('mod_super_login', 'niftydefault'));
   		break;
	case "niftyquick":
		require(JModuleHelper::getLayoutPath('mod_super_login', 'niftyquick'));
   		break;
	case "quick":
		require(JModuleHelper::getLayoutPath('mod_super_login', 'quick'));
   		break;
	case "mega":
		require(JModuleHelper::getLayoutPath('mod_super_login', 'mega'));
   		break;   		
	default:
		require(JModuleHelper::getLayoutPath('mod_super_login', 'default'));
}

$document =& JFactory::getDocument();
$document->addStyleSheet($module_base . 'mod_super_login.css.php');