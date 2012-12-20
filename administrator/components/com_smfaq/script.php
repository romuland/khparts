<?php
/**
 * SMFAQ
 *
 * @package		component for Joomla 1.6. - 2.5
 * @version		1.7 beta 1
 * @copyright	(C)2009 - 2012 by SmokerMan (http://joomla-code.ru)
 * @license		GNU/GPL v.3 see http://www.gnu.org/licenses/gpl.html
 */

// защита от прямого доступа
defined('_JEXEC') or die('@-_-@');
 
/**
 * Script file of HelloWorld component
 */
class com_smfaqInstallerScript
{
        /**
         * method to install the component
         *
         * @return void
         */
        function install($parent) 
        {
            //$parent->getParent()->setRedirectURL('index.php?option=com_smfaq&view=info');
        }
 
        /**
         * method to uninstall the component
         *
         * @return void
         */
        function uninstall($parent) 
        {
        }
 
        /**
         * method to update the component
         *
         * @return void
         */
        function update($parent) 
        {
        	//Add filed for  1.6
        	
        	$db		= JFactory::getDBO();
        	$field = $db->getTableFields('#__smfaq');
        	if (!isset($field['#__smfaq']['metadesc'])) {
        		$query = ' ALTER TABLE `#__smfaq` ADD `metadesc` text NOT NULL,
        					ADD `metakey` text NOT NULL';
        		$db->setQuery($query);
        		if (!$db->query()) {
        			throw new Exception($db->getErrorMsg()) ;
        		}

        	}
        }
 
        /**
         * method to run before an install/update/uninstall method
         *
         * @return void
         */
        function preflight($type, $parent) 
        {
        }
 
        /**
         * method to run after an install/update/uninstall method
         *
         * @return void
         */
        function postflight($type, $parent) 
        {
        }
}
