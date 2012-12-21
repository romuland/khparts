<?php
	class Manufacturers{
		private $manufacturer_id = 1;
		private $currentManufacturerId = 1;
		private $currentManufacturerName = '';
		private $table_prefix = "";
		private $db;
		private $manufacturer_media_id = 1;
		private $main_manufacturers;
		
		function init($d, $tp){
			$this->db = $d;
			$this->table_prefix = $tp;
			$html = "";
			
			$result_select = $this->db->select($this->table_prefix."manufacturers", "virtuemart_manufacturer_id", "", 'max');
			$html.=$result_select['html'];
			$this->manufacturer_id = $result_select['value'] + 1;
			
			$result_select = $this->db->select($this->table_prefix."manufacturer_medias", "id", "", 'max');
			$html.=$result_select['html'];
			$this->manufacturer_media_id = $result_select['value'] + 1;
			$result_select = $this->db->select($this->table_prefix."manufacturers", "virtuemart_manufacturer_id", "virtuemart_manufacturercategories_id=1", 'std');
			$html.=$result_select['html'];
			$this->main_manufacturers = $result_select['value'];
			return $html;
		}
		
		function process($data){
			$html = "";
			
			$name = trim($data[3]);
			$desc = "Производитель автозапчастей";
			$DEFAULT_MANUFACTURER_CATEGORY = 2;
			if ($name != "") {
				$desc = $desc." ".$name;
			}	
			$table_name = $this->table_prefix."manufacturers_ru_ru";
			$result_select = $this->db->select($table_name, "", "LOCATE( mf_name, '$name') > 0 AND mf_name != '' AND virtuemart_manufacturer_id in('".implode("','", $this->main_manufacturers)."')", 'std');
			$html.=$result_select['html'];
			$rows_num = $result_select['rows'];
			if ($result_select['rows'] > 0){
				if ($result_select['rows'] > 1)
					$html.="<p style='color:red;'> Найдено несколько категорий $name<br/>".$this->db->query_toString()."<br/></p>\n";
				else{
					$image_id = $result_select['value'][0]['mf_name'];
//************РЕЗУЛЬТАТ******************				
					$this->currentManufacturer = '{"id":"'.$result_select['value'][0]['virtuemart_manufacturer_id'].'", "name":"'.$result_select['value'][0]['mf_name'].'"}';
					$this->manufacturer_id = $this->manufacturer_id + 1;
//***************************************						
				}
			}else{
			
			$manufacturer_image = $this->db->get_image($name);

			$slug = makeAliasFromName($name);
			//check if record exist
			
			
			$result_select = $this->db->select($table_name, "virtuemart_manufacturer_id", "mf_name='$name' or slug='$slug'", 'std');
			$html.=$result_select['html'];
			$rows_num = $result_select['rows'];
			
			//if record doesn't exist insert record in tables
			if ($rows_num == 0){
				$dateNow = date("Y-m-d H:i:s");
				
//================================================================================				
				//virtuemart_manufacturer_id 	mf_name 	mf_email 	mf_desc 	mf_url 	slug
				//4 	ORG 		<p>Производитель автозапчастей ORG</p> 		org		
				$html.=$this->db->insert($table_name, "$this->manufacturer_id,'$name','','<p class=\"title-text\">$desc</p>','','$slug'");
				
//================================================================================				
				//virtuemart_manufacturer_id 	virtuemart_manufacturercategories_id 	hits 	published 	created_on 	created_by
				//modified_on 	modified_by 	locked_on 	locked_by
				$manufacturercategories_id = $DEFAULT_MANUFACTURER_CATEGORY; //manufacturer category - default
				$html.=$this->db->insert($this->table_prefix."manufacturers", "$this->manufacturer_id,$manufacturercategories_id ,0,1,'$dateNow',42,'$dateNow',42,'$NULL_DATE',0");
		
//================================================================================
				//check if media exist
				$image_file_name = substr($manufacturer_image,strrpos($manufacturer_image,DIRECTORY_SEPARATOR) + 1);
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
		
					$html.=$this->db->insert($table_name, "$image_id,1,'','','$image_file_name','$file_type','manufacturer','$manufacturer_image','',0,0,0,'',0,1,'$dateNow',42,'$dateNow',42,'$NULL_DATE',0");
					
				}
				elseif ($result_select['rows'] > 1) 
					$html.="<p style='color:red;'> Найдено несколько категорий $image_file_name<br/></p>\n";
				else
					$image_id = $result_select['value'][0];

				// id virtuemart_manufacturer_id 	virtuemart_media_id 	ordering 
				$temp = $this->db->insert($this->table_prefix."manufacturer_medias", "$this->manufacturer_media_id,$this->manufacturer_id,$image_id,1"); 
				if($temp =='')	$this->manufacturer_media_id++;
				else 			$html.= $temp;

//************РЕЗУЛЬТАТ******************				
				$this->currentManufacturer = '{"id":"'.$this->manufacturer_id.'", "name":"'.$name.'"}';
				$this->manufacturer_id = $this->manufacturer_id + 1;
//***************************************				
			}else{
//************РЕЗУЛЬТАТ******************	
				$this->currentManufacturer = '{"id":"'.$result_select['value'][0].'", "name":"'.$name.'"}';
//***************************************				
			}
			}
			return $html;
    	}
		

		function getManufacturer(){
			return $this->currentManufacturer;
		}
	}
?>

