<?php
/**
*
* Modify user form view
*
* @package	VirtueMart
* @subpackage User
* @author Oscar van Eijk
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: edit.php 6053 2012-06-05 12:36:21Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//AdminMenuHelper::startAdminArea();
// vmdebug('User edit',$this);
// Implement Joomla's form validation
JHTML::_('behavior.formvalidation');
JHTML::stylesheet('vmpanels.css', JURI::root().'components/com_virtuemart/assets/css/'); // VM_THEMEURL
?>
<style type="text/css">
.invalid {
	border-color: #f00;
	background-color: #ffd;
	color: #000;
}
label.invalid {
	background-color: #fff;
	color: #f00;
}
</style>
<script language="JavaScript" type="text/javascript">
function myValidator(f, t)
{
	f.task.value=t;
	if (document.formvalidator.isValid(f)) {
		f.submit();
		return true;
	} else {
		var msg = '<?php echo addslashes( JText::_('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS') ); ?>';
		alert (msg);
	}
	return false;
}
jQuery(function($){
	$(document).ready(function(e) {
		$('.usertype').addClass('jQtooltip');
		$('.usertype').attr('title','Укажите пожалуйста, в каком статусе Вы будете работать с нами');
		$('.usertype').append("<img src='/images/questionmark.png' style='padding-left:5px; padding-right:3px; padding-bottom:5px;'/>");

		var person = document.getElementById('usertypeperson');
		var org = document.getElementById('usertypeorganization');
		
		//if($('#usertypeperson').checked = 'checked'){}
		
		var radio_selected='', lbl_selected='', radio='', lbl='';
		if (org.checked){
			radio_selected = '#usertypeorganization';
			lbl_selected = '#usertypeorganization-lbl';
			radio = '#usertypeperson';
			lbl = '#usertypeperson-lbl';
			$('#company_field').css('display', 'block');
	   		$('.company').css('display', 'block');
		}
		else{
			radio_selected = '#usertypeperson';
			lbl_selected = '#usertypeperson-lbl';
			radio = '#usertypeorganization';
			lbl = '#usertypeorganization-lbl';
			$('#company_field').css('display', 'none');
	   		$('.company').css('display', 'none');
		}
		$(radio_selected).addClass('RadioClass').addClass('RadioSelected');
		$(radio_selected).attr('checked', true)
		$(lbl_selected).addClass('LabelSelected');
		$(radio).addClass('RadioClass');
		$(lbl).addClass('RadioLabelClass')
		
   		$('#usertypeperson').click(function(e) {
       		$('#company_field').hide('slow');
	   		$('.company').hide('slow');
    	});
		$('#usertypeorganization').click(function(e) {
       		$('#company_field').show('slow');
	   		$('.company').show('slow');
    	});
		$(".RadioClass").change(function(){
			if($(this).is(":checked")){
				$(this).parent().children(".LabelSelected").addClass("RadioLabelClass").removeClass("LabelSelected");
				$(this).next("label").addClass("LabelSelected").removeClass("RadioLabelClass");
				$(".RadioSelected:not(:checked)").removeClass("RadioSelected");
			}
		});		

		$(".company").click(
			function(){$("#hide1").fadeOut("slow");}
		);
		$(".person").click(
			function(){$("#hide1").fadeIn("slow");}
		);
	});	
});

jQuery(function($){
	$(function() {
		$('.jQtooltip').each(function() {
    	var title = $(this).attr('title');
    	if (title && title != '') {
      		$(this).attr('title', '').append('<div>' + title + '</div>');
      		var width = $(this).find('div').width();
      		var height = $(this).find('div').height();
      		$(this).hover(
       			function() {
          			$(this).find('div')
            		.clearQueue()
            		.animate({width: width + 20, height: height + 20}, 200).show(200)
            		.animate({width: width, height: height}, 200);
        		},
        		function() {
         			$(this).find('div')
            		.animate({width: width + 20, height: height + 20}, 150)
           			.animate({width: 'hide', height: 'hide'}, 150);
        		}
      		)
    	}
 	 })

	})
})
</script>
<p id="title-text"><?php echo $this->page_title ?></p>
<?php echo shopFunctionsF::getLoginForm(false); ?>

<p id="title-text"><?php if($this->userDetails->virtuemart_user_id==0) {
	echo JText::_('COM_VIRTUEMART_YOUR_ACCOUNT_REG');
}?></p>
<form method="post" id="adminForm" name="userForm" action="<?php echo JRoute::_('index.php?view=user',$this->useXHTML,$this->useSSL) ?>" class="form-validate">
<?php if($this->userDetails->user_is_vendor){ ?>

    <?php } ?>
<?php // Loading Templates in Tabs
if($this->userDetails->virtuemart_user_id!=0) {
    $tabarray = array();
    if($this->userDetails->user_is_vendor){
	    $tabarray['vendor'] = 'COM_VIRTUEMART_VENDOR';
    }
    $tabarray['shopper'] = 'COM_VIRTUEMART_SHOPPER_FORM_LBL';
    //$tabarray['user'] = 'COM_VIRTUEMART_USER_FORM_TAB_GENERALINFO';
    if (!empty($this->shipto)) {
	    $tabarray['shipto'] = 'COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL';
    }
    if (($_ordcnt = count($this->orderlist)) > 0) {
	    $tabarray['orderlist'] = 'COM_VIRTUEMART_YOUR_ORDERS';
    }

//личный кабинет
    shopFunctionsF::buildTabs ( $this, $tabarray);

 } else {
	 //Регистрация
    echo $this->loadTemplate ( 'shopper' );
 }

/*
 * TODO this Stuff should be converted in a payment module. But the idea to show already saved payment information to the user is a good one
 * So maybe we should place here a method (joomla plugin hook) which loads all published plugins, which already used by the user and display
 * them.
 */
//	echo $this->pane->startPanel( JText::_('COM_VIRTUEMART_SHOPPER_PAYMENT_FORM_LBL'), 'edit_payment' );
//	echo $this->loadTemplate('payment');
//	echo $this->pane->endPanel();

//	echo $this->pane->startPanel( JText::_('COM_VIRTUEMART_SHOPPER_SHIPMENT_FORM_LBL'), 'edit_shipto' );
//	echo $this->loadTemplate('shipto');
//	echo $this->pane->endPanel();
//	if ($this->shipto !== 0) {
//		// Note:
//		// Of the order of the tabs change here, change the startOffset value for
//		// JPane::getInstance() as well in view.html.php!
//		echo $this->pane->startPanel( JText::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL'), 'edit_shipto' );
//		echo $this->loadTemplate('shipto');
//		echo $this->pane->endPanel();
//	}

// 	if (($_ordcnt = count($this->orderlist)) > 0) {
// 		echo $this->pane->startPanel( JText::_('COM_VIRTUEMART_ORDER_LIST_LBL') . ' (' . $_ordcnt . ')', 'edit_orderlist' );
// 		echo $this->loadTemplate('orderlist');
// 		echo $this->pane->endPanel();
// 	}

// 	if (!empty($this->userDetails->user_is_vendor)) {
// 		echo $this->pane->startPanel( JText::_('COM_VIRTUEMART_VENDOR_MOD'), 'edit_vendor' );
// 		echo $this->loadTemplate('vendor');
// 		echo $this->pane->endPanel();
// 	}

// 	echo $this->pane->endPane();
?>
<input type="hidden" name="option" value="com_virtuemart" />
<input type="hidden" name="controller" value="user" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>

