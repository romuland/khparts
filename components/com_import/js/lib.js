<<<<<<< HEAD

jQuery(document).ready(function(){
		if($('dnd')) {
		var template = 	'<div class="preview">'	+
							'<span class="image-container">'+
								'<img id="img-id"/>'+
								'<span class="dnd-info" id="upload-id"></span>'+
								'<span class="dnd-progress" id="progress-id"></span>'+
							'</span>'+
						'</div>'; 
		dnd = document.getElementById("dnd");

		if (typeof(window.FileReader) == 'undefined') {
		    dnd.text('Не поддерживается браузером!');
		    dnd.addClass('error');
		}else{
			dnd.ondragover = function() {
			    dnd.addClass('hover');
			    return false;
			};
    
			dnd.ondragleave = function() {
			    dnd.removeClass('hover');
			    return false;
			};
			dnd.ondrop = function(event) {
    			event.preventDefault();
				//progressBar.style.display = 'block';
    			dnd.removeClass('hover');
				//dnd.innerHTML = '';
   				dnd.addClass('drop');
				var fileList = event.dataTransfer.files;
			
				for(var i = 0; i < fileList.length; i++){
					var curFile = fileList[i];
					//progressBar.style.width = "0px";
					//progressBar.innerHTML = "0%";
					if (curFile.type.match(/image.*/)) {
						var preview = document.createElement('div');
						//preview.setAttribute('class', 'preview');
						preview.innerHTML = template;
						dnd.appendChild(preview);
						var info = document.getElementById('upload-id');
						info.innerHTML = "Имя: " + curFile.name + "<br />Размер: " + curFile.size + " байт"
						info.setAttribute('id','');
						var reader = new FileReader();
						
						reader.onload = function (event, curFile) {
							var image = document.getElementById('img-id');
							image.setAttribute('width',100);
							image.setAttribute('height',80);
							image.setAttribute('src',event.target.result);
							image.setAttribute('id','');
						};
						reader.readAsDataURL(curFile);
									
						var formData = new FormData();
						formData.append('file', curFile);
					
						var xhr = new XMLHttpRequest();
						xhr.upload.onprogress = function(event) {
						    var percent = parseInt(event.loaded / event.total * 100);
							var progressBar = document.getElementById("progress-id");
							progressBar.style.width = percent + "px";  
							progressBar.innerHTML = percent + "%";
						};
						xhr.onload = function () {
							var progressBar = document.getElementById("progress-id");
							progressBar.style.width = "100px";
							progressBar.innerHTML = "100%";
							progressBar.setAttribute('id','');
						};
						xhr.onreadystatechange = function(){
							document.getElementById("imagesresult").innerHTML = xhr.responseText;
							if (xhr.readyState == 4) {
					        	if (xhr.status == 200) {
        	    					//li.innerHTML = li.innerHTML + 'Загрузка успешно завершена!';
					        	} else {
    	        					//li.innerHTML = li.innerHTML + 'Произошла ошибка!';
						            dnd.addClass('error');
					    	    }
						    }
						};	
						xhr.open('POST', '/components/com_import/import_images.php');
						xhr.send(formData);
					}
				}
			};
		}
	}
});
	function redirectPage() {
		window.location = linkLocation;
	}
function doUpload(form, resultField){
        // проверяем поддерживает ли браузер FormData
        if(!window.FormData) {document.getElementById(resultField).innerHTML = "Браузер не поддерживает необходимый класс FormData. Воспользуйтесь старой версией загрузчика"; return true;}
        else {
                var data = new FormData(form);
                xhr = new XMLHttpRequest();
                document.getElementById(resultField).innerHTML = '';
                xhr.open('POST', form.action);
                
                xhr.onreadystatechange = function(){
                    if (xhr.readyState == 4) {
                        if (xhr.status == 200) {
                            document.getElementById(resultField).innerHTML = xhr.responseText;
                        } else {
                            document.getElementById(resultField).innerHTML = "Ошибка";
                        }
                    }
                };
                xhr.send(data);
                return false;
            }
        }
        
        
