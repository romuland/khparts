<?php
/**
 * Virtuemart 2 - JComments Content Plugin
 *
 * Helper class for the form field
 *
 * @version 1.0
 * @author Florian Voutzinos
 * @copyright (C) 2012 Florian Voutzinos
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 *
 **/

defined('_JEXEC') or die('Restricted access');

if (!class_exists('VmConfig')) require(JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_virtuemart' .DS. 'helpers' .DS. 'config.php');
if (!class_exists('VmModel')) require(JPATH_VM_ADMINISTRATOR .DS. 'helpers' .DS. 'vmmodel.php');
if (!class_exists('TableCategories')) require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'categories.php');

abstract class VmJcommentsHelperFormField
{
	
	public static $categoryTree = array();
	
	/**
	 * Get a category tree string with defined separators in order to create an useful array
	 * 
	 * @author Florian Voutzinos (adapted from v2)
	 * @param int $cid virtuemart category id
	 * @param int $level category level
	 * @return formated string with separator containing categories_name and categoires id
	 */
    function getCategoryTree($cid = 0, $level = 0) {
    	
    	static $categoryTree = '';
    	static $catsId = '';
    	
    	$level++;
    	$categoryModel = VmModel::getModel('category');
    	$records = $categoryModel->getCategories(true, $cid);
    	
		if ( !empty($records) ) {
			foreach ($records as $category) {

				$childId = $category->category_child_id;

				if ($childId != $cid) {
					$prefix = str_repeat(' - ', ($level-1) );
				}
				
				// using an array for $categorytree can pose index problems
				// so I use 2 strings and define a category name & category id separator ','
				$categoryTree .= $prefix . $category->category_name . ',';
				$catsId .= $category->virtuemart_category_id . ',';

				if($categoryModel->hasChildren($childId)){
					self::getCategoryTree($childId, $level);
				}
			}
		}
		// separe category name and cat id with :
		return $categoryTree.':'.$catsId;
    }
    

    /**
	 * Creates and  returns an ordered array of categories with catids as keys
	 * and category name as values
	 * 
	 * @author Florian Voutzinos (adapted from vm 2)
	 * @param int $cid virtuemart category id
	 * @param int $level category level
	 * @return array with virtuemart_category_id as $key and category_name as $value
	 */
	public static function categoryListTree($cid = 0, $level = 0) {

		if( empty(self::$categoryTree) ) {
			
			$cache = & JFactory::getCache();
			$cache->setCaching( 1 );
			$categoryTree = $cache->call( array( 'VmJcommentsHelperFormField', 'getCategoryTree' ), $cid, $level);
			
			// parse the retuned string and form a nice array with categories id
			// as key and categories name as value
			$cats = explode(':', $categoryTree);
			$categories = explode(',', $cats[0]);
			$catIds = explode(',', $cats[1]);
			
			$categoryTree = array_combine($catIds, $categories);
			array_pop($categoryTree);
		
			self::$categoryTree = $categoryTree;
		}
		return self::$categoryTree;
	}
}
