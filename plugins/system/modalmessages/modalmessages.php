<?php
/*------------------------------------------------------------------------
# Modal syslem messages
# ------------------------------------------------------------------------
# Dmitry Chernov
# Copyright (C) 2011 Provitiligo.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.provitiligo.com
# Support:  provitiligo@gmail.com
-------------------------------------------------------------------------*/

// no direct access

defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

$lang = JFactory::getLanguage();
$lang->load('plg_system_modalmessages');

class plgSystemModalmessages extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onAfterInitialise()
	{
		if(JFactory::getApplication()->isAdmin()) return true;

		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'plugins/system/modalmessages/css/style.css');

		if ($this->params->def('loadjquery') == "yes")
		{
			$document->addScript("plugins/system/modalmessages/js/jquery-1.7.1.min.js");
		}

		$script = "	jQuery.noConflict();
					jQuery(document).ready(function()
					{
						jQuery('#modal-messages').css('margin-top', ((jQuery(window).height() - jQuery('#modal-messages').outerHeight())/2) + jQuery(window).scrollTop() + 'px');
						jQuery('#modal-messages').css('margin-left', ((jQuery(window).width() - jQuery('#modal-messages').outerWidth())/2) + jQuery(window).scrollLeft() + 'px');
						jQuery('#modal-messages').show();";

		if ($this->params->def('dimback') == "yes")
		{
    		$script .= "jQuery('#messages-overlay').show();

    					jQuery('#smwin_close_btn').click(function()
    					{
							jQuery('#messages-overlay').hide();
							jQuery('#modal-messages').hide();
    					});

    					jQuery('#messages-overlay').click(function()
    					{
							jQuery('#messages-overlay').hide();
							jQuery('#modal-messages').hide();
    					});";
    	}
    	else
    	{
        	$script .= "jQuery('#smwin_close_btn').click(function()
    					{
							jQuery('#modal-messages').hide();
    					});

    					jQuery('#messages-overlay').click(function()
    					{
							jQuery('#modal-messages').hide();
    					});";
    	}

		$script .= "});";

		$document->addScriptDeclaration($script);
	}

	function onAfterRender()
	{
		if(JFactory::getApplication()->isAdmin()) return true;

		$messages = JFactory::getApplication()->getMessageQueue();
		$content = "";

		if($messages)
		{
			foreach ($messages as $message)
			{
				if (($message['type'] != "message") && ($message['type'] != "notice") && ($message['type'] != "error"))
				{
					$type = "message";
				}
				else
				{
					$type = $message['type'];
				}

				$content .= "<span class='sm-".$type."'>".$message['message']."</span>";
			}

        	$output = JResponse::getBody();
        	$pattern = '/<\/body>/';

	        $replacement = '<div id="modal-messages" style="display: none;">
				<div id="smWindow" class="'.$type.'">
					<div id="smwin_tl"></div>
					<div id="smwin_tm"></div>
					<div id="smwin_tr"></div>
					<div class="clearfix"></div>
					<div id="smwin_ml">
						<div id="smwin_mr">
							<div id="smWindowContentOuter">
								<div id="smWindowContentTop">
									<a id="smwin_close_btn" href="javascript:void(0);" title="'.JText::_('PLG_SYSTEM_MODALMESSAGES_CLOSE').'"></a>
									<div id="logoContainer">
										<div id="smwin_logo"></div>
										<div id="smwin_title">'.JText::_('PLG_SYSTEM_MODALMESSAGES_TITLE').'</div>
									</div>
									<div class="clearfix"></div>
								</div>
								<div id="smWindowContentWrap">
									<div id="smWindowContent">'.$content.'</div>
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div id="smwin_bl"></div>
					<div id="smwin_bm"></div>
					<div id="smwin_br"></div>
					<div class="clearfix"></div>
				</div>
			</div>
			<div id="messages-overlay" style="display: none;"></div>
			</body>';
        	$output = preg_replace($pattern, $replacement, $output);
	        JResponse::setBody($output);
	        return true;
		}
	}
}

?>