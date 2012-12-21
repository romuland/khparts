<?php
/*требуется РЕФАКТОРИНГ*/
/*1. селекты разбить на методы или метод*/
/*2. добавление картинок сделать отдельной функцией*/
	class Manufacturers{
		private $manufacturer_id = 1;
		private $currentManufacturerId = 1;
		private $currentManufacturerName = '';
		private $table_prefix = "";
		private $link;
		private $manufacturer_media_id = 1;
		
		function init($l, $tp){
			$this->link = $l;
			$this->table_prefix = $tp;
			$html = "";
			$table_name = $this->table_prefix."manufacturers";
			$query_select = "SELECT MAX(virtuemart_manufacturer_id) FROM ".$table_name;
			$manufacturer_max_select = mysql_query($query_select, $this->link);
			if(mysql_error() != ""){
				$html.="<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
			}else{
				$manufacturer_max = mysql_fetch_array($manufacturer_max_select, MYSQL_NUM);
				$this->manufacturer_id = $manufacturer_max[0] + 1;
			}
		
			$table_name = $this->table_prefix."manufacturer_medias";
			$query_select = "SELECT MAX(id) FROM ".$table_name;
			$manufacturer_media_max_select = mysql_query($query_select, $this->link);
			if(mysql_error() != ""){
				$html.="<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
			}else{
				$manufacturer_media_max = mysql_fetch_array($manufacturer_media_max_select, MYSQL_NUM);
				$this->manufacturer_media_id = $manufacturer_media_max[0] + 1;
			}
			return $html;
		}
		
		function process($data){
			$html = "";
			
			$name = trim($data[3]);
			$desc = "Производитель автозапчастей";
			if ($name != "") {
				$desc = $desc." ".$name;
				}			
			$dir = $_SERVER['DOCUMENT_ROOT']."/images/";
			foreach (glob($dir."*.jpg") as $filename) {
				$file = substr($filename, strrpos($filename, "/") + 1);
				$temp = substr($temp, 0, strpos($temp, "."));
    			if(strpos($name,$temp) > 0) {
					$manufacturer_image = $filename; 
					break;
				}
			}
			if ($manufacturer_image == ""){
				$manufacturer_image = $dir."hyn_kia.png";
			}

			$slug = makeAliasFromName($name);
			//check if record exist
			$table_name = $this->table_prefix."manufacturers_ru_ru";
	
			$query_select = "select * from ".$table_name." where mf_name='$name' or slug='$slug'";
			$result = mysql_query($query_select, $this->link);
			$rows_num = mysql_num_rows($result);
			
			//if record doesn't exist insert record in tables
			if ($rows_num == 0){
				$dateNow = date("Y-m-d H:i:s");
//================================================================================				
				//virtuemart_manufacturer_id 	mf_name 	mf_email 	mf_desc 	mf_url 	slug
				//4 	ORG 		<p>Производитель автозапчастей ORG</p> 		org		
				$full_desc = sprintf("", "title-text");// 
				$query_insert = "insert into ".$table_name." values($this->manufacturer_id,'$name','','<p id=\"title-text\">$desc</p>','','$slug')";
				mysql_query($query_insert, $this->link);
				if(mysql_error() != ""){
					$html.="<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
				}
//================================================================================				
				$table_name = $this->table_prefix."manufacturers";
				//virtuemart_manufacturer_id 	virtuemart_manufacturercategories_id 	hits 	published 	created_on 	created_by
				//modified_on 	modified_by 	locked_on 	locked_by
				$manufacturercategories_id = 1; //manufacturer category - default
				$query_insert = "insert into ".$table_name." values($this->manufacturer_id,$manufacturercategories_id ,0,1,'$dateNow',42,'$dateNow',42,'0000-00-00 00:00:00',0)";
				mysql_query($query_insert, $this->link);		
				if(mysql_error() != ""){
					$html.="<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
				}
//================================================================================
				//check if media exist
				$image_file_name = substr($manufacturer_image,strrpos($manufacturer_image,"/") + 1);
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
		
					$query_insert = "insert into ".$table_name." values($image_id,1,'','','$image_file_name','$file_type','manufacturer','$manufacturer_image','',0,0,0,'',0,1,'$dateNow',42,'$dateNow',42,'0000-00-00 00:00:00',0)";
					mysql_query($query_insert, $this->link);		
					if(mysql_error() != ""){
						$html.="<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
					}
				}
				elseif ($image_num > 1) {
					$html.="<p style='color:red;'> Найдено несколько категорий $image_file_name<br/></p>\n";
				}
				else{
					$image_id_arr = mysql_fetch_array($result_image, MYSQL_NUM);
					$image_id = $image_id_arr[0];
				}
				
				$table_name = $this->table_prefix."manufacturer_medias";
				// id virtuemart_manufacturer_id 	virtuemart_media_id 	ordering 
				$query_insert = "insert into ".$table_name." values($this->manufacturer_media_id,$this->manufacturer_id,$image_id,1)";
				mysql_query($query_insert, $this->link);	
				if(mysql_error() != ""){
					$html.="<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
				}else $this->manufacturer_media_id++;
				
				$this->currentManufacturer = '{"id":"'.$this->manufacturer_id.'", "name":"'.$name.'"}';
				
				$this->manufacturer_id = $this->manufacturer_id + 1;
			}else{
				$manufacturer_cur_id = mysql_fetch_array($result, MYSQL_NUM);
				$this->currentManufacturer = '{"id":"'.$manufacturer_cur_id[0].'", "name":"'.$name.'"}';
				
			}

			
			
			return $html;
    }
	function getManufacturer(){
		return $this->currentManufacturer;
	}
}
?>

