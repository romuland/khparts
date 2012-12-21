<?php
/**
 * Virtuemart 2 - JComments Content Plugin
 *
 * Plugin for attaching Jcomments to Virtuemart 2 product views
 *
 * @version 1.0
 * @author Florian Voutzinos
 * @copyright (C) 2012 Florian Voutzinos
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 *
 **/

defined('_JEXEC') or die('Restricted access');

if ( JFactory::getApplication()->isAdmin() ) return;

if (!defined('JCOMMENTS_JVERSION')) return;

jimport('joomla.plugin.plugin');

if (!class_exists('VmJcommentsHelperPlugin')) require(JPATH_PLUGINS .DS. 'content' .DS. 'vmjcomments' .DS. 'helpers' .DS. 'plugin.php');

class plgContentVmJcomments extends JPlugin
{
	
	function plgContentVmJcomments(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	 /**
	 * We clear the product description $product->text by removing {Jcomments} tags
	 * And set the config value for the display
	 * 
	 * @author Florian Voutzinos
	 * @param string $context
	 * @param object reference $product virtuemart product object
	 * @param object reference $params 
	 * @param int $limitstart
	 * @return void
	 */
	function onContentPrepare($context, &$product, &$params, $limitstart = 0) {
		
		// If the trigger is coming from virtuemart productdetails view
		if ( $context == 'com_virtuemart.productdetails' ) {
			
			// If the plugin is activated in the plugin config
			// we parse {Jcomments} tag and set in in config
			if ( $this->params->get('plugin_activated') ) {
				
				// Replaces commenting systems tags like {moscomment}, {jomcomment} with {jcomments}
				VmJcommentsHelperPlugin::processForeignTags($product);
				
				// Set Jcomments config for the actual $product
				$excludedCatId = $this->params->get('excluded_catids', 0); 
				VmJcommentsHelperPlugin::applyConfig($product, $excludedCatId);	
			}
			// Clear {Jcomments} tag from the product description
			VmJcommentsHelperPlugin::clear($product);
		}	
	}


	 /**
	 * We display Jcomments tpl, regarding the plugin config and the display config set previously
	 * 
	 * @author Florian Voutzinos
	 * @param string $context
	 * @param object reference $product virtuemart product object
	 * @param object reference $params 
	 * @param int $limitstart
	 * @return string JComments display
	 */
	function onContentAfterDisplay($context, &$product, &$params, $limitstart = 0) {
		
		// If the trigger comes from virtuemart productdetails and the plugin is activated in the config
		if ( $context == 'com_virtuemart.productdetails' && $this->params->get('plugin_activated') ) {
			
			// Do not display comments in modules
			$data = $params->toArray();
			if ( isset($data['moduleclass_sfx']) ) {
				return '';
			}
			
			// Display the comments if enabled
			if ( VmJcommentsHelperPlugin::areCommentsEnabled() ) {
				if (!class_exists('JComments')) require(JCOMMENTS_BASE . '/jcomments.php');
				return JComments::show($product->virtuemart_product_id, 'com_virtuemart', $product->product_name);
			}
			return '';
		}
		return '';
	}
	
}