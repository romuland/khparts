window.addEvent ('load', function() {
	var winScroller;
	if($('backtotop')) {
		winScroller = new Fx.Scroll(window);

		$('backtotop').addEvent('click', function(e) {
			e = new Event(e).stop();
			winScroller.toTop();
		});
	}	
	if($('shop1link') || $('shop2link')) {
		winScroller = winScroller || new Fx.Scroll(window);

		$('shop1link').addEvent('click', function(e) {
			e = new Event(e).stop();
			winScroller.toElement('shop1map');
		});
		$('shop2link').addEvent('click', function(e) {
			e = new Event(e).stop();
			winScroller.toElement('shop2map');
		});
	}	
});

window.addEvent ('scroll', function() {
	if (document.documentElement.scrollTop > 100){
		jQuery('#backtotop').fadeIn();
	} else {
		jQuery('#backtotop').fadeOut();
	}
});
window.addEvent('click', function(){
	//alert('2');
});

jQuery(document).ready(function(){
	/*4 видимых элемента - top, zt-header,ajax-content, zt-footer*/
	var winHeight = parseInt(jQuery(window).height());
	var currentHeight = parseInt(jQuery('#top').css("height")) + parseInt(jQuery('#zt-header').css("height")) + parseInt(jQuery('#ajax-content').css("height")) + parseInt(		jQuery('#zt-footer').css("height"));
	if(currentHeight < winHeight){
	//	jQuery('#ajax-content').css("height", parseInt(jQuery('#ajax-content').css("height")) + (winHeight - currentHeight) + "px");
	}

	document.documentElement.scrollTop > 100 ? jQuery('#backtotop').show():jQuery("#backtotop").hide();
	
	jQuery(function () {
		// scroll body to 0px on click
		jQuery('backtotop a').click(function () {
			jQuery('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});
	
	popupPositionCalcalated = false;
	popupWrapper = '#right-side-wrapper';
	
});

if(jQuery('div.tabs-block')){
	jQuery(function() {
	  jQuery('ul.tabs-title').delegate('li:not(.current)', 'click', function() {
   		jQuery(this).addClass('current').siblings().removeClass('current').parents('div.tabs-block').find('div.tab-content').eq(jQuery(this).index()).fadeIn(200).siblings('div.tab-content').hide();
		})
	})
}

function getMarginLeft(obj) {
	return Math.max(40, parseInt(jQuery(window).width()/2 - obj.width()/2));
}
 
function getMarginTop(obj) {
	return Math.max(40, parseInt(jQuery(window).height()/2 - obj.height()/2));
}

function calculatePostitionPopup(popupElName){
	dlg_width = popupElName.width();
	dlg_height = popupElName.height();
	dlg_marginLeft = getMarginLeft(popupElName);
	dlg_marginTop = getMarginTop(popupElName);
	popupPositionCalcalated = true;
}

function showPopup(popup){
	var wrapper = jQuery(popupWrapper, document);
	if(popup.id == "callback") {
		mainElement = jQuery("#cb-dlg", wrapper);
		/*очищаем форму*/
		jQuery('#cb-in-name').val("");
		jQuery('#cb-in-phone').val("");
		changeRadio('cb-in-time', "time", jQuery("#cb-in-time input:radio[name=time]").first());	
		jQuery('#cb-antispam').val("");
		showButton_CB();
	}
	else if(popup.id == "question") {
		mainElement = jQuery("#q-dlg", wrapper);
		jQuery('#q-in-name').val("");
		jQuery('#q-in-email').val("");
		jQuery('#q-in-message').val("");
		jQuery('#q-antispam').val("");
		showButton_Q();
	}
	if(!popupPositionCalcalated) calculatePostitionPopup(mainElement);
	

	wrapper.show();
	
	/*Форма появляется справа, поэтому берем сначала правый край, потом присваем ему 0, потом возвращаем обратно*/
	var right = mainElement.css('right');
	
	mainElement.animate({ right: 0, marginTop: dlg_marginTop + (dlg_height/2) + 'px', width:0, height:0}, 0 );
	mainElement.show();

	mainElement.animate({
		marginLeft: dlg_marginLeft + 'px', right: dlg_marginLeft + 'px', marginTop: dlg_marginTop + 'px', width: dlg_width, height: dlg_height
		}, 500, function(){
			//jQuery("#cb-wrapper").fadeTo('slow', 0.7)
			}
		);
}

function closePopup(){
		mainElement.animate({
				right: 0, marginTop: dlg_marginTop + (dlg_height/2) + 'px', width: 0, height: 0
			}, 500, function(){mainElement.hide();jQuery("#right-side-wrapper").hide();}
		);
}

function stopBubble(event){
    event = event || window.event // кросс-браузерно
    if (event.stopPropagation) {
        // Вариант стандарта W3C:
        event.stopPropagation()
    } else {
        // Вариант Internet Explorer:
        event.cancelBubble = true
    }
}

function changeRadio(parent, radioName, obj){		

	var curChecked = jQuery("#" + parent + " input:radio[name=" + radioName + "]:checked");
	curChecked.removeAttr("checked");
	curChecked.addClass("RadioClass").removeClass("RadioSelected");
	curChecked.next(".LabelSelected").addClass("RadioLabelClass").removeClass("LabelSelected");
	
	var newChecked = jQuery(obj);
	newChecked.addClass("LabelSelected").removeClass("RadioLabelClass");
	newChecked.prev(".RadioClass").attr("checked", "checked");
	newChecked.prev(".RadioClass").addClass("RadioSelected").removeClass("RadioClass");
}

function showButton_CB(){
	if(jQuery('#cb-in-name').val() != "" && jQuery('#cb-in-phone').val() != "" && jQuery('#cb-antispam').val() == "330") {
		jQuery("#cb-submit").css('display','block');}
	else jQuery("#cb-submit").css('display','none');
}

function showButton_Q(){
	if(jQuery('#q-in-name').val() != "" && jQuery('#q-in-email').val() != "" && jQuery('#q-in-message').val() != "" && jQuery('#q-antispam').val() == "330") {
		jQuery("#q-submit").css('display','block');}
	else jQuery("#q-submit").css('display','none');
}

function sendMail_CB(obj){
    jQuery.ajax({  
    	url: "/libraries/sendmail_callback.php",  
		type: "POST",
		data: {name:jQuery('#cb-in-name').val(), phone:jQuery('#cb-in-phone').val(), time:jQuery("#cb-in-time input:radio[name=time]:checked").val()},
        dataType: "html",
        cache: false,  
        success: function(html){  
           alert(html);  
		   closePopup();
        }  
    });    
	return false;
}

function sendMail_Q(obj){
	
    jQuery.ajax({  
    	url: "/libraries/sendmail_question.php",  
		type: "POST",
		data: {name:jQuery('#q-in-name').val(), email:jQuery('#q-in-email').val(), message:jQuery("#q-in-message").val()},
        dataType: "html",
        cache: false,  
        success: function(html){  
           alert(html);  
		   closePopup();
        }  
    });    
	return false;
}

function ZTmakeEqualHeight(divs){if(!divs||divs.length<2)return;var maxh=0;divs.each(function(el,i){if($(el)){var ch=$(el).getCoordinates().height;maxh=(maxh<ch)?ch:maxh;}},this);divs.each(function(el,i){if($(el)){$(el).setStyle('min-height',maxh-($(el).getStyle('padding-top').toInt())-($(el).getStyle('padding-bottom').toInt()));}},this);}
window.addEvent('load',function(){ZTmakeEqualHeight(['zt-c-left','zt-c-content','zt-c-right']);});