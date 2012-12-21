<?php
/**
 * Virtuemart 2 - JComments Content Plugin
 *
 * Helper class for the plugin
 *
 * @version 1.0
 * @author Florian Voutzinos
 * @copyright (C) 2012 Florian Voutzinos
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 *
 **/

defined('_JEXEC') or die('Restricted access');

if (!class_exists('JCommentsCfg')) require(JCOMMENTS_BASE . '/jcomments.config.php');
if (!class_exists('JCommentsFactory')) require(JCOMMENTS_BASE . '/jcomments.class.php');

abstract class VmJcommentsHelperPlugin
{
	
	/* 
	 * boolean true if comments are enabled for this product or false
	 */
	private static $comments_enabled;
	
	
	/**
	 * Set Jcomments config for the actual product, and the value of $comments_enabled
	 * 
	 * @author Florian Voutzinos
	 * @param object $product virtuemart product object
	 * @param array	$excludedCatIds Ids of excluded category from plugin params
	 * @return void
	 */
	public static function applyConfig($product, $excludedCatIds) {
		
		$config = JCommentsFactory::getConfig();
		
		// {Jcomments} tag domines Excluded categories !
		// and {Jcomments OFF} domines everything
		
		// if {Jcomments OFF} tag found we disable comments by force
		if ( self::isDisabled($product) ) {
			$config->set('comments_on', 0);
			$config->set('comments_off', 1);
			self::$comments_enabled = false;
		}
		
		// else if {Jcomments ON} tag found we enable comments by force
		elseif ( self::isEnabled($product) ) {
			$config->set('comments_on', 1);
			$config->set('comments_off', 0);
			self::$comments_enabled = true;
		}
		
		// We enable or disable comments regarding if the product category is enabled or no
		else {
			// we enable comments if the product category is enabled
			if( self::isCategoryProductEnabled($product, $excludedCatIds) ) {
				$config->set('comments_on', 1);
				$config->set('comments_off', 0);
				self::$comments_enabled = true;
			}
			// we disable comments
			else {
				$config->set('comments_on', 0);
				$config->set('comments_off', 1);
				self::$comments_enabled = false;
			}
		}
		
		$config->set('comments_locked', (int)self::isLocked($product));
	}
	
	
	/**
	 * 
	 * Returns true if comments are enabled for the actual product or false
	 */
	public static function areCommentsEnabled() {
		return self::$comments_enabled;
	}
	
	
	/**
	 * Replaces or removes commenting systems tags like {moscomment}, {jomcomment} etc
	 * From Jcomments content plugin helper
	 * 
	 * @param object $product virtuemart product object
	 * @param boolean $removeTags Remove all 3rd party tags or replace it to JComments tags?
	 * @return void
	 */
	public static function processForeignTags( &$product, $removeTags = false) {
		
		// replace
		if ( !$removeTags ) {
			$patterns = array('#\{(moscomment|mxc|jomcomment|easycomments)\}#is', '#\{\!jomcomment\}#is', '#\{mxc\:\:closed\}#is');
			$replacements = array('{jcomments on}', '{jcomments off}', '{jcomments lock}');
		} 
		
		// remove
		else {
			$patterns = array('#\{(moscomment|mxc|msc::closed|!jomcomment|jomcomment|easycomments)\}#is');
			$replacements = array('');
		}
		self::_processTags($product, $patterns, $replacements);
	}
	
	
	/**
	 * Clears all JComments tags from $product->text
	 * Adapted from JComments content plugin helper
	 *
	 * @param object $product virtuemart product object
	 * @return void
	 */
	public static function clear( &$product )
	{
		$patterns = array('/{jcomments\s+(off|on|lock)}/is');
		$replacements = array('');
		
		self::_processTags($product, $patterns, $replacements );
	}
	
	
	/**
	 * Process foreign tags
	 * Adapted from JComments content plugin helper
	 * 
	 * @param object $product virtuemart product object
	 * @param array $patterns
	 * @param array $replacements
	 * @return void
	 */
	private static function _processTags(&$product, $patterns = array(), $replacements = array()) {
		if (count($patterns) > 0) {
			ob_start();
			if (isset($product->text)) {
				$product->text = preg_replace($patterns, $replacements, $product->text);
			}
			ob_end_clean();
		}
	}
	
	
	/**
	 * Searches given tag in virtuemart $product->text
	 * Adapted from JComments content plugin helper
	 *
	 * @param object $product virtuemart product object
	 * @param string $pattern
	 * @return boolean True if any tag found, False otherwise
	 */
	private static function _findTag( &$product, $pattern) {
		return (isset($product->text) && preg_match($pattern, $product->text));
	}
	
	
	/**
	 * Determines if the product category is enabled (true) or excluded (false)
	 *
	 * @author Florian Voutzinos
	 * @param object $product 			Virtuemart product object
	 * @param mixed	 $excludedCatIds 	Ids of excluded category from plugin params
	 * @return boolean True if excluded or false if enabled
	 */
	private static function isCategoryProductEnabled($product, $excludedCatIds) {
		
		if ( is_array($excludedCatIds) && in_array($product->virtuemart_category_id, $excludedCatIds) ) {
			return false;
		}
		return true;
	}
	
	
	/**
	 * Return true if $product->text contains {jcomments on} tag
	 * From JComments content plugin helper
	 *
	 * @param object  $product virtuemart product object
	 * @return boolean True if {jcomments on} found, False otherwise
	 */
	private static function isEnabled( &$product ) {
		return self::_findTag($product, '/{jcomments\s+on}/is');
	}
	
	
	/**
	 * Return true if $product->text contains {jcomments off} tag
	 * From JComments content plugin helper
	 *
	 * @param object $product virtuemart product object
	 * @return boolean True if {jcomments off} found, False otherwise
	 */
	private static function isDisabled( &$product ) {
		return self::_findTag($product, '/{jcomments\s+off}/is' );
	}
	
	
	/**
	 * Return true if $product->text contains {jcomments lock} tag
	 * From JComments content plugin helper
	 *
	 * @param object $product virtuemart product object
	 * @return boolean True if {jcomments lock} found, False otherwise
	 */
	private static function isLocked( &$product ) {
		return self::_findTag($product, '/{jcomments\s+lock}/is');
	}
	
}