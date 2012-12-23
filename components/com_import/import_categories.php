<?php
	class Categories{
		private $category_id = 1;		//столбец category_id в БД
		private $category_media_id = 1;	//столбец category_media_id в БД
		private $category_parent_id = 1;//столбец category_parent_id в БД(не путать с parentCategoryId!!!)
		private $table_prefix = "";		//префикс таблицы БД
		private $db;					//ссылка на БД
		private $parentCategoryId = 0;	//ID родительской категории (не путать с category_parent_id!!!)
		private $parentCategoryName = '';	//Имя родительской категории
		private $currentCategory; 	//массив текущих категорий
		private $description = ''; 	//описание, если есть (для сопутствющих товаров)
		private $source_name =''; 	//исходное название
		private $html = '';
		
		private $DEFAULT_IMAGE_PATH = "/images/stories/virtuemart/category/cars/";
		private $DEFAULT_IMAGE_NAME = "hyn_kia.png";
		private $DEFAULT_CATEGORY = "Другое";
		private $DEFAULT_CATEGORY_SYNONYM = "Other";
		private $DEFAULT_CATEGORY_IMAGE_NAME = "other.jpg";	
		
		private $ACC_CATEGORY = "Сопутствующие товары";
		private $ACC_CATEGORY_SYNONYM = "accompanying";
		private $ACC_CATEGORY_IMAGE_NAME = "accompanying.jpg";	
		
		private $EMPTY_CATEGORY = "";
		private $ALL_CATEGORY = "Все модели";
		private $ALL_CATEGORY_SYNONYM = "All";
		
		function init($d, $tp){
			
			$html = "";
			
			$this->db = $d;
			$this->table_prefix = $tp;
			
			$result_select = $this->db->select($this->table_prefix."categories", "virtuemart_category_id", "", 'max');
			$html.=$result_select['html'];
			$this->category_id = $result_select['value'] + 1;
			
			$result_select = $this->db->select($this->table_prefix."category_medias", "id", "", 'max');
			$html.=$result_select['html'];
			$this->category_media_id = $result_select['value'] + 1;
			
			$result_select = $this->db->select($this->table_prefix."category_categories", "id", "", 'max');
			$html.=$result_select['html'];
			$this->category_parent_id = $result_select['value'] + 1;
						
			$html.=$this->createTopCategories();
			
			return $html;
		}
		
		function process($data){
			
			$this->html = "";
			
			$this->currentCategory = array();

			$name = trim($data[1]);
			
			//preg_replace возвращает пустую строку с русскими буквами 
			if (preg_match("/[а-яА-я]/", $name) == 0)
				$name = trim(preg_replace('/\s+/', ' ', $name));
				
			$name = str_replace ('\\', '', $name);
			$name = str_replace ('~', '', $name);
			$name = str_replace ('"', '', $name);
			$name = ucfirst ($name);
			if(strpos($name, ";") === false){
				$this->html .= $this->processCategory($name, false);
			}
			else{
				$temp = $name;
				if(strpos($temp, ";") > 0) {
					while(strpos($temp, ";") > 0) {
						$name = substr($temp, 0, strpos($temp, ";"));
						$temp = substr($temp, strpos($temp, ";") + 1);
						$this->html.= $this->processCategory($name, false);
					}
				}
		
				$this->html.= $this->processCategory($temp, false);
			}
			
			return $this->html;
	    }
	
		function createTopCategories(){
			$html = '';
			$result_select = $this->db->select($this->table_prefix."category_links", "category_name", "parent_name IS NULL and synonym_name IS NULL", 'std');
			$html.=$result_select['html'];
			$rows_num = $result_select['rows'];
			
			if ($rows_num > 0){
				for($i = 0; $i < count($result_select['value']); $i++)
					$html .= $this->processCategory($result_select['value'][$i], true);
			}
			return $html;		
		}
	
		function processCategory($name_param, $is_top){
			$html = '';
			$json_result = "";
			$this->source_name = '';
			$name_param = trim($name_param);
			if($name_param != ""){
				$name = ucfirst($name_param);
				if(strpos($name, "(") > 0)
					$name = trim(substr($name, 0, strpos($name, "(")));
			}
			else
				$name = $this->EMPTY_CATEGORY;

		$name = $this->getCategoryLink($name, $is_top);	
		$slug = makeAliasFromName($name);

		$is_default = false;
		$is_all = false;
		$is_acc = false;
		if($name==$this->DEFAULT_CATEGORY_SYNONYM) {$name=$this->DEFAULT_CATEGORY; $is_default = true;}
		elseif($name==$this->ACC_CATEGORY_SYNONYM) {$name=$this->ACC_CATEGORY; $is_acc = true;}
		elseif ($name==$this->ALL_CATEGORY_SYNONYM) {$name=$this->ALL_CATEGORY." ".$this->parentCategoryName; $slug = makeAliasFromName($name);$is_all = true;}

		//check if record exist
		$table_name = $this->table_prefix."categories_ru_ru";
		$result_select = $this->db->select($table_name, "", "category_name = '$name' or slug = '$slug'", 'std');
		$html.=$result_select['html'];
		$rows_num = $result_select['rows'];
					
		if ($rows_num == 0){
			
			if ($name == ""){
				$category_image = $_SERVER['DOCUMENT_ROOT'].$this->DEFAULT_IMAGE_PATH.$this->ACC_CATEGORY_IMAGE_NAME;
				$desc = "";
			}
			else{
				if($is_all) $category_image = $this->uploadCategoryImage($this->parentCategoryName);
				elseif($is_acc) $category_image = $_SERVER['DOCUMENT_ROOT'].$this->DEFAULT_IMAGE_PATH.$this->ACC_CATEGORY_IMAGE_NAME;
				else $category_image = $this->uploadCategoryImage($name);
				
				if($this->parentCategoryName != '') 
					if($this->parentCategoryName == $this->DEFAULT_CATEGORY) $desc = "Запчасти для ".$name;
					elseif($this->parentCategoryName == $this->ACC_CATEGORY) $desc = "Запчасти для ".$name;
					else if($is_all) $desc = "Запчасти для всех моделей ".$this->parentCategoryName;
						 else $desc = "Запчасти для ".$this->parentCategoryName." ".$name;
				else
					if($is_default) $desc = "Другие запчасти";	
					elseif($is_all) $desc = "Запчасти для всех моделей";	
					elseif($is_acc) $desc = $name;	
					else $desc = "Запчасти для ".$name;	
			}
			
			if($name==$this->ALL_CATEGORY_SYNONYM) $name=$this->ALL_CATEGORY." ".$this->parentCategoryName;;
			
			$NULL_DATE = '0000-00-00 00:00:00';	
			$dateNow = date("Y-m-d H:i:s");
//================================================================================				
			// virtuemart_category_id category_name category_description metadesc metakey customtitle slug 
			$html.=$this->db->insert($table_name, "$this->category_id,'$name','<h1>$desc</h1>','$desc','$desc','$desc','$slug'");

//================================================================================				
			$table_name = $this->table_prefix."categories";
			//virtuemart_category_id virtuemart_vendor_id category_template category_layout category_product_layout
			//products_per_row limit_list_start limit_list_step limit_list_max limit_list_initial hits metarobot 
			//metaauthor ordering shared published created_on created_by modified_on modified_by locked_by	locked_on 
			
			$html.=$this->db->insert($table_name, "$this->category_id,1,0,0,0,0,0,30,0,30,0,'index,follow','',0,0,1,'$dateNow',42,'$dateNow',42,'$NULL_DATE',0");	

//================================================================================
			$table_name = $this->table_prefix."category_categories";
			//id category_parent_id category_child_id ordering
				
			/*Определение родительской категории:*/
			/*должен быть массив соответсвия между названиями марок, и обозначениями в таблице*/
			/*сначала заливаем массив марок, затем с помощью массива привязываемся к нему по табличным значениям*/
			
			$temp=$this->db->insert($table_name, "$this->category_parent_id,$this->parentCategoryId,$this->category_id,0");					
			if($temp =='')	$this->category_parent_id++;
			else 			$html.= $temp;
				
//================================================================================
			//check if media exist
			$image_file_name = substr($category_image,strrpos($category_image,"/") + 1);
			$table_name = $this->table_prefix."medias";
			//virtuemart_media_id 	virtuemart_vendor_id 	file_title 	file_description 	file_meta 	file_mimetype 	file_type
			//file_url 	file_url_thumb 	file_is_product_image 	file_is_downloadable 	file_is_forSale 	file_params 	shared
			//published 	created_on 	created_by 	modified_on 	modified_by 	locked_on 	locked_by 	
			
			$result_select = $this->db->select($table_name, "virtuemart_media_id", "file_title='$image_file_name'", 'std');
			$html.=$result_select['html'];
			
			if ($result_select['rows'] == 0){
				//insert new image
				$result_select = $this->db->select($table_prefix.$table_name, "virtuemart_media_id", "", 'max');
				$html.=$result_select['html'];
				$image_id = $result_select['value'] + 1;	
								
				if(strpos($image_file_name, "jpg") > 0 || strpos($image_file_name, "jpeg") > 0)	$file_type = "image/jpeg";
				elseif(strpos($image_file_name, "png") > 0) $file_type = "image/png";
				elseif(strpos($image_file_name, "gif") > 0) $file_type = "image/gif";
		
				$html.=$this->db->insert($table_name, "$image_id,1,'$image_file_name','','$image_file_name','$file_type','category','cars/$image_file_name','',0,0,0,'',0,1,'$dateNow',42,'$dateNow',42,'$NULL_DATE',0");
					
				}
			elseif ($result_select['rows'] > 1) 		
				$html.="<p style='color:red;'> Several same medias $image_file_name<br/></p><br />";
			else
				$image_id = $result_select['value'][0];
			
			// id virtuemart_category_id 	virtuemart_media_id 	ordering 
			$temp = $this->db->insert($this->table_prefix."category_medias", "$this->category_media_id,$this->category_id,$image_id,1"); 
			if($temp =='')	$this->category_media_id++;
			else 			$html.= $temp;
						
			$json_result = '{"id":"'.$this->category_id.'", "name":"'.$name.'", "slug":"'.$slug.'", "parent":"'.$this->parentCategoryName.'", "description":"'.$this->description.'", "source_name":"'.$this->source_name.'"}';

			if(count($this->currentCategory) > 0)	array_push($this->currentCategory, $json_result);
			else 								$this->currentCategory = array($json_result);
			$this->category_id = $this->category_id + 1;
		}else{
			$json_result = '{"id":"'.$result_select['value'][0]['virtuemart_category_id'].'", "name":"'.$name.'", "slug":"'.$slug.'", "parent":"'.$this->parentCategoryName.'", "description":"'.$this->description.'", "source_name":"'.$this->source_name.'"}';
			if(count($this->currentCategory) > 0)	array_push($this->currentCategory, $json_result);
			else 								$this->currentCategory = array($json_result);
		}		
		return $html;
	}

	function getCategoryLink($init_key, $is_top){
		$category = '';
//категории все
		if (strpos(strtolower($init_key), strtolower($this->ALL_CATEGORY_SYNONYM)) == 0 && strpos(strtolower($init_key), strtolower($this->ALL_CATEGORY_SYNONYM)) !== FALSE) {
			if(strpos(trim($init_key), " ") > 0){
 				$parent = ucfirst(strtolower(trim(substr($init_key, strpos($init_key, " ")))));
				$category = $this->ALL_CATEGORY_SYNONYM;
			}
			else{
				$category = $this->ACC_CATEGORY;
				$this->source_name = $init_key; 
			}
		}elseif($init_key == "") {
			$this->source_name = ''; $category=$this->ACC_CATEGORY_SYNONYM;
		}else{
			$key[] = $init_key;
			if(strpos(trim($init_key), " ") > 0){
				$temp = $init_key;
				//запоминаем текущее и предыдущее значение, чтобы добавить в массив ключей пары значений (необходимо для Santa Fe 2.5 и т.п.)
				$prev_value = '';
				$cur_value = '';
				while(strpos($temp, " ") > 0) {
					$cur_value = substr($temp, 0, strpos($temp, " "));
					$key[] = $cur_value;
					if($prev_value != '')
						$key[] = $prev_value." ".$cur_value;
					$prev_value = $cur_value;
					$temp = trim(substr($temp, strpos($temp, " ")));
				}
			}
	
			$table_name = $this->table_prefix."category_links";
			$result_select = $this->db->select($table_prefix.$table_name, "", "category_name in('".implode("','", $key)."')", 'std');
			$this->html .= $result_select['html'];
			
			//if record doesn't exist insert record in tables
			if ($result_select['rows'] == 0){
				//категория без родителя помещаем в категорию по умолчанию
			//	$this->parentCategoryId = 0;
			//	$this->parentCategoryName = '';
				$parent = $this->DEFAULT_CATEGORY;
				$category = $init_key;
				$this->html .= "Не найдена подходящая категория по ключу: ".$init_key."<br />";
			}
			else {
				if(trim($result_select['value'][0]['synonym_name']) != "") $category = $result_select['value'][0]['synonym_name'];
				else								  $category = $result_select['value'][0]['category_name'];
			
				$parent = $result_select['value'][0]['parent_name'];
				
				if($parent==$this->ACC_CATEGORY_SYNONYM) {$this->source_name = $category; $category=$parent;}
				
				if($parent == '' && !$is_top) {
					$parent = $category;
					$category = $this->ALL_CATEGORY_SYNONYM;
				}
				elseif($parent == $this->DEFAULT_CATEGORY_SYNONYM) $parent = $this->DEFAULT_CATEGORY;
				
				if(trim($result_select['value'][0]['description']) != "") 
					if($this->description != '') $this->description .= " ".$result_select['value'][0]['description'];
					else $this->description = $result_select['value'][0]['description'];
			}	
		}
		$table_name = $this->table_prefix."categories_ru_ru";
		$result_select = $this->db->select($table_prefix.$table_name, "virtuemart_category_id", "category_name='$parent'", 'std');
		$this->html .= $result_select['html'];
			
		//if record doesn't exist insert record in tables
		if ($result_select['rows'] == 0){
			$this->parentCategoryId = 0;$this->parentCategoryName = '';
		}else{
			$this->parentCategoryId = $result_select['value'][0];
			$this->parentCategoryName = $parent;
		}
		
		return $category;
	}
	
	function uploadCategoryImage($name){
		$dir = $_SERVER['DOCUMENT_ROOT'].$this->DEFAULT_IMAGE_PATH;
		if ($this->ALL_CATEGORY == $name) $search_value = $this->parentCategoryName; 
		else 							  $search_value = $name;
		foreach (glob($dir."*.jpg") as $filename) {
			$file = substr($filename, strrpos($filename, "/") + 1);
			$temp = substr($file, 0, strpos($file, "."));
			$pos = strpos(strtolower($search_value),strtolower($temp));
   			if(!($pos === false)) {
				$category_image = $filename; 
				break;
			}
		}
		if ($category_image == ""){
			if ($this->DEFAULT_CATEGORY == $name) $category_image = $dir.$this->DEFAULT_CATEGORY_IMAGE_NAME;
			else 							   $category_image = $dir.$this->DEFAULT_IMAGE_NAME;
		}
		return $category_image;
	}
	
	function getCategory(){
		return $this->currentCategory;
	}
	function getParentCategoryId(){
		return $this->parentCategoryId;
	}		
}
?>