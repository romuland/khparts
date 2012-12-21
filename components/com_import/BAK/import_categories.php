<?php
/*требуется РЕФАКТОРИНГ*/
/*1. селекты разбить на методы или метод*/
/*2. добавление картинок сделать отдельной функцией*/
/*3. Человеческая разбивка на категории: категория верхнего уровня, например GETZ, далее подкатегории - название в таблице*/
	class Categories{
		private $category_id = 1;
		private $category_media_id = 1;
		private $category_parent_id = 1;
		private $table_prefix = "";
		private $link;
		private $parentCategoryId = 0;

		function init($l, $tp){
			$this->link = $l;
			$this->table_prefix = $tp;
			$table_name = $this->table_prefix."categories";
			$query_select = "SELECT MAX(virtuemart_category_id) FROM ".$table_name;
			$category_max_select = mysql_query($query_select, $this->link);
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
			}else{
				$category_max = mysql_fetch_array($category_max_select, MYSQL_NUM);
				$this->category_id = $category_max[0] + 1;
			}
			
			$table_name = $this->table_prefix."category_medias";
			$query_select = "SELECT MAX(id) FROM ".$table_name;
			$category_media_max_select = mysql_query($query_select, $this->link);
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
			}else{
				$category_media_max = mysql_fetch_array($category_media_max_select, MYSQL_NUM);
				$this->category_media_id = $category_media_max[0] + 1;
			}
			
			$table_name = $this->table_prefix."category_categories";			
			$query_select = "SELECT MAX(id) FROM ".$table_name;
			$category_parent_max_select = mysql_query($query_select, $this->link);
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
			}else{
				$category_parent_max = mysql_fetch_array($category_parent_max_select, MYSQL_NUM);
				$this->category_parent_id = $category_parent_max[0] + 1;
			}			
		}
		
		function process($data){
			
			$name = trim(preg_replace('/\s+/', ' ', trim($data[1])));
			$name = str_replace ('\\', '', $name);
			if ($name == "") {
				$desc = "Запчасти";
				$name = "Общая";
				}
			else{
				$desc = "Запчасти для ".$name;
				}			
			//$dir = $_SERVER['DOCUMENT_ROOT']."\\cars\\";
			$dir = $_SERVER['DOCUMENT_ROOT']."/images/stories/virtuemart/category/cars/";
			foreach (glob($dir."*.jpg") as $filename) {
				$file = substr($filename, strrpos($filename, "/") + 1);
				$temp = substr($file, 0, strpos($file, "."));
				$pos = strpos(strtolower($name),strtolower($temp));
    			if(!($pos === false)) {
					$category_image = $filename; 
					break;
				}
			}
			if ($category_image == ""){
				$category_image = $dir."hyn_kia.png";
			}

			$slug = makeAliasFromName($name);
			//check if record exist
			$table_name = $this->table_prefix."categories_ru_ru";
			$query_select = "select virtuemart_category_id from ".$table_name." where category_name='$name' or slug='$slug'";

			$result = mysql_query($query_select, $this->link);
			$rows_num = mysql_num_rows($result);

			//if record doesn't exist insert record in tables
			if ($rows_num == 0){
				$dateNow = date("Y-m-d H:i:s");
//================================================================================				
				// virtuemart_category_id category_name category_description metadesc metakey customtitle slug 
				$full_desc = sprintf("", "title-text");// 
				$query_insert = "insert into ".$table_name." values($this->category_id,'$name','<p id=\"title-text\">$desc</p>','$desc','$desc','','$slug')";
				mysql_query($query_insert, $this->link);
				if(mysql_error() != ""){
					echo "<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
				}
//================================================================================				
				$table_name = $this->table_prefix."categories";
				//virtuemart_category_id virtuemart_vendor_id category_template category_layout category_product_layout
				//products_per_row limit_list_start limit_list_step limit_list_max limit_list_initial hits metarobot 
				//metaauthor ordering shared published created_on created_by modified_on modified_by locked_by	locked_on 
				
				$query_insert = "insert into ".$table_name." values($this->category_id,1,0,0,0,0,0,10,0,10,0,'index,follow','',0,0,1,'$dateNow',42,'$dateNow',42,'0000-00-00 00:00:00',0)";
				mysql_query($query_insert, $this->link);		
				if(mysql_error() != ""){
					echo "<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
				}
//================================================================================
				$table_name = $this->table_prefix."category_categories";
				//id category_parent_id category_child_id ordering
				
				/*Определение родительской категории:*/
				/*должен быть массив соответсвия между названиями марок, и обозначениями в таблице*/
				/*сначала заливаем массив марок, затем с помощью массива привязываемся к нему по табличным значениям*/
				
				$query_insert = "insert into ".$table_name." values($this->category_parent_id,$this->parentCategoryId,$this->category_id,0)";
				mysql_query($query_insert, $this->link);	
				if(mysql_error() != ""){
					echo "<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
				}else $this->category_parent_id++;
//================================================================================
				//check if media exist
				$image_file_name = substr($category_image,strrpos($category_image,"/") + 1);
				$table_name = $this->table_prefix."medias";
				//virtuemart_media_id 	virtuemart_vendor_id 	file_title 	file_description 	file_meta 	file_mimetype 	file_type
				//file_url 	file_url_thumb 	file_is_product_image 	file_is_downloadable 	file_is_forSale 	file_params 	shared
				//published 	created_on 	created_by 	modified_on 	modified_by 	locked_on 	locked_by 	
				$query_select = "select virtuemart_media_id from ".$table_name." where file_title='$image_file_name'";
				$result_image = mysql_query($query_select, $this->link);
				$image_num = mysql_num_rows($result_image);
				if ($image_num == 0){
					//insert new image
					$query_select = "SELECT MAX(virtuemart_media_id) FROM ".$table_prefix.$table_name;
					$result_image = mysql_query($query_select, $this->link);
					$max_id_num = mysql_num_rows($result_image);
					if ($max_id_num == 0){
						$image_id = 1;
					}
					else{
						$image_id_arr = mysql_fetch_array($result_image, MYSQL_NUM);
						$image_id = $image_id_arr[0] + 1;
					}
					
					if(strpos($image_file_name, "jpg") > 0 || strpos($image_file_name, "jpeg") > 0)	$file_type = "image/jpeg";
					elseif(strpos($image_file_name, "png") > 0) $file_type = "image/png";
					elseif(strpos($image_file_name, "gif") > 0) $file_type = "image/gif";
		
					$query_insert = "insert into ".$table_name." values($image_id,1,'$image_file_name','','$image_file_name','$file_type','category','cars/$image_file_name','',0,0,0,'',0,1,'$dateNow',42,'$dateNow',42,'0000-00-00 00:00:00',0)";
					mysql_query($query_insert, $this->link);		
					if(mysql_error() != ""){
						echo "<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
					}
				}
				elseif ($image_num > 1) {
					echo "<p style='color:red;'> Several same medias $image_file_name<br/></p>\n";
				}
				else{
					$image_id_arr = mysql_fetch_array($result_image, MYSQL_NUM);
					$image_id = $image_id_arr[0];

				}
				$table_name = $this->table_prefix."category_medias";
				// id virtuemart_category_id 	virtuemart_media_id 	ordering 
				$query_insert = "insert into ".$table_name." values($this->category_media_id,$this->category_id,$image_id,1)";
				mysql_query($query_insert, $this->link);	
				if(mysql_error() != ""){
					echo "<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
				}else $this->category_media_id++;
				$this->currentCategoryId = $this->category_id;
			}else{
				$category_cur_id = mysql_fetch_array($result, MYSQL_NUM);
				$this->currentCategoryId = $category_cur_id[0];
			}
			$this->category_id = $this->category_id + 1;
    }
	function getCategoryId(){
		return $this->currentCategoryId;
	}
	function getParentCategoryId(){
		return $this->parentCategoryId;
	}	
}
?>

