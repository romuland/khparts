<?php
/**
 *
 * Modify user form view, User info
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
 * @version $Id: edit_vmshopper.php 6203 2012-07-03 09:48:00Z enytheme $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>

<fieldset>
	<span class="userfields_info" style="display:none;">
		<?php echo JText::_('COM_VIRTUEMART_SHOPPER_FORM_LBL') ?>
	</span>
	<table class="adminForm user-details" style="display:none;">
<?php	if(Vmconfig::get('multix','none')!=='none'){ ?>

		<tr>
			<td class="key">
				<label for="virtuemart_vendor_id">
					<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_VENDOR') ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['vendors']; ?>
			</td>
		</tr>
<?php } ?>

		<tr>
			<td class="key">
				<label for="perms">
					<?php echo JText::_('COM_VIRTUEMART_USER_FORM_PERMS') ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['perms']; ?>
			</td>
		</tr>

		<tr>
			<td class="key">
				<label for="customer_number">
					<?php echo JText::_('COM_VIRTUEMART_USER_FORM_CUSTOMER_NUMBER') ?>:
				</label>
			</td>
			<td>
			 <?php if(Permissions::getInstance()->check('admin')) { ?>
				<input type="text" class="inputbox" name="customer_number" id="customer_number" size="40" value="<?php echo  $this->lists['custnumber'];
					?>" />
			<?php } else {
				echo $this->lists['custnumber'];
			} ?>
			</td>
		</tr>
		 <?php if($this->lists['shoppergroups']) { ?>
		<tr>
			<td class="key">
				<label for="virtuemart_shoppergroup_id">
					<?php echo JText::_('COM_VIRTUEMART_SHOPPER_FORM_GROUP') ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['shoppergroups']; ?>
			</td>
		</tr>
		<?php } ?>
	</table>
</fieldset>