function doOperation(obj, operationName, resultField){
    jQuery("#" + resultField).empty();
    jQuery.ajax({  
         url: obj.action,  
         type: "GET",
         data: {operation: obj.name, result: resultField},
         dataType: "html",
         cache: false,  
         beforeSend: function(){
             jQuery("#" + resultField).html(operationName + ' начинается, пожалуйста подождите...<br />');  
         },
         success: function(html){  
              jQuery("#" + resultField).html(jQuery("#" + resultField).html() + html);  
         }  
    });
	return false;    
}

function onClickAjax(ajaxdata, action, resultField){
    jQuery("#" + resultField).empty();
    jQuery.ajax({  
         url: action,  
         type: "GET",
         data: {data: ajaxdata},
         dataType: "html",
         cache: false,  
         success: function(html){  
              jQuery("#" + resultField).html(html);  
         }  
    });
	
	return false;    
}

function onClickAjax_input(formName, tableName, action, resultField){
    
	var form = jQuery("#"+ formName);
	var form_values = form.serializeArray();
	var vals = [];
	var isValid = true;
	jQuery.each(form_values, function(index, JSONElement){
		if((JSONElement.name=="category_links_ID" || JSONElement.name=="category_name") && JSONElement.value == ''){
			alert("Не заполнено обязательное поле ID или Наименование для новой категории");
			isValid = false;
		}
		if(JSONElement.value =='') vals[index] = 'null';
		else vals[index] = JSONElement.value;
	});
	if(isValid){
		jQuery("#" + resultField).empty();
    	jQuery.ajax({  
      		url: action,  
        	type: "GET",
        	data: {tableName: tableName, valuesSet: vals.join(',')},
       	 	dataType: "html",
	        cache: false,  
    	    success: function(html){  
            	jQuery("#" + resultField).html(html);  
         }  
    });
	}
	return false; 
	
}

function setDefault(){
            document.getElementById('currow').value = 1;
            document.getElementById('rowcount').value = 1;
            document.getElementById('filename').value = '';
}

function doImport(form){
            // проверяем поддерживает ли браузер FormData
            
            if(!window.FormData) {document.getElementById('impresult').innerHTML = "Браузер не поддерживает необходимый класс FormData."; return true;}
            else {
                var data = new FormData(form),
                xhr = new XMLHttpRequest(),
                    
                progressBar = document.querySelector('progress');
				
                file = document.getElementById('uploadedfile');  
                if (document.getElementById('currow').value == "1")
                {
                    document.getElementById('impresult').innerHTML = 'Импорт начинается, пожалуйста подождите...';
                    
                }
                document.getElementById('uploadsection').style.display = 'block';
                xhr.open('POST', "/components/com_import/import.php");
                
                xhr.onload = function (e) {
                    //  document.getElementById('impresult').innerHTML = e.currentTarget.responseText;
                }
                    
                    xhr.onreadystatechange = function(){
                        if (xhr.readyState == 4) {
                            if (xhr.status == 200) {

								try{
                                	var response = JSON.parse(xhr.responseText);
								}catch(e){
									document.getElementById('impresult').innerHTML = xhr.responseText + "<br />" + document.getElementById('impresult').innerHTML;
								}
                                if (response.log != "")
                                    document.getElementById('impresult').innerHTML = response.log + "<br />" + document.getElementById('impresult').innerHTML;
								
                                document.getElementById('currow').value = response.currow;
                                document.getElementById('rowcount').value = response.rowcount;
                                document.getElementById('filename').value = response.filename;
                                var temp = response.currow /response.rowcount;
                                progressBar.value = temp * 100;                                
                                if(response.state=="process"){
                                    doImport(form);
                                }
                            } else {
                                document.getElementById('impresult').innerHTML = "Ошибка";
                            }
                        }
                    };
                
                xhr.send(data);
                return false;
            }
}

