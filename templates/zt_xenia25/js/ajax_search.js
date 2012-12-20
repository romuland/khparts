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