<<<<<<< HEAD
<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
?>
<!--BEGIN Search Box 
<script type="text/javascript">
$(function(){
  $("#mod_virtuemart_search").keyup(function(){
     var search = $("#mod_virtuemart_search").val();
     $.ajax({
       type: "POST",
       url: "search.php",
       data: {"search": search},
       cache: false,                                
       success: function(response){
          $("#resSearch").html(response);
       }
     });
     return false;
   });
});
</script>
-->

<form action="<?php echo JRoute::_('index.php?option=com_virtuemart&view=category&search=true&limitstart=0&virtuemart_category_id='.$category_id ); ?>" method="get">
<div class="search<?php echo $params->get('moduleclass_sfx'); ?>">

<?php $output = '<input autocomplete="off" style="height:16px;vertical-align :middle;" name="keyword" id="mod_virtuemart_search" maxlength="'.$maxlength.'" alt="'.$button_text.'" class="inputbox'.$moduleclass_sfx.'" type="text" size="'.$width.'" value="'.$text.'"  onblur="if(this.value==\'\') this.value=\''.$text.'\';" onfocus="if(this.value==\''.$text.'\') this.value=\'\';" onkeyup="ajaxSearch(this.value,'."'".$cur_dir."'".',event);" onkeydown="prevent(event)" onkeypress="prevent(event)"/>';

			if ($moduleclass_sfx == 'hor') {$image = JURI::base().'components/com_virtuemart/assets/images/vmgeneral/lupa.png' ;
			$button = '<input style="vertical-align :middle;height:20px;margin: -22px; " type="image" value="'.$button_text.'" class="buttonsearch" src="'.$image.'" onclick="this.form.keyword.focus();"/>';
			}
			else {
				/*$image = JURI::base().'components/com_virtuemart/assets/images/vmgeneral/search.png' ;
				if ($button) :
				    if ($imagebutton) :
				        $button = '<input style="vertical-align :middle;height:16px;margin: 2px; border: 1px solid #CCC;" type="image" value="'.$button_text.'" class="button'.$moduleclass_sfx.'" src="'.$image.'" onclick="this.form.keyword.focus();"/>';
				    else :
			    	    $button = '<input type="submit" value="'.$button_text.'" class="button'.$moduleclass_sfx.'"  onclick="this.form.keyword.focus();"/>';
				    endif;
				endif;*/
/*default*/
				$image = JURI::base().'components/com_virtuemart/assets/images/vmgeneral/lupa.png' ;
				$button = '<input style="vertical-align :middle;height:20px;margin: -22px; " type="image" value="'.$button_text.'" class="buttonsearch" src="'.$image.'" onclick="this.form.keyword.focus();"/>';
				$button_pos = 'right';

			}
			switch ($button_pos) :
			    case 'top' :
				    $button = $button.'<br />';
				    $output = $button.$output;
				    break;

			    case 'bottom' :
				  //  $button = '<br />'.$button;
					$table = '<table width="213px"><tr><td width="75%" valign="top"><div id="resSearch" class="ajax_list"></div></td><td width="25%">'.$button.'</td></tr></table>';
				    $output = $output.$table;
				    break;

			    case 'right' :
				    $output = $output.'<div id="resSearch" class="ajax_list"></div>'.$button;
				    break;

			    case 'left' :
			    default :
				    $output = $button.$output;
				    break;
			endswitch;

			echo $output;
?>
</div>
		<input type="hidden" name="limitstart" value="0" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="view" value="category" />
	  </form>

=======
<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
?>
<!--BEGIN Search Box 
<script type="text/javascript">
$(function(){
  $("#mod_virtuemart_search").keyup(function(){
     var search = $("#mod_virtuemart_search").val();
     $.ajax({
       type: "POST",
       url: "search.php",
       data: {"search": search},
       cache: false,                                
       success: function(response){
          $("#resSearch").html(response);
       }
     });
     return false;
   });
});
</script>
-->

<form action="<?php echo JRoute::_('index.php?option=com_virtuemart&view=category&search=true&limitstart=0&virtuemart_category_id='.$category_id ); ?>" method="get">
<div class="search<?php echo $params->get('moduleclass_sfx'); ?>">

<?php $output = '<input autocomplete="off" style="height:16px;vertical-align :middle;" name="keyword" id="mod_virtuemart_search" maxlength="'.$maxlength.'" alt="'.$button_text.'" class="inputbox'.$moduleclass_sfx.'" type="text" size="'.$width.'" value="'.$text.'"  onblur="if(this.value==\'\') this.value=\''.$text.'\';" onfocus="if(this.value==\''.$text.'\') this.value=\'\';" onkeyup="ajaxSearch(this.value,'."'".$cur_dir."'".',event);" onkeydown="prevent(event)" onkeypress="prevent(event)"/>';

			if ($moduleclass_sfx == 'hor') {$image = JURI::base().'components/com_virtuemart/assets/images/vmgeneral/lupa.png' ;
			$button = '<input style="vertical-align :middle;height:20px;margin: -22px; " type="image" value="'.$button_text.'" class="buttonsearch" src="'.$image.'" onclick="this.form.keyword.focus();"/>';
			}
			else {
				/*$image = JURI::base().'components/com_virtuemart/assets/images/vmgeneral/search.png' ;
				if ($button) :
				    if ($imagebutton) :
				        $button = '<input style="vertical-align :middle;height:16px;margin: 2px; border: 1px solid #CCC;" type="image" value="'.$button_text.'" class="button'.$moduleclass_sfx.'" src="'.$image.'" onclick="this.form.keyword.focus();"/>';
				    else :
			    	    $button = '<input type="submit" value="'.$button_text.'" class="button'.$moduleclass_sfx.'"  onclick="this.form.keyword.focus();"/>';
				    endif;
				endif;*/
/*default*/
				$image = JURI::base().'components/com_virtuemart/assets/images/vmgeneral/lupa.png' ;
				$button = '<input style="vertical-align :middle;height:20px;margin: -22px; " type="image" value="'.$button_text.'" class="buttonsearch" src="'.$image.'" onclick="this.form.keyword.focus();"/>';
				$button_pos = 'right';

			}
			switch ($button_pos) :
			    case 'top' :
				    $button = $button.'<br />';
				    $output = $button.$output;
				    break;

			    case 'bottom' :
				  //  $button = '<br />'.$button;
					$table = '<table width="213px"><tr><td width="75%" valign="top"><div id="resSearch" class="ajax_list"></div></td><td width="25%">'.$button.'</td></tr></table>';
				    $output = $output.$table;
				    break;

			    case 'right' :
				    $output = $output.'<div id="resSearch" class="ajax_list"></div>'.$button;
				    break;

			    case 'left' :
			    default :
				    $output = $button.$output;
				    break;
			endswitch;

			echo $output;
?>
</div>
		<input type="hidden" name="limitstart" value="0" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="view" value="category" />
	  </form>

>>>>>>> 1e1b309bbf9e27aa09f1eef63d7bfeed85150451
<!-- End Search Box -->