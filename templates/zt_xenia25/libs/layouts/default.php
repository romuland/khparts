<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>">
<head>
<jdoc:include type="head" />
<script src=""></script>
<?php JHTML::_('behavior.mootools'); ?>
<?php
		$document = JFactory::getDocument();

	//	require_once($ztTools->templateurl().'compress_timestamp.php');         //загрузить timestamp сохранённый в файле, чтобы обмануть кэширование. Устанавливает $compress_stamp=unix_timestamp                       
	$compress_stamp=1353395767;               
		//if (stripos($_SERVER['HTTP_ACCEPT_ENCODING'],'GZIP')!==false)   
        //	$gz='gz';
		//else
        //	$gz=null;
//echo '<link rel="stylesheet" type="text/css" href="min/css_schedule_'.$compress_stamp.'.css'.$gz.'" />',PHP_EOL;
		
		$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
		//$document->addScript($ztTools->templateurl().'min/js_schedule_'.$compress_stamp.'.js'.$gz);
		
		$document->addScript($ztTools->templateurl().'js/noconflict.js');
		
		$document->addScript($ztTools->templateurl().'js/ajax_search.js');
		$document->addScript($ztTools->templateurl() . 'js/zt.script.js');
		
		$document->addStyleSheet($ztTools->baseurl() . 'templates/system/css/system.css');
		$document->addStyleSheet($ztTools->baseurl() . 'templates/system/css/general.css');
		$document->addStyleSheet($ztTools->templateurl() . 'css/default.css');
		$document->addStyleSheet($ztTools->templateurl() . 'css/template.css');
		$document->addStyleSheet($ztTools->templateurl() . 'css/patterns.css');
		$document->addStyleSheet($ztTools->templateurl() . 'css/fonts.css');
	?>
	
    <script type="text/javascript">
		(function(d,w,c){(w[c]=w[c]||[]).push(function(){try{w.yaCounter17029624=new Ya.Metrika({id:17029624,clickmap:true,accurateTrackBounce:true,webvisor:true});}catch(e){}});var n=d.getElementsByTagName("script")[0],s=d.createElement("script"),f=function(){n.parentNode.insertBefore(s, n);};s.type="text/javascript";s.async=true;s.src=(d.location.protocol=="https:"?"https:":"http:")+"//mc.yandex.ru/metrika/watch.js";if(w.opera=="[object Opera]"){d.addEventListener("DOMContentLoaded",f);}else{f();}})(document,window,"yandex_metrika_callbacks");

	</script>

	<!--<link href='http://fonts.googleapis.com/css?family=Orbitron' rel='stylesheet' type='text/css'>-->
   	<!--<link href='http://fonts.googleapis.com/css?family=Kelly+Slab' rel='stylesheet' type='text/css'>-->
    
	<link rel="stylesheet" href="<?php echo $ztTools->templateurl(); ?>css/modules.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $ztTools->templateurl(); ?>css/css3.php?url=<?php echo $ztTools->templateurl(); ?>" type="text/css" />
    
	<!--[if lte IE 6]>
	<link rel="stylesheet" href="<?php/* echo $ztTools->templateurl(); */?>css/ie6.css" type="text/css" />
	<script type="text/javascript" src="<?php/* echo $ztTools->templateurl()*/ ?>js/ie_png.js"></script>
	<script type="text/javascript">
	window.addEvent ('load', function() {
	   ie_png.fix('.png');
	});
	</script>
	<![endif]-->
	<!--[if lte IE 7]>
	<link rel="stylesheet" href="<?php echo $ztTools->templateurl(); ?>css/ie7.css" type="text/css" />
	<![endif]-->

<?php
include_once (dirname(__FILE__).DS.'header.php');
?>

</head>
<body id="bd" class="fs<?php echo $ztTools->getParam('zt_font'); ?> <?php echo $ztrtl; ?> <?php echo $ztTools->getPageClassSuffix(); ?> clearfix <?php echo $ztTools->getParamsValue($prefix, 'image', 'bd');?>">