=======

jQuery(document).ready(function(){
		if($('dnd')) {
		var template = 	'<div class="preview">'	+
							'<span class="image-container">'+
								'<img id="img-id"/>'+
								'<span class="dnd-info" id="upload-id"></span>'+
								'<span class="dnd-progress" id="progress-id"></span>'+
							'</span>'+
						'</div>'; 
		dnd = document.getElementById("dnd");

		if (typeof(window.FileReader) == 'undefined') {
		    dnd.text('Не поддерживается браузером!');
		    dnd.addClass('error');
		}else{
			dnd.ondragover = function() {
			    dnd.addClass('hover');
			    return false;
			};
    
			dnd.ondragleave = function() {
			    dnd.removeClass('hover');
			    return false;
			};
			dnd.ondrop = function(event) {
    			event.preventDefault();
				//progressBar.style.display = 'block';
    			dnd.removeClass('hover');
				//dnd.innerHTML = '';
   				dnd.addClass('drop');
				var fileList = event.dataTransfer.files;
			
				for(var i = 0; i < fileList.length; i++){
					var curFile = fileList[i];
					//progressBar.style.width = "0px";
					//progressBar.innerHTML = "0%";
					if (curFile.type.match(/image.*/)) {
						var preview = document.createElement('div');
						//preview.setAttribute('class', 'preview');
						preview.innerHTML = template;
						dnd.appendChild(preview);
						var info = document.getElementById('upload-id');
						info.innerHTML = "Имя: " + curFile.name + "<br />Размер: " + curFile.size + " байт"
						info.setAttribute('id','');
						var reader = new FileReader();
						
						reader.onload = function (event, curFile) {
							var image = document.getElementById('img-id');
							image.setAttribute('width',100);
							image.setAttribute('height',80);
							image.setAttribute('src',event.target.result);
							image.setAttribute('id','');
						};
						reader.readAsDataURL(curFile);
									
						var formData = new FormData();
						formData.append('file', curFile);
					
						var xhr = new XMLHttpRequest();
						xhr.upload.onprogress = function(event) {
						    var percent = parseInt(event.loaded / event.total * 100);
							var progressBar = document.getElementById("progress-id");
							progressBar.style.width = percent + "px";  
							progressBar.innerHTML = percent + "%";
						};
						xhr.onload = function () {
							var progressBar = document.getElementById("progress-id");
							progressBar.style.width = "100px";
							progressBar.innerHTML = "100%";
							progressBar.setAttribute('id','');
						};
						xhr.onreadystatechange = function(){
							document.getElementById("imagesresult").innerHTML = xhr.responseText;
							if (xhr.readyState == 4) {
					        	if (xhr.status == 200) {
        	    					//li.innerHTML = li.innerHTML + 'Загрузка успешно завершена!';
					        	} else {
    	        					//li.innerHTML = li.innerHTML + 'Произошла ошибка!';
						            dnd.addClass('error');
					    	    }
						    }
						};	
						xhr.open('POST', '/components/com_import/import_images.php');
						xhr.send(formData);
					}
				}
			};
		}
	}
});
	function redirectPage() {
		window.location = linkLocation;
	}
function doUpload(form, resultField){
        // проверяем поддерживает ли браузер FormData
        if(!window.FormData) {document.getElementById(resultField).innerHTML = "Браузер не поддерживает необходимый класс FormData. Воспользуйтесь старой версией загрузчика"; return true;}
        else {
                var data = new FormData(form);
                xhr = new XMLHttpRequest();
                document.getElementById(resultField).innerHTML = '';
                xhr.open('POST', form.action);
                
                xhr.onreadystatechange = function(){
                    if (xhr.readyState == 4) {
                        if (xhr.status == 200) {
                            document.getElementById(resultField).innerHTML = xhr.responseText;
                        } else {
                            document.getElementById(resultField).innerHTML = "Ошибка";
                        }
                    }
                };
                xhr.send(data);
                return false;
            }
        }
        
        
