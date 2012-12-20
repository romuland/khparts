	var intervalID;
	var TimeOutID;

	var menyFlag=false;
	function oncontext_transparent(txs){
		menyFlag=true;
		txs.style.zIndex=1;
	}
	clickCount = 1;
	function Mousedown_transparent(txs){
		clickCount++;
		clearTimeout(TimeOutID);
		TimeOutID=setTimeout(function() {if(!menyFlag){txs.style.zIndex=-1}menyFlag=false;},300); 
		setTimeout(function() {txs.style.zIndex=1},1300); 
	}
	
	$(document).ready(function(){
	$('#ajax-content').css('display', 'none').fadeIn(100);
	$('a').click(function(event){
		event.preventDefault();
		linkLocation = this.href;
		$('#ajax-content').load(this.href+' #ajax-content', function(){
			window.history.pushState(null, null, linkLocation);//отслеживает и вставляет в адресную строку новую ссылку
			});
		
//		jQuery('#ajax-content').fadeOut(1000, redirectPage);
	});
	});
/*
var content_td=jQuery('.table_load').html();

jQuery(".uMenuRoot a, .uMenuV a").click(function(){

                jQuery('.befor_td').remove();//удаляем блок с картинкой "Загрузка", нужен если бычтро нажимать на разные ссылки
                jQuery(".uMenuRoot a").removeClass("uMenuItemA");//меняем стиль отображения активной ссылки
                jQuery(this).addClass("uMenuItemA");//меняем стиль отображения активной ссылки

                var href_a=jQuery(this).attr("href");//заносим в переменную адрес нажимаемой ссылки
                window.history.pushState(null, null, href_a);//отслеживает и вставляет в адресную строку новую ссылку
                
                jQuery('.content_table').before("<td class='befor_td'></td>").fadeOut(400, function(){//добавляем блок с картинкой "Загрузка" и скрываем блок с контентом
                jQuery('.befor_td').html('<p><p style="margin-top:100px;text-align:center;font-size:24px;">Идет ЗАГРУЗКА</p><p style="text-align:center;"><img src="/jquery/images/loader.gif"></p></p>').css('width','800px');
                jQuery('.content_table').load(href_a+" .table_load", function(){//загружаем с сервера новый контент
                                 jQuery('.table_load').css('width','800px');
                                 jQuery('.befor_td').remove();//удаляем блок с картинкой "Загрузка"
                                 jQuery('.content_table:hidden').slideDown(300); //открываем блок с новым загруженным контентом
                                                                        });
                                                                                                                    });
            returnfalse;// не даем браузеру перейти по ссылке при клике


                                    });
window.addEventListener("popstate", function(e){//берет ссылку из строки браузера и по ней загружает контент, как бы переход при клике по верхним стрелкам назад вперед
            var href_next_back=window.location.pathname;
             jQuery('.table_load').html('<p style="margin-top:300px;text-align:center;font-size:24px;">Идет ЗАГРУЗКА</p><p style="text-align:center;"><img src="/jquery/images/loader.gif"></p>');
             jQuery('.table_load').load(href_next_back+" .table_load", function(){
                                         jQuery('.table_load').replaceWith(jQuery('.table_load').html());
                                        window.history.pushState(null, null, href_a);
                                                                            });
                                        });*/