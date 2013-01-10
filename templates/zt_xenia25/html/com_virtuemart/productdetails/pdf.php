<?php
defined('_JEXEC') or die('Restricted access');

// addon for joomla modal Box
JHTML::_('behavior.modal');
// JHTML::_('behavior.tooltip');
$url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component');
$document = JFactory::getDocument();
$document->addScriptDeclaration("
	jQuery(document).ready(function($) {
		$('a.ask-a-question').click( function(){
			$.facebox({
				iframe: '" . $url . "',
				rev: 'iframe|550|550'
			});
			return false ;
		});
	/*	$('.additional-images a').mouseover(function() {
			var himg = this.href ;
			var extension=himg.substring(himg.lastIndexOf('.')+1);
			if (extension =='png' || extension =='jpg' || extension =='gif') {
				$('.main-image img').attr('src',himg );
			}
			console.log(extension)
		});*/
	});
");
/* Let's see if we found the product */
if (empty($this->product)) {
    echo JText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
    echo '<br /><br />  ' . $this->continue_link_html;
    return;
}

$options = array(
    'onActive' => 'function(title, description){
        description.setStyle("display", "block");
        title.addClass("open").removeClass("closed");
    }',
    'onBackground' => 'function(title, description){
        description.setStyle("display", "none");
        title.addClass("closed").removeClass("open");
    }',
    'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
    'useCookie' => true, // this must not be a string. Don't use quotes.
);
 
 ?> 

<div class="productdetails-wrapper1">
<?php /*echo JHtml::_('tabs.start', 'tab_group_id', $options);
echo JHtml::_('tabs.panel', JText::_('Описание'), 'panel_1_id');*/?>
<div class="productdetails-view">

