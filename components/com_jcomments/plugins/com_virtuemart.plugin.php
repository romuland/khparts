<?php
/**
 * JComments plugin for VirtueMart objects support
 *
 * @version 2.0
 * @package JComments
 * @author Sergey M. Litvinov (smart@joomlatune.ru)
 * @copyright (C) 2006-2012 by Sergey M. Litvinov (http://www.joomlatune.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 **/

jimport('joomla.filesystem.file');

// if Vmconfig is present it is Virtuemart 2 and we load the config for VMLANG value
$configPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php';
if ( JFile::exists($configPath) ) {
	if (!class_exists( 'VmConfig' )) require($configPath);
	VmConfig::loadConfig();
}
// else it's virtuemart 1
else define("IS_VM1");
	
class jc_com_virtuemart extends JCommentsPlugin
{
		/*
	 * For virtuemart 1 & 2 compatiblity
	 */
	function getObjectTitle($id){
		
		if (defined('IS_VM1')) {
			return $this->getObjectTitleVM1($id);
		} else {
			return $this->getObjectTitleVM2($id);
		}
	}
	
	/*
	 * For virtuemart 1 & 2 compatiblity
	 */
	function getObjectLink($id) {
		
		if (defined('IS_VM1')) {
			return $this->getObjectLinkVM1($id);
		} else {
			return $this->getObjectLinkVM2($id);
		}
	}
	
	/*
	 * For Virtuemart 2
	 */ 
	function getObjectTitleVM2($id) {
		
		$db = JCommentsFactory::getDBO();
		$db->setQuery('SELECT product_name, virtuemart_product_id FROM #__virtuemart_products_'.VMLANG.' WHERE virtuemart_product_id ='.$id);
		return $db->loadResult();
	}
	
	/*
	 * For Virtuemart 2
	 */ 
	function getObjectLinkVM2($id) {
		
		$db = JCommentsFactory::getDBO();
		$db->setQuery('SELECT virtuemart_category_id FROM #__virtuemart_product_categories WHERE virtuemart_product_id ='.$id);
		$categoryId = $db->loadResult();
		
		$link = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$id.'&virtuemart_category_id='.$categoryId ;
		return JoomlaTuneRoute::_($link);
	}
	
	/*
	 * For Virtuemart 1
	 */ 
	function getObjectTitleVM1($id)
	{
		$db = JCommentsFactory::getDBO();
		$db->setQuery( 'SELECT product_name, product_id FROM #__vm_product WHERE product_id = ' . $id );
		return $db->loadResult();
	}
	

	/*
	 * For Virtuemart 1
	 */ 
	function getObjectLinkVM1($id)
	{
		$_Itemid =  self::getItemid('com_virtuemart');
		$_Itemid = $_Itemid > 0 ? '&Itemid=' . $_Itemid : '';

		$categoryFlypage = '';
		if (!defined('FLYPAGE')) {
			include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.cfg.php');
			if (defined('FLYPAGE')) {
				$categoryFlypage = FLYPAGE;
			}
		}

		$db = JCommentsFactory::getDBO();
		$query = "SELECT CASE WHEN c.category_flypage IS NULL OR c.category_flypage = '' "
			."\n          THEN CONCAT('index.php?option=com_virtuemart&page=shop.product_details&flypage=', '".$categoryFlypage."','&category_id=',c.category_id,'&product_id=', a.product_id, '".$_Itemid."' )"
			."\n          ELSE CONCAT('index.php?option=com_virtuemart&page=shop.product_details&flypage=', c.category_flypage,'&category_id=',c.category_id,'&product_id=', a.product_id, '".$_Itemid."' )"
			."\n          END"
			. "\n FROM #__vm_product AS a"
			. "\n LEFT JOIN #__vm_product_category_xref AS b ON b.product_id = a.product_id"
			. "\n LEFT JOIN #__vm_category AS c ON b.category_id = c.category_id"
			. "\n WHERE a.product_id = '$id'"
			;
		$db->setQuery($query);
		$link = JoomlaTuneRoute::_($db->loadResult());
		return $link;
	}
}
?>