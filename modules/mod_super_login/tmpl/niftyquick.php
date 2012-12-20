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
JHtml::_('behavior.keepalive');
?>

<?php if($type == 'logout') : ?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" name="login">
<?php endif; ?>

<span class="<?php echo $style ?>">
	<span class="super-login">

      <?php if($type == 'logout') : ?>
      <span class="logout">
      
			<?php if ($params->get('greeting')) : ?>
			<span class="greeting"><?php echo JText::sprintf( 'HINAME', $user->get('name') ); ?></span>
			<?php endif; ?>

			<span class="logout-button<?php echo "-" . $logout_button ?>">
				<button value="<?php if ( $logout_button == "text" ) { echo JText::_( 'BUTTON_LOGOUT'); } ?>" name="Submit" type="submit" title="<?php echo JText::_('BUTTON_LOGOUT'); ?>"><?php if ( $logout_button == "text" ) { echo JText::_( 'BUTTON_LOGOUT'); } ?></button>
			</span>

		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
        <?php echo JHtml::_('form.token'); ?>

		</span>
        </form>
      <?php else : ?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" >
      <span class="login">
      
         <?php echo $params->get('pretext'); ?>
         
			<span class="username">

				<?php if ($text_mode == "input") { ?>

        <input id="modlgn-username" type="text" name="username" class="inputbox" size="18" value="<?php echo JText::_( 'Username' ); ?>" onblur="if(this.value=='') this.value='<?php echo JText::_( 'Username' ); ?>';" onfocus="if(this.value=='<?php echo JText::_( 'Username' ); ?>') this.value='';" />						
				<?php } else { ?>
				<label for="modlgn_username<?php echo $superlogin_id; ?>"><?php echo JText::_( 'Username' ); ?></label>
				<br />
				<input id="modlgn_username<?php echo $superlogin_id; ?>" type="text" name="username" size="18" />
				<?php } ?>

			</span>

			<span class="password">

				<?php if ($text_mode == "input") { ?>
						<input id="modlgn-passwd" type="password" name="password" class="inputbox" size="18" value="<?php echo JText::_( 'Password' ); ?>" onblur="if(this.value=='') this.value='<?php echo JText::_( 'Password' ); ?>';" onfocus="if(this.value=='<?php echo JText::_( 'Password' ); ?>') this.value='';" />
				<?php } else { ?>
				<label for="modlgn_passwd<?php echo $superlogin_id; ?>"><?php echo JText::_( 'Password' ); ?></label>
				<br />
				<input id="modlgn_passwd<?php echo $superlogin_id; ?>" type="password" name="passwd" size="10" />
				<?php } ?>

			</span>

         <?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
			<?php if ( $auto_remember ) { ?>
			<input type="hidden" name="remember" value="yes" />
			<?php } else { ?>
			<span class="remember">
				<input id="modlgn_remember<?php echo $superlogin_id; ?>" type="checkbox" name="remember" value="yes" />
				<label for="modlgn_remember<?php echo $superlogin_id; ?>"><?php echo JText::_( 'Remember me' ); ?></label>
			</span>
			<?php } ?>
         <?php endif; ?>

			<span class="login-button-container">
			<span class="login-button<?php echo "-" . $login_button ?>">
				<button value="<?php if ( $login_button == "text" ) { echo JText::_( 'BUTTON_LOGIN'); } ?>" name="Submit" type="submit" title="<?php echo JText::_('BUTTON_LOGIN'); ?>"><?php if ( $login_button == "text" ) { echo JText::_( 'BUTTON_LOGIN'); } ?></button>
			</span>
			</span>

         <?php if ( $lost_password ) { ?>
         <span class="lostpassword">
            <a href="<?php echo JRoute::_( 'index.php?option=com_users&view=reset' ); ?>" title="<?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?>"></a>
         </span>
         <?php } ?>

         <?php if ( $lost_username ) { ?>
         <span class="lostusername">
            <a href="<?php echo JRoute::_( 'index.php?option=com_users&view=remind' ); ?>" title="<?php echo JText::_('FORGOT_YOUR_USERNAME'); ?>"></a>
         </span>
         <?php } ?>
         
         <?php
         $usersConfig = &JComponentHelper::getParams( 'com_users' );
         if ($usersConfig->get('allowUserRegistration') && $registration) { ?>
         <span class="registration">
            <a href="<?php echo JRoute::_( $reglink);?>" title="<?php echo JText::_( 'REGISTER'); ?>"></a>
         </span>
         <?php } ?>
         
         <?php echo $params->get('posttext'); ?>
         
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>

      </span>
      
      <?php endif; ?>
      
   </span>
</span>
</form>