<div id="zt-wrapper" >
	<div id="zt-wrapper-inner">
    	<!--Top-->
        	<div id="top" class="clearfix pattern9">
			<div class="zt-wrapper">
				<div id="top-inner">
                <table>
                	<tr>
                    	<td id="top-left">
							<div><jdoc:include type="modules" name="top-left" /></div>
	                     </td>   
                         <td id="top-right-side">	
                        	<div><jdoc:include type="modules" name="top-right-side" /></div>
                        </td>     
	                 </tr>       
                     </table>
				</div>
			</div>
		</div>	
       
    		<div id="zt-header" class="clearfix">
		<!--#begin Header-->
			<div class="zt-wrapper">
				<div id="zt-header-inner">

					<div id="zt-logo">
						<h1 class="zt-logo"><a class="png" href="<?php echo $ztTools->baseurl() ; ?>" title="<?php echo $ztTools->sitename(); ?>">
							<span><?php echo $ztTools->sitename() ; ?></span></a>
						</h1>
					</div>

					<div id="zt-mainmenu">
						<div id="zt-mainmenu-inner">
							<?php $menu->show(); ?>
						</div>
					</div>

				</div>	
			</div>	
		</div>	
        
		<!--#end Header-->
        <div id="ajax-content">
        <jdoc:include type="modules" name="content" />
        
		<?php  if($this->countModules('slideshow')) : ?>							
		<div id="zt-slideshow" class="clearfix <?php echo $ztTools->getParamsValue($prefix, 'image', 'zt-slideshow');?>">
			<div class="zt-slideshow-wrapper">
				<div id="zt-slideshow-inner">
					<jdoc:include type="modules" name="slideshow" />
				</div>
			</div>
		</div>
		<?php endif; ?>
        
		<!-- #Main -->
		<div id="zt-mainframe" class="clearfix <?php echo $ztTools->getPageClassSuffix(); ?>">
			<div class="zt-wrapper">
					<div id="zt-container<?php echo $zt_width; ?>" class="clearfix zt-layout<?php echo $ztTools->getParam('zt_layout'); ?>">
							<div id="zt-content">
								<?php if($this->countModules('breadcrumb')) : ?>
									<!-- Breadcrumb -->
									<div id="zt-breadcrumbs" class="clearfix">
										<jdoc:include type="modules" name="breadcrumb" />
									</div>
									<!-- #Breadcrumb -->
								<?php  endif ; ?>
                                    
								<?php if($this->countModules('horizontal-search')) : ?>
									<!-- horizontal-search -->
									<div id="horizontal-search" class="clearfix">
										<div id="horizontal-search-inner">
											<jdoc:include type="modules" name="horizontal-search" />
										</div>
									</div>
									<!-- #horizontal-search -->
								<?php endif ; ?>
                                                         
								<div id="zt-component" class="clearfix">
									<div id="zt-component-inner">
										<jdoc:include type="message" />
										<jdoc:include type="component" />
									</div>
								</div>
							</div>

							<?php if($this->countModules('right')) : ?>
							<div id="zt-right">
								<div id="zt-right-inner" class="side-module">
									<jdoc:include type="modules" name="right" style="ztxhtml" />
									<div class="clearfix"></div>
								</div>
                          		<?php if($this->countModules('right-login')) : ?>
								<div id="zt-right-login">
									<jdoc:include type="modules" name="right-login" style="ztxhtml" />
								</div>
                                <?php endif; ?>  
							</div>
							<?php endif; ?>
						</div>
					</div>
                    </div>
               </div>
		</div>
		<!-- #End Main -->

		<!-- footer -->
        <noscript><div><img src="//mc.yandex.ru/watch/17029624" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
		<div><img src="//mc.yandex.ru/watch/17029624"; style="position:absolute; left:-9999px;" alt="" /></div>
		<div id="zt-footer" class="clearfix pattern9">
			<div class="zt-wrapper">
				<div id="zt-footer-inner">	
					<div id="zt-copyright">
						
						<?php if($this->countModules('footer-menu')) : ?>
							<div id="zt-footer-menu">	
								<jdoc:include type="modules" name="footer-menu" />
							</div>		
						<?php endif; ?>
						
						<?php if($ztTools->getParam('zt_footer')) : ?>
						<?php echo $ztTools->getParam('zt_footer_text'); ?>
						<?php else : ?>
						Copyright &copy; <?php echo Date('Y');?> &nbsp;&nbsp;<a style='' href="www.khparts.ru">khparts.ru</a>&nbsp;&nbsp;&nbsp;All rights reserved.
						<?php endif; ?>
					</div>
                    <?php if($this->countModules('sitemap')) : ?>
                    <div id="zt-sitemap" class="mainfont">	
						<jdoc:include type="modules" name="sitemap" />
                     </div> 
					<?php endif; ?>						
					<?php if($this->countModules('footer')) : ?>
                    <div id="zt-footer-right" class="mainfont">	
						<jdoc:include type="modules" name="footer" />
                     </div> 
					<?php endif; ?>	
                    

				</div>
			</div>
		</div>		
		<!-- #footer -->

	</div>
    <p id="backtotop">
		<a href="#"><span></span></a>
 	</p>
    
    <div id="right-side">	
	    <div id="callback" onclick="showPopup(this)">	
		</div>	
	    <div id="question" onclick="showPopup(this)">	
		</div>	
	</div>	
    
    <div id="right-side-wrapper" onclick="closePopup()">

	<div id="cb-dlg" onclick="stopBubble(event)">
    	<div id="popup-close" onclick="closePopup()"></div>    
	    <div id="cb-form">
	        <form action="" method="post" name="callback" onsubmit="return sendMail_CB(this)">
            	<h2>Перезвоните мне</h2>
	            <div class="popup-input">
	            	<strong><span>Ваше имя</span><span class="star">*</span></strong><br />
	            	<input type="text" name="name" id="cb-in-name" class="inputbox" onkeyup="showButton_CB()" onchange="showButton_CB()"/>
	            </div>
	            <div class="popup-input">
	            	<strong><span>Контактный телефон</span><span class="star">*</span></strong><br />
	            	<input type="text" name="phone" id="cb-in-phone" class="inputbox" onkeyup="showButton_CB()" onchange="showButton_CB()"/>
	            </div>
	            <div class="popup-input">
	            	<span><strong>Удобное время звонка</strong></span>
	            	<div id="cb-in-time" class="popup-input">
                    	<input type="radio" name="time" value="Как можно скорее" class="RadioSelected" checked="checked"><label class="LabelSelected" class = "defaultradio" onclick="changeRadio('cb-in-time', 'time', this)">Как можно скорее</label>
                        <input type="radio" name="time" value="09:00-14:00" class="RadioClass"><label class="RadioLabelClass" onclick="changeRadio('cb-in-time', 'time', this)">09:00-14:00</label><br />
						<input type="radio" name="time" value="14:00-18:00" class="RadioClass"><label class="RadioLabelClass" onclick="changeRadio('cb-in-time', 'time', this)">14:00-18:00</label>
						<input type="radio" name="time" value="18:00-22:00" class="RadioClass"><label class="RadioLabelClass" onclick="changeRadio('cb-in-time', 'time', this)">18:00-22:00</label><br />
                    </div>
	            </div>
                <div class="popup-input">
	            	<strong><span>Защита от спама</span><span class="star">*</span></strong><br />
                    <span>Сколько будет <span style="font-family:'Verdana, Geneva, sans-serif'">ЗЗ х 1О </span>= </span>
	            	<input autocomplete="off" type="text" name="antispam" id="cb-antispam" class="inputbox" onkeyup="showButton_CB()" onchange="showButton_CB()" />
	            </div>
	            <div id="cb-submit">
	            	<input type="submit" value="Жду звонка" class="button"/>
	            </div>
	    	</form>	    	
		</div>
	</div>
    
 	<div id="q-dlg" onclick="stopBubble(event)">
    	<div id="popup-close" onclick="closePopup()"></div>    
	    <div id="q-form">
	        <form action="" method="post" name="question" onsubmit="return sendMail_Q(this)">
            	<h2>Задать вопрос</h2>
	            <div class="popup-input">
	            	<span>Ваше имя</span><span class="star">*</span><br />
	            	<input type="text" name="name" id="q-in-name" class="inputbox" onkeyup="showButton_Q()" onchange="showButton_Q()"/>
	            </div>
	            <div class="popup-input">
	            	<span>Email для ответа</span><span class="star">*</span><br />
	            	<input type="text" name="email" id="q-in-email" class="inputbox" onkeyup="showButton_Q()" onchange="showButton_Q()"/>
	            </div>
	            <div class="popup-input">
	            	<span>Ваш вопрос</span><span class="star">*</span><br />
                    <textarea name="message" id="q-in-message" class="inputbox"  onkeyup="showButton_Q()" onchange="showButton_Q()"></textarea>
	            </div>
                <div class="popup-input">
	            	<strong><span>Защита от спама</span><span class="star">*</span></strong><br />
                    <span>Сколько будет <span style="font-family:'Verdana, Geneva, sans-serif'">ЗЗ х 1О </span>= </span>
	            	<input autocomplete="off" type="text" name="antispam" id="q-antispam" class="inputbox" onkeyup="showButton_Q()" onchange="showButton_Q()"/>
	            </div>                
	            <div id="q-submit">
	            	<input type="submit" value="Отправить сообщение" class="button"/>
	            </div>
	    	</form>	    	
		</div>
	</div>
       
	</div>
</div>

<jdoc:include type="modules" name="debug" />
<?php
	if($this->params->get('zt_change_color')) {
		include_once (dirname(__FILE__).DS.'footer.php');
	}
?>
	
</body>
</html>