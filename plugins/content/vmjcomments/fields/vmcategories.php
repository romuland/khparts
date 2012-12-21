<?php
/**
 * Virtuemart 2 - JComments Content Plugin
 *
 * For displaying a list of vm categories in the plugin params
 *
 * @version 1.0
 * @author Florian Voutzinos
 * @copyright (C) 2012 Florian Voutzinos
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 *
 **/

defined('_JEXEC') or die('Restricted access');

if (!class_exists('VmJcommentsHelperFormField')) require(JPATH_PLUGINS .DS. 'content' .DS. 'vmjcomments' .DS. 'helpers' .DS. 'formfield.php');

jimport('joomla.form.formfield');
jimport('joomla.html.html');

class JFormFieldVmCategories extends JFormField
{
	
    var $name = 'vmcategories';

    /**
	 * Gets the list of vm categories with multiple select formatting
	 * @author Florian Voutzinos
	 * @return string
	 */
    function getInput() {
		
		$categoryTree = VmJcommentsHelperFormField::categoryListTree();

		$key = ($this->element['key_field'] ? $this->element['key_field'] : 'value');
		$val = ($this->element['value_field'] ? $this->element['value_field'] : $this->name);
		
		// Dynamic height
		$size = count($categoryTree);
		if($size > 100) $size = 100;
		
		// '[]' is needed after $this->name in order to multiselect !
		return JHTML::_('select.genericlist', $categoryTree, $this->name.'[]', 'class="inputbox" multiple="multiple" style="width: 150px" size="'.$size.'"', $key, $val, $this->value, $this->id);
    }
    
}
