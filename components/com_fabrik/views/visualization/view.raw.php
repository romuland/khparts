<?php
/**
* @package Joomla
* @subpackage Fabrik
* @copyright Copyright (C) 2005 Rob Clayburn. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class FabrikViewVisualization extends JView{
	
	function display( $tmpl = 'default' )
	{
		
		$model = $this->getModel();
		$usersConfig = JComponentHelper::getParams('c