function doOperation(obj, operationName, resultField){
    jQuery("#" + resultField).empty();
    jQuery.ajax({  
         url: obj.action,  
         type: "GET",
         data: {operation: obj.name, result: resultField},
         dataType: "html",
         cache: false,  
         beforeSend: function(){
             jQuery("#" + resultField).html(operationName + ' начинается, пожалуйста подождите...<br />');  
         },
         success: function(html){  
              jQuery("#" + resultField).html(jQuery("#" + resultField).html() + html);  
         }  
    });
	return false;    
}

function onClickAjax(ajaxdata, action, resultField){
    jQuery("#" + resultField).empty();
    jQuery.ajax({  
         url: action,  
         type: "GET",
         data: {data: ajaxdata},
         dataType: "html",
         cache: false,  
         success: function(html){  
              jQuery("#" + resultField).html(html);  
         }  
    });
	
	return false;    
}

function onClickAjax_input(formName, tableName, action, resultField){
    
	var form = jQuery("#"+ formName);
	var form_values = form.serializeArray();
	var vals = [];
	var isValid = true;
	jQuery.each(form_values, function(index, JSONElement){
		if((JSONElement.name=="category_links_ID" || JSONElement.name=="category_name") && JSONElement.value == ''){
			alert("Не заполнено обязательное поле ID или Наименование для новой категории");
			isValid = false;
		}
		if(JSONElement.value =='') vals[index] = 'null';
		else vals[index] = JSONElement.value;
	});
	if(isValid){
		jQuery("#" + resultField).empty();
    	jQuery.ajax({  
      		url: action,  
        	type: "GET",
        	data: {tableName: tableName, valuesSet: vals.join(',')},
       	 	dataType: "html",
	        cache: false,  
    	    success: function(html){  
            	jQuery("#" + resultField).html(html);  
         }  
    });
	}
	return false; 
	
}

function setDefault(){
            document.getElementById('currow').value = 1;
            document.getElementById('rowcount').value = 1;
            document.getElementById('filename').value = '';
}

function doImport(form){
            // проверяем поддерживает ли браузер FormData
            
            if(!window.FormData) {document.getElementById('impresult').innerHTML = "Браузер не поддерживает необходимый класс FormData."; return true;}
            else {
                var data = new FormData(form),
                xhr = new XMLHttpRequest(),
                    
                progressBar = document.querySelector('progress');
				
                file = document.getElementById('uploadedfile');  
                if (document.getElementById('currow').value == "1")
                {
                    document.getElementById('impresult').innerHTML = 'Импорт начинается, пожалуйста подождите...';
                    
                }
                document.getElementById('uploadsection').style.display = 'block';
                xhr.open('POST', "/components/com_import/import.php");
                
                xhr.onload = function (e) {
                    //  document.getElementById('impresult').innerHTML = e.currentTarget.responseText;
                }
                    
                    xhr.onreadystatechange = function(){
                        if (xhr.readyState == 4) {
                            if (xhr.status == 200) {

								try{
                                	var response = JSON.parse(xhr.responseText);
								}catch(e){
									document.getElementById('impresult').innerHTML = xhr.responseText + "<br />" + document.getElementById('impresult').innerHTML;
								}
                                if (response.log != "")
                                    document.getElementById('impresult').innerHTML = response.log + "<br />" + document.getElementById('impresult').innerHTML;
								
                                document.getElementById('currow').value = response.currow;
                                document.getElementById('rowcount').value = response.rowcount;
                                document.getElementById('filename').value = response.filename;
                                var temp = response.currow /response.rowcount;
                                progressBar.value = temp * 100;                                
                                if(response.state=="process"){
                                    doImport(form);
                                }
                            } else {
                                document.getElementById('impresult').innerHTML = "Ошибка";
                            }
                        }
                    };
                
                xhr.send(data);
                return false;
            }
}

>>>>>>> 1e1b309bbf9e27aa09f1eef63d7bfeed85150451
