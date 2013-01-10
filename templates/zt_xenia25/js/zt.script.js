window.addEvent ('scroll', function() {
	if (document.documentElement.scrollTop > 100){
		jQuery('#backtotop').fadeIn();
	} else {
		jQuery('#backtotop').fadeOut();
	}
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
var activeDiv;
var firstDiv = false;
var ajax_list_currentLetters = new Array();
var ajax_optionDiv = false;
var ajax_optionDiv_iframe = false;

var currentValue = '';

function ajaxSearch(val, handlerPath, event){
	/*обрабатываем нажатие Enter и Esc*/
	if((event.keyCode == 9 || event.keyCode == 13) && !activeDiv) return;
	if(event.keyCode == 27) {hideDiv(); return;}
	
	/*если не ввели ничего нового, пропускаем обработку клавиш*/

	
	var START_FROM_SYMB_COUNT = 1;
	
	if(val.length > START_FROM_SYMB_COUNT){
		document.body.onkeydown=keyboardNavigation(event);
		
		if (currentValue == val) return;
		currentValue = val;
		var http = createRequest();
		searchValue = encodeURIComponent(val);
		ajax_optionDiv = document.getElementById('resSearch');
		ajax_optionDiv.style.display = "block";
		ajax_optionDiv.innerHTML = "Поиск...  <strong>" + searchValue+"";

		// Set te random number to add to URL request
		nocache = Math.random();
		//alert(handlerPath+'search.php?name='+searchValue+'&nocache='+nocache);
		activeDiv = false;

		http.onreadystatechange = function(){
			if(http.readyState == 4){
				
				var responseRaw = http.responseText;
//				var response = JSON.parse(responseRaw);
	//var fullname = contact.surname + ", " + contact.firstname;
//alert(response.row[0].product_sku);
				if (responseRaw == '') 	ajax_optionDiv.innerHTML = 'Нет результатов';
				else					ajax_optionDiv.innerHTML = responseRaw;
				firstDiv = ajax_optionDiv.firstChild;
			}
		};
		//handlerPath=''
		target_url = handlerPath +'\\libraries\\search.php';
		target_url = '/libraries/search.php';
		
		//alert($target_url);
		http.open('get', target_url+'?search='+searchValue+'&nocache='+nocache);
		http.send(null);

	}else hideDiv();
}


function createRequest() {
	var request;
	var browser = navigator.appName;
	if(browser == "Microsoft Internet Explorer"){
		request = new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		request = new XMLHttpRequest();
	}
	return request;
}

	function rollOverActiveDiv(div,fromKeyBoard)
	{
		if(activeDiv) activeDiv.className = 'optionDiv';
		div.className='optionDivSelected';
		activeDiv = div;

		if(fromKeyBoard){
			if(activeDiv.offsetTop>ajax_optionDiv.offsetHeight){
				ajax_optionDiv.scrollTop = activeDiv.offsetTop - ajax_optionDiv.offsetHeight + activeDiv.offsetHeight + 2 ;
			}
			if(activeDiv.offsetTop<ajax_optionDiv.scrollTop)
			{
				ajax_optionDiv.scrollTop = 0;
			}
		}
	}
	
	function selectDiv(div){
		document.getElementById('mod_virtuemart_search').value = div.innerHTML;
		ajax_optionDiv.style.display='none';
		activeDiv = false;
	}
	
	/*удалили все - очищаем выбор*/
	
	/*навигация стрелками*/
function keyboardNavigation(e)
	{
		
		var evt = e || window.event;
		
		if(!ajax_optionDiv) return;
		if(ajax_optionDiv.style.display=='none') return;

		if(e.keyCode==38){	// Up arrow
			if(!activeDiv) return;
			if(activeDiv && !activeDiv.previousSibling)return;
			rollOverActiveDiv(activeDiv.previousSibling,true);
		}

		if(e.keyCode==40){	// Down arrow
			if(!activeDiv){
				rollOverActiveDiv(firstDiv,true);
			}else{
				if(!activeDiv.nextSibling)return;
				rollOverActiveDiv(activeDiv.nextSibling,true);
			}
		}

		if(e.keyCode==13 || e.keyCode==9){	// Enter key or tab key

			if(activeDiv && activeDiv.className=='optionDivSelected')selectDiv(activeDiv);
			if(e.keyCode==13)return false; else return true;
		}
		if(e.keyCode==27){	// Escape key
			ajax_options_hide();
		}
	}
	
	/*скрываем выбор*/
	function hideDiv(){
		if(ajax_optionDiv) ajax_optionDiv.style.display='none';
	}
	
	function prevent(event){
    event = event || window.event 

if(event.keyCode == 13 && activeDiv){
    if (event.preventDefault) {
        // Вариант стандарта W3C: 
        event.preventDefault()
    } else {
        // Вариант Internet Explorer:
        event.returnValue = false
    }}
	}