<?php
    // Product Navigation
    if (VmConfig::get('product_navigation', 1)) {
	?>
        <div class="product-neighbours">
	    <?php
	    if (!empty($this->product->neighbours ['previous'][0])) {
		$prev_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['previous'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id);
		echo JHTML::_('link', $prev_link, $this->product->neighbours ['previous'][0]
			['product_name'], array('class' => 'previous-page'));
	    }
	    if (!empty($this->product->neighbours ['next'][0])) {
		$next_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['next'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id);
		echo JHTML::_('link', $next_link, $this->product->neighbours ['next'][0] ['product_name'], array('class' => 'next-page'));
	    }
	    ?>
    	<div class="clear"></div>
        </div>
    <?php } // Product Navigation END
    ?>

	<?php // Back To Category Button
	if ($this->product->virtuemart_category_id) {
		$catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id);
		$categoryName = $this->product->category_name ;
	} else {
		$catURL =  JRoute::_('index.php?option=com_virtuemart');
		$categoryName = jtext::_('COM_VIRTUEMART_SHOP_HOME') ;
	}
	?>
	<div class="back-to-category">
    	<a href="<?php echo $catURL ?>" class="product-details" title="<?php echo $categoryName ?>"><?php echo JText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO',$categoryName) ?></a>
	</div>

    <?php // Product Title   ?>
    <h1><?php echo $this->product->product_name ?></h1>
    <?php // Product Title END   ?>

    <?php // afterDisplayTitle Event
    echo $this->product->event->afterDisplayTitle ?>

    <?php
    // Product Edit Link
    echo $this->edit_link;
    // Product Edit Link END
    ?>

    <?php
    // PDF - Print - Email Icon
    if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_button_enable')) {
	?>
        <div class="icons">
	    <?php
	    //$link = (JVM_VERSION===1) ? 'index2.php' : 'index.php';
	    $link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;
	    $MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';

	    if (VmConfig::get('pdf_icon', 1) == '1') {
		echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_button_enable', false);
	    }
	    echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
	    echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend');
	    ?>
    	<div class="clear"></div>
        </div>
    <?php } // PDF - Print - Email Icon END
    ?>

    <?php
    // Product Short Description
    if (!empty($this->product->product_s_desc)) {
	?>
        <div class="product-short-description">
	    <?php
	    /** @todo Test if content plugins modify the product description */
	   /* echo nl2br($this->product->product_s_desc);*/
	    ?>
        </div>
	<?php
    } // Product Short Description END
	?>

    <div class="product_desc width50 floatleft">
    <?php
	$car_name = '';
	foreach($this->product->customfieldsSorted as $s_field){
		foreach($s_field as $field){
			if($field->custom_title == "Car Name"){
			$car_name = $field->custom_value;
			}
		}
	}

	if(strpos($car_name, $categoryName) > 0){
		$temp = trim(substr($car_name, 0, strpos($car_name, $categoryName)));

		if(strpos($temp , "~") > 0){
			$temp = trim(substr($temp, strrpos($temp, "~") + 1));
if(strpos($temp , " ") > 0) $car_name = trim(substr($temp, -1))." ".$categoryName;	
else $car_name = $temp." ".$categoryName;	
			
			$car_name = trim(str_replace("~", "", $car_name));
		}else $car_name =trim( $temp." ".$categoryName);
	} else if ($categoryName != 'Сопутствующие товары')$car_name = $categoryName;
	
	echo "<table cols='2' style='margin-bottom:10px' width='100%'>";
    echo "<tr>";
    echo "<td width='40%'>Номер:</td>";
	echo "<td width='60%'><h3>".$this->product->product_sku."</h3></td>";
	echo "</tr>";
	echo "<tr>";
    echo "<td>Производитель:</td>";
	echo "<td><a href='/home/proizvoditeli/".$this->product->mf_name."'>".$this->product->mf_name."</h4></td>";
	echo "</tr>";
	echo "<tr>";
	if ($categoryName == 'Сопутствующие товары') echo "<td>Марка:</td>";
    else echo "<td>Модель автомобиля:</td>";
	echo "<td><h3>".$car_name."</h3></td>";
	echo "</tr>";
	echo "<tr>";
   	echo "<td class=''>Наличие:</td>";
	if ($this->product->stock->stock_level == "nostock") 
		echo "<td>"."<span>Нет в наличии.</span>"."</td>";
	else	
		echo "<td>"."<span>".$this->product->product_in_stock." шт.</span>"."</td>";
	echo "</tr>";
	
			
	$price = $this->product->prices["basePrice"];
	$price = round($price, 2);
	
	if(strpos($price, ".") > 0) {
		if(!preg_match("/\.\d{2}$/", $price)) $price .= 0;
		$temp = substr($price, 0, strpos($price, "."))." руб. ";
		$price = $temp.substr($price, strpos($price, ".") + 1)." коп.";

	}else{
		$price = $price." руб.";
		}
	echo "<tr><td> </td></tr>";	
	echo "<tr>";
		echo "<td class=''>Цена:</td>";
		echo "<td><span><strong>".$price."</strong></span></td>";
	echo "</tr>";
	echo "</table>";	


			if ($this->show_prices and (empty($this->product->images[0]) or $this->product->images[0]->file_is_downloadable == 0)) {
		 /*   echo $this->loadTemplate('showprices');*/
		}
	?>  


    <?php
    if (!empty($this->product->customfieldsSorted['ontop'])) {
	$this->position = 'ontop';
	echo $this->loadTemplate('customfields');
    } // Product Custom ontop end
    ?>
     </div>
	<?php if(!empty($this->product->images) && count($this->product->images)>1) {?>
    	<div>
		<div class="width50 floatleft">
	<?php
		echo $this->loadTemplate('images');
	?>
		</div>
	    <div class="width50 floatright">
	<?php } else {?>
		<div class="width50 floatleft">
	<?php } ?>

	    <div class="spacer-buy-area">

		<?php
		// TO DO in Multi-Vendor not needed at the moment and just would lead to confusion
		/* $link = JRoute::_('index2.php?option=com_virtuemart&view=virtuemart&task=vendorinfo&virtuemart_vendor_id='.$this->product->virtuemart_vendor_id);
		  $text = JText::_('COM_VIRTUEMART_VENDOR_FORM_INFO_LBL');
		  echo '<span class="bold">'. JText::_('COM_VIRTUEMART_PRODUCT_DETAILS_VENDOR_LBL'). '</span>'; ?><a class="modal" href="<?php echo $link ?>"><?php echo $text ?></a><br />
		 */
		?>

		<?php
		if ($this->showRating) {
		    $maxrating = VmConfig::get('vm_maximum_rating_scale', 5);

		    if (empty($this->rating)) {
			?>
			<span class="vote"><?php echo JText::_('COM_VIRTUEMART_RATING') . ' ' . JText::_('COM_VIRTUEMART_UNRATED') ?></span>
			    <?php
			} else {
			    $ratingwidth = $this->rating->rating * 24; //I don't use round as percetntage with works perfect, as for me
			    ?>
			<span class="vote">
	<?php echo JText::_('COM_VIRTUEMART_RATING') . ' ' . round($this->rating->rating) . '/' . $maxrating; ?><br/>
			    <span title=" <?php echo (JText::_("COM_VIRTUEMART_RATING_TITLE") . round($this->rating->rating) . '/' . $maxrating) ?>" class="ratingbox" style="display:inline-block;">
				<span class="stars-orange" style="width:<?php echo $ratingwidth.'px'; ?>">
				</span>
			    </span>
			</span>
			<?php
		    }
		}
		if (is_array($this->productDisplayShipments)) {
		    foreach ($this->productDisplayShipments as $productDisplayShipment) {
			echo $productDisplayShipment . '<br />';
		    }
		}
		if (is_array($this->productDisplayPayments)) {
		    foreach ($this->productDisplayPayments as $productDisplayPayment) {
			echo $productDisplayPayment . '<br />';
		    }
		}
		// Product Price
		//показываем свою цену в основной секции
		/*
		if ($this->show_prices and (empty($this->product->images[0]) or $this->product->images[0]->file_is_downloadable == 0)) {
		    echo $this->loadTemplate('showprices');
		}*/
		?>

		<?php
		// Add To Cart Button
// 			if (!empty($this->product->prices) and !empty($this->product->images[0]) and $this->product->images[0]->file_is_downloadable==0 ) {
		if (!VmConfig::get('use_as_catalog', 0) and !empty($this->product->prices)) {
		    echo $this->loadTemplate('addtocart');
		}  // Add To Cart Button END
		?>

		<?php
		// Availability Image
		$stockhandle = VmConfig::get('stockhandle', 'none');
		if (($this->product->product_in_stock - $this->product->product_ordered) < 1) {
			if ($stockhandle == 'risetime' and VmConfig::get('rised_availability') and empty($this->product->product_availability)) {
			?>	<div class="availability">
			    <?php echo (file_exists(JPATH_BASE . DS . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability'))) ? JHTML::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability', '7d.gif'), VmConfig::get('rised_availability', '7d.gif'), array('class' => 'availability')) : VmConfig::get('rised_availability'); ?>
			</div>
		    <?php
			} else if (!empty($this->product->product_availability)) {
			?>
			<div class="availability">
			<?php echo (file_exists(JPATH_BASE . DS . VmConfig::get('assets_general_path') . 'images/availability/' . $this->product->product_availability)) ? JHTML::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . $this->product->product_availability, $this->product->product_availability, array('class' => 'availability')) : $this->product->product_availability; ?>
			</div>
			<?php
			}
		}
		?>


<?php
// Ask a question about this product
if (VmConfig::get('ask_question', 1) == '1') {
    ?>
<!--    		<div class="ask-a-question">-->
<!--     		    <a class="ask-a-question" href="<?php/* echo $url ?>" ><?php /*echo JText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL')*/ ?></a>-->
    		    <!--<a class="ask-a-question modal" rel="{handler: 'iframe', size: {x: 700, y: 550}}" href="<?php echo $url ?>"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>-->
    		</div>
		<?php }
		?>

		<?php
		// Manufacturer of the Product
		if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) {
		    echo $this->loadTemplate('manufacturer');
		}
		?>

	    </div>

	<div class="clear"></div>
    </div>

	<?php // event onContentBeforeDisplay
	echo $this->product->event->beforeDisplayContent; ?>

	<?php
	// Product Description
	if (!empty($this->product->product_desc)) {
	    ?>
		<div class="clear"></div>
        <div class="product-description" >
	<?php /** @todo Test if content plugins modify the product description */ ?>
    	<span class="title"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE') ?></span>
	<?php echo $this->product->product_desc; ?>
        </div>

	<?php
    } // Product Description END

/*    if (!empty($this->product->customfieldsSorted['normal'])) {
	$this->position = 'normal';
	echo $this->loadTemplate('customfields');
    } // Product custom_fields END*/
    // Product Packaging
    $product_packaging = '';
    if ($this->product->packaging || $this->product->box) {
	?>
        <div class="product-packaging">

	    <?php
	    if ($this->product->packaging) {
		$product_packaging .= JText::_('COM_VIRTUEMART_PRODUCT_PACKAGING1') . $this->product->packaging;
		if ($this->product->box)
		    $product_packaging .= '<br />';
	    }
	    if ($this->product->box)
		$product_packaging .= JText::_('COM_VIRTUEMART_PRODUCT_PACKAGING2') . $this->product->box;
	    echo str_replace("{unit}", $this->product->product_unit ? $this->product->product_unit : JText::_('COM_VIRTUEMART_PRODUCT_FORM_UNIT_DEFAULT'), $product_packaging);
	    ?>
        </div>
    <?php } // Product Packaging END
    ?>

    <?php
    // Product Files
    // foreach ($this->product->images as $fkey => $file) {
    // Todo add downloadable files again
    // if( $file->filesize > 0.5) $filesize_display = ' ('. number_format($file->filesize, 2,',','.')." MB)";
    // else $filesize_display = ' ('. number_format($file->filesize*1024, 2,',','.')." KB)";

    /* Show pdf in a new Window, other file types will be offered as download */
    // $target = stristr($file->file_mimetype, "pdf") ? "_blank" : "_self";
    // $link = JRoute::_('index.php?view=productdetails&task=getfile&virtuemart_media_id='.$file->virtuemart_media_id.'&virtuemart_product_id='.$this->product->virtuemart_product_id);
    // echo JHTMl::_('link', $link, $file->file_title.$filesize_display, array('target' => $target));
    // }
    if (!empty($this->product->customfieldsRelatedProducts)) {
	echo $this->loadTemplate('relatedproducts');
    } // Product customfieldsRelatedProducts END

    if (!empty($this->product->customfieldsRelatedCategories)) {
	echo $this->loadTemplate('relatedcategories');
    } // Product customfieldsRelatedCategories END
    // Show child categories
    if (VmConfig::get('showCategory', 1)) {
	echo $this->loadTemplate('showcategory');
    }
    if (!empty($this->product->customfieldsSorted['onbot'])) {
    	$this->position='onbot';
    	echo $this->loadTemplate('customfields');
    } // Product Custom ontop end


//echo JHtml::_('tabs.panel', JText::_('Комментарии'), 'panel_2_id');
     ?>
<?php // onContentAfterDisplay event
echo $this->product->event->afterDisplayContent; 

//$count = JComments::getCommentsCount($this->product->virtuemart_product_id, 'com_virtuemart');
//echo $count;

//echo JHtml::_('tabs.end');
?>

<?php
echo $this->loadTemplate('reviews');
?>
</div>