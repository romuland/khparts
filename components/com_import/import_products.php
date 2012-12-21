<<<<<<< HEAD
<?php
	include "import_products_buffer.php";
	
	class Products{
		private $product_id = 1;		//ID в таблице продуктов
		private $buffer_id = 1;			//ID строки в таблице импорта (таблица полное соответствие csv файлу, но с айдишниками
		private $prod_cat_id = 1;		//ID строки в таблице соответствия продукта с категорией(-ями)
		private $prod_man_id = 1;		//ID строки в таблице соответствия продукта с производителем(-ями)
		private $prod_med_id = 1;		//ID строки в таблице соответствия продукта с картинкой(-ами)
		private $prod_price_id = 1;		//ID строки в таблице соответствия продукта с ценой(-ами)
		private $prod_cf_id = 1;		//ID строки в таблице соответствия продукта с дополнительными полями
		private $table_prefix = "";		//префикс по умолчанию для таблицы Virtuemart
		private $db;					//коннект с БД
		private $DEFAULT_CATEGORY_NAME = "Другое";
		private $EMPTY_CATEGORY_NAME = "";
		private $ALL_CATEGORY_NAME = "все модели";
		
		function init($d, $tp){
			$html = "";
			
			$this->db = $d;
			$this->table_prefix = $tp;
			
			$result_select = $this->db->select($this->table_prefix."products", "virtuemart_product_id", "", 'max');
			$html.=$result_select['html'];
			$this->product_id = $result_select['value'] + 1;
			
			$result_select = $this->db->select($this->table_prefix."import", "id", "", 'max');
			$html.=$result_select['html'];
			$this->buffer_id = $result_select['value'] + 1;
									
			$result_select = $this->db->select($this->table_prefix."product_categories", "id", "", 'max');
			$html.=$result_select['html'];
			$this->prod_cat_id = $result_select['value'] + 1;
						
			$result_select = $this->db->select($this->table_prefix."product_manufacturers", "id", "", 'max');
			$html.=$result_select['html'];
			$this->prod_man_id = $result_select['value'] + 1;
			
			$result_select = $this->db->select($this->table_prefix."product_medias", "id", "", 'max');
			$html.=$result_select['html'];
			$this->prod_med_id = $result_select['value'] + 1;
			
			$result_select = $this->db->select($this->table_prefix."product_prices", "virtuemart_product_price_id", "", 'max');
			$html.=$result_select['html'];
			$this->prod_price_id = $result_select['value'] + 1;
						
			$result_select = $this->db->select($this->table_prefix."product_customfields", "virtuemart_customfield_id", "", 'max');
			$html.=$result_select['html'];
			$this->prod_cf_id = $result_select['value'] + 1;
					
			return $html;
		}
		
		function process($data, $categories, $manufacturer, $warehouse){
			$html = "";
			$session_id = mt_rand();
			$buffer = new Products_Buffer();
			$buffer->init($this->db, $this->table_prefix, $this->buffer_id, $session_id);
			$buffer_result = 0;
		
			if (count($categories) > 0){
				foreach($categories as $category){
					$html.= $this->manageBuffer($buffer, $data, $categories, $category, $manufacturer, $warehouse, $session_id);
				}
			}
			else $html.= $this->manageBuffer($buffer, $data, '', '', $manufacturerId, $warehouse, $session_id);
			
			return $html;
		}
		
		private function manageBuffer($buffer, $data, $categories, $category, $manufacturer, $warehouse, $session_id){
			$category_d = json_decode($category);
			$manufacturer_d = json_decode($manufacturer);
			
			$buffer_result = $buffer->processRecord($this->buffer_id, $data, $this->product_id, $category_d->id, $manufacturer_d->id, $warehouse, $session_id);
			$html = $buffer->getResult();
			//Задача по управлению обновлением/добавлением запчастей лежит на классе буффер, здесь только принимаем результат, и выполняем его
	
			if($buffer_result > 0) {
				//обновление
				$html.= $this->updateExist($data, $buffer_result);
			}elseif($buffer_result == 0	){
				//добавление
				$html.= $this->newProduct($categories, $category_d, $manufacturer_d, $buffer->getJson(), $data);
				$this->product_id = $this->product_id + 1;
				$this->buffer_id = $this->buffer_id + 1;	
			}else{
				//обновление, но обновлять не требуется
			}
			return $html;
		}
		
		private function newProduct($categories, $category_d, $manufacturer_d, $data_json, $source_data){
			
			$html = "";
			
			$dateNow = date("Y-m-d H:i:s");
			$NULL_DATE = '0000-00-00 00:00:00';	
			$SITE_NAME = $_SERVER['SERVER_NAME'];
			$SHOP_LINK = '/catalog/';
			
			$data = json_decode($data_json);
			
			$name =  $data->{'name'};
			$name =  str_replace ('\\', '', $name);
//$html.=iconv('UTF-8','windows-1251',$data_json)."<br />";

			$code = $data->{'code'};
			$code =  str_replace ('\\', '', $code);
			$inStock = $data->{'inStock'};	
			$price = $data->{'price'};
			$manufacturer = $manufacturer_d->{'name'};

			$full_name ='';

			if ($category_d->{'source_name'} != '') {
				$category = $this->getRightCategoryName($category_d->{'source_name'});
				
				$full_name = $category;
				$s_desc = $name;
				$desc = $s_desc."<br />Наименование: ".$data->{'carname'}."<br />Производитель: ".$manufacturer."<br />";
				$customtitle = $category.": ".$name." (".$code.")";	
			}
			else{
				$category = $this->getRightCategoryName($category_d->{'name'});

				$anotherCategories = '<br />Подходит также для ';

				foreach($categories as $cat){
					$cat_d = json_decode($cat);

					if ($cat_d->{'parent'} == $this->DEFAULT_CATEGORY_NAME) $full_name.= trim($cat_d->{'name'});
//				else if ($cat_d->{'parent'} == $this->ALL_CATEGORY_NAME) $full_name.= trim("Все модели ".$cat_d->{'parent'});
					else {
						if($cat_d->{'parent'} == '')  $full_name.= trim($cat_d->{'name'});
						else if(strpos($cat_d->{'name'}, $cat_d->{'parent'})!== FALSE) $full_name.= trim($cat_d->{'name'});				
							else $full_name.= trim($cat_d->{'parent'}." ".$cat_d->{'name'});				
					}
				
					if (count($categories) > 1) {
						$full_name.="~";
						if ($cat_d->{'name'} != $category)
							$anotherCategories.= '<a href="'.$SHOP_LINK.$cat_d->{'slug'}.'">'.trim($cat_d->{'parent'}." ".$cat_d->{'name'}).'</a>, ';
					}
					else $anotherCategories = '';
				}
		
				if(strpos($full_name, "~") > 0) $full_name = substr($full_name, 0, strlen($full_name) - 1);

				if ($anotherCategories != "") {
					$anotherCategories = trim($anotherCategories);
					$anotherCategories = substr($anotherCategories, 0, strlen($anotherCategories) - 1).".";
				}
			
//echo iconv('UTF-8', 'windows-1251',"'".$anotherCategories."'"."<br />");
				if ($category_d->{'parent'} == $this->DEFAULT_CATEGORY_NAME) $s_desc = $name." (".$code.") для ".$category;
				else if ($category_d->{'parent'} == $this->DEFAULT_CATEGORY_NAME) $s_desc = $name." (".$code.") для всех моделей ".$category_d->{'parent'};
					 else $s_desc = $name." (".$code.") для ".$category_d->{'parent'}." ".$category;
				 
				$desc = $s_desc."<br />Наименование автомобиля: ".$data->{'carname'}."<br />Производитель: ".$source_data[3]."<br />".$anotherCategories;
				$customtitle = "Запчасти для ".$category_d->{'parent'}." ".$category.": ".$name;	
			}
						
			$table_name = $this->table_prefix."products";

			//1. products основная строка продукта
			//virtuemart_product_id virtuemart_vendor_id product_parent_id product_sku product_weight 
			//product_weight_uom product_length product_width product_height
			//product_lwh_uom product_url product_in_stock product_ordered low_stock_notification product_available_date
			// product_availability product_special product_sales
			//product_unit product_packaging product_params hits intnotes metarobot metaauthor layout published created_on
			// created_by modified_on modified_by locked_on	locked_by
					
			$low_stock_notification = 1;
			$html.=$this->db->insert($table_name, "$this->product_id,1,0,'$code',0,'KG',0,0,0,'M','',$inStock,0,$low_stock_notification,'$dateNow','',0,0,'',0,'min_order_level=0|max_order_level=0|',NULL,'','index,follow','$SITE_NAME','pdf',1,'$dateNow',42,'$dateNow',42,'$NULL_DATE',0");

			//2. products_ru_ru описание продукта	
			// virtuemart_product_id product_s_desc product_desc product_name metadesc metakey customtitle 	slug
			$table_name = $this->table_prefix."products_ru_ru";
			$alias = makeAliasFromName($name);
			
			$result_select = $this->db->select($table_name, "product_name", "slug LIKE '$alias%'", 'count');
			$html.=$result_select['html'];
			$max_name = $result_select['value'];
								 
			if ($max_name > 0){
				$max_name = $max_name + 1;
				$alias= $alias."_".$max_name;
			}
					
			$html.=$this->db->insert($table_name, "$this->product_id,'$s_desc','<p>$desc</p>','$name','$s_desc','$s_desc','$customtitle','".$alias."'");				
			//кодирока - в данном случае приводим все к utf-8, чтобы не менять кодировку в базе
			//mysql_query("SET NAMES 'cp-1251'");
			//mysql_query("SET CHARACTER SET 'cp-1251'");
					
			//3. product_categories категории продукта
			// id 	virtuemart_product_id 	virtuemart_category_id 	ordering

			$table_name = $this->table_prefix."product_categories";
			$html.=$this->db->insert($table_name, "$this->prod_cat_id,$this->product_id,$category_d->id,0");	
			$this->prod_cat_id++;
		
			//4. product_manufacturers Производитель
			//id virtuemart_product_id virtuemart_manufacturer_id
			$table_name = $this->table_prefix."product_manufacturers";
			$html.=$this->db->insert($table_name, "$this->prod_man_id,$this->product_id,$manufacturer_d->id");
			$this->prod_man_id++;						

			//5. product_media Картинки,	сначала нужна загрузка картинки в таблицу  	
			//	`tcp86_virtuemart_medias`, потом привязка по media_id
			//id virtuemart_product_id virtuemart_media_id ordering
			$table_name = $this->table_prefix."product_media"; 
			$query_insert = "insert into ".$table_name." values($this->prod_med_id,$this->product_id,$image_id,0)";
			//mysql_query($query_insert, $this->link);	
	
			//6. product_prices Цены
			//virtuemart_product_price_id 	virtuemart_product_id virtuemart_shoppergroup_id  product_price override 		
			//product_override_price product_tax_id product_discount_id product_currency product_price_vdate 
			//price_quantity_start	price_quantity_end 	created_on created_by 	modified_on modified_by locked_on locked_by
			$table_name = $this->table_prefix."product_prices";
			$html.=$this->db->insert($table_name, "$this->prod_price_id,$this->product_id,NULL,$price,NULL,0,0,0,131,NULL,NULL,NULL,NULL,'$dateNow',42,'$dateNow',42,'$NULL_DATE',0");
			$this->prod_price_id++;

			//7. product_customfields поля
			//virtuemart_product_id	virtuemart_customfield_id virtuemart_custom_id custom_value
			//custom_param published 	created_on 	created_by 	modified_on 	modified_by 	locked_on 	locked_by 	ordering		
			$table_name = $this->table_prefix."product_customfields";
			$virtuemart_custom_id = '16';//default value
			$html.=$this->db->insert($table_name, "$this->prod_cf_id,$this->product_id,$virtuemart_custom_id,'$full_name',NULL,'',0,'$dateNow',42,'$dateNow',42,'$NULL_DATE',0,0");			
			$this->prod_cf_id++;	
			
			$virtuemart_custom_id = '21';//default value
			$html.=$this->db->insert($table_name, "$this->prod_cf_id,$this->product_id,$virtuemart_custom_id,'$warehouse',NULL,'',0,'$dateNow',42,'$dateNow',42,'$NULL_DATE',0,0");			
			$this->prod_cf_id++;	

			return $html;		
		}
		
		private function updateExist($data, $productId){
			$html = "";
			
			$inStock = $data[4];	
			$price = floatval(trim(preg_replace('/[ р]+/', '', trim($data[5]))));
			
			$table_name = $this->table_prefix."products";
			$query_update = "UPDATE ".$table_name." SET product_in_stock='$inStock' WHERE virtuemart_product_id=$productId";
			mysql_query($query_update, $this->link);	
			if(mysql_error() != ""){
				$html.="<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_update."\n"."</p>";
			}
			$table_name = $this->table_prefix."product_prices";
			$query_update = "UPDATE ".$table_name." SET product_price='$price' WHERE virtuemart_product_id=$productId";
			mysql_query($query_update, $this->link);	
			if(mysql_error() != ""){
				$html.="<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_update."\n"."</p>";
			}	
			return $html;
		}
		
		private function getRightCategoryName($source){
			$result = '';
			$result =  str_replace ('\\', '', $source);
			$result = preg_replace('/\s+/', ' ', trim($result));
			return $result;
		}
	}
?>

=======
<?php
	include "import_products_buffer.php";
	
	class Products{
		private $product_id = 1;		//ID в таблице продуктов
		private $buffer_id = 1;			//ID строки в таблице импорта (таблица полное соответствие csv файлу, но с айдишниками
		private $prod_cat_id = 1;		//ID строки в таблице соответствия продукта с категорией(-ями)
		private $prod_man_id = 1;		//ID строки в таблице соответствия продукта с производителем(-ями)
		private $prod_med_id = 1;		//ID строки в таблице соответствия продукта с картинкой(-ами)
		private $prod_price_id = 1;		//ID строки в таблице соответствия продукта с ценой(-ами)
		private $prod_cf_id = 1;		//ID строки в таблице соответствия продукта с дополнительными полями
		private $table_prefix = "";		//префикс по умолчанию для таблицы Virtuemart
		private $db;					//коннект с БД
		private $DEFAULT_CATEGORY_NAME = "Другое";
		private $EMPTY_CATEGORY_NAME = "";
		private $ALL_CATEGORY_NAME = "все модели";
		
		function init($d, $tp){
			$html = "";
			
			$this->db = $d;
			$this->table_prefix = $tp;
			
			$result_select = $this->db->select($this->table_prefix."products", "virtuemart_product_id", "", 'max');
			$html.=$result_select['html'];
			$this->product_id = $result_select['value'] + 1;
			
			$result_select = $this->db->select($this->table_prefix."import", "id", "", 'max');
			$html.=$result_select['html'];
			$this->buffer_id = $result_select['value'] + 1;
									
			$result_select = $this->db->select($this->table_prefix."product_categories", "id", "", 'max');
			$html.=$result_select['html'];
			$this->prod_cat_id = $result_select['value'] + 1;
						
			$result_select = $this->db->select($this->table_prefix."product_manufacturers", "id", "", 'max');
			$html.=$result_select['html'];
			$this->prod_man_id = $result_select['value'] + 1;
			
			$result_select = $this->db->select($this->table_prefix."product_medias", "id", "", 'max');
			$html.=$result_select['html'];
			$this->prod_med_id = $result_select['value'] + 1;
			
			$result_select = $this->db->select($this->table_prefix."product_prices", "virtuemart_product_price_id", "", 'max');
			$html.=$result_select['html'];
			$this->prod_price_id = $result_select['value'] + 1;
						
			$result_select = $this->db->select($this->table_prefix."product_customfields", "virtuemart_customfield_id", "", 'max');
			$html.=$result_select['html'];
			$this->prod_cf_id = $result_select['value'] + 1;
					
			return $html;
		}
		
		function process($data, $categories, $manufacturer, $warehouse){
			$html = "";
			$session_id = mt_rand();
			$buffer = new Products_Buffer();
			$buffer->init($this->db, $this->table_prefix, $this->buffer_id, $session_id);
			$buffer_result = 0;
		
			if (count($categories) > 0){
				foreach($categories as $category){
					$html.= $this->manageBuffer($buffer, $data, $categories, $category, $manufacturer, $warehouse, $session_id);
				}
			}
			else $html.= $this->manageBuffer($buffer, $data, '', '', $manufacturerId, $warehouse, $session_id);
			
			return $html;
		}
		
		private function manageBuffer($buffer, $data, $categories, $category, $manufacturer, $warehouse, $session_id){
			$category_d = json_decode($category);
			$manufacturer_d = json_decode($manufacturer);
			
			$buffer_result = $buffer->processRecord($this->buffer_id, $data, $this->product_id, $category_d->id, $manufacturer_d->id, $warehouse, $session_id);
			$html = $buffer->getResult();
			//Задача по управлению обновлением/добавлением запчастей лежит на классе буффер, здесь только принимаем результат, и выполняем его
	
			if($buffer_result > 0) {
				//обновление
				$html.= $this->updateExist($data, $buffer_result);
			}elseif($buffer_result == 0	){
				//добавление
				$html.= $this->newProduct($categories, $category_d, $manufacturer_d, $buffer->getJson(), $data);
				$this->product_id = $this->product_id + 1;
				$this->buffer_id = $this->buffer_id + 1;	
			}else{
				//обновление, но обновлять не требуется
			}
			return $html;
		}
		
		private function newProduct($categories, $category_d, $manufacturer_d, $data_json, $source_data){
			
			$html = "";
			
			$dateNow = date("Y-m-d H:i:s");
			$NULL_DATE = '0000-00-00 00:00:00';	
			$SITE_NAME = $_SERVER['SERVER_NAME'];
			$SHOP_LINK = '/catalog/';
			
			$data = json_decode($data_json);
			
			$name =  $data->{'name'};
			$name =  str_replace ('\\', '', $name);
//$html.=iconv('UTF-8','windows-1251',$data_json)."<br />";

			$code = $data->{'code'};
			$code =  str_replace ('\\', '', $code);
			$inStock = $data->{'inStock'};	
			$price = $data->{'price'};
			$manufacturer = $manufacturer_d->{'name'};

			$full_name ='';

			if ($category_d->{'source_name'} != '') {
				$category = $this->getRightCategoryName($category_d->{'source_name'});
				
				$full_name = $category;
				$s_desc = $name;
				$desc = $s_desc."<br />Наименование: ".$data->{'carname'}."<br />Производитель: ".$manufacturer."<br />";
				$customtitle = $category.": ".$name." (".$code.")";	
			}
			else{
				$category = $this->getRightCategoryName($category_d->{'name'});

				$anotherCategories = '<br />Подходит также для ';

				foreach($categories as $cat){
					$cat_d = json_decode($cat);

					if ($cat_d->{'parent'} == $this->DEFAULT_CATEGORY_NAME) $full_name.= trim($cat_d->{'name'});
//				else if ($cat_d->{'parent'} == $this->ALL_CATEGORY_NAME) $full_name.= trim("Все модели ".$cat_d->{'parent'});
					else {
						if($cat_d->{'parent'} == '')  $full_name.= trim($cat_d->{'name'});
						else if(strpos($cat_d->{'name'}, $cat_d->{'parent'})!== FALSE) $full_name.= trim($cat_d->{'name'});				
							else $full_name.= trim($cat_d->{'parent'}." ".$cat_d->{'name'});				
					}
				
					if (count($categories) > 1) {
						$full_name.="~";
						if ($cat_d->{'name'} != $category)
							$anotherCategories.= '<a href="'.$SHOP_LINK.$cat_d->{'slug'}.'">'.trim($cat_d->{'parent'}." ".$cat_d->{'name'}).'</a>, ';
					}
					else $anotherCategories = '';
				}
		
				if(strpos($full_name, "~") > 0) $full_name = substr($full_name, 0, strlen($full_name) - 1);

				if ($anotherCategories != "") {
					$anotherCategories = trim($anotherCategories);
					$anotherCategories = substr($anotherCategories, 0, strlen($anotherCategories) - 1).".";
				}
			
//echo iconv('UTF-8', 'windows-1251',"'".$anotherCategories."'"."<br />");
				if ($category_d->{'parent'} == $this->DEFAULT_CATEGORY_NAME) $s_desc = $name." (".$code.") для ".$category;
				else if ($category_d->{'parent'} == $this->DEFAULT_CATEGORY_NAME) $s_desc = $name." (".$code.") для всех моделей ".$category_d->{'parent'};
					 else $s_desc = $name." (".$code.") для ".$category_d->{'parent'}." ".$category;
				 
				$desc = $s_desc."<br />Наименование автомобиля: ".$data->{'carname'}."<br />Производитель: ".$source_data[3]."<br />".$anotherCategories;
				$customtitle = "Запчасти для ".$category_d->{'parent'}." ".$category.": ".$name;	
			}
						
			$table_name = $this->table_prefix."products";

			//1. products основная строка продукта
			//virtuemart_product_id virtuemart_vendor_id product_parent_id product_sku product_weight 
			//product_weight_uom product_length product_width product_height
			//product_lwh_uom product_url product_in_stock product_ordered low_stock_notification product_available_date
			// product_availability product_special product_sales
			//product_unit product_packaging product_params hits intnotes metarobot metaauthor layout published created_on
			// created_by modified_on modified_by locked_on	locked_by
					
			$low_stock_notification = 1;
			$html.=$this->db->insert($table_name, "$this->product_id,1,0,'$code',0,'KG',0,0,0,'M','',$inStock,0,$low_stock_notification,'$dateNow','',0,0,'',0,'min_order_level=0|max_order_level=0|',NULL,'','index,follow','$SITE_NAME','pdf',1,'$dateNow',42,'$dateNow',42,'$NULL_DATE',0");

			//2. products_ru_ru описание продукта	
			// virtuemart_product_id product_s_desc product_desc product_name metadesc metakey customtitle 	slug
			$table_name = $this->table_prefix."products_ru_ru";
			$alias = makeAliasFromName($name);
			
			$result_select = $this->db->select($table_name, "product_name", "slug LIKE '$alias%'", 'count');
			$html.=$result_select['html'];
			$max_name = $result_select['value'];
								 
			if ($max_name > 0){
				$max_name = $max_name + 1;
				$alias= $alias."_".$max_name;
			}
					
			$html.=$this->db->insert($table_name, "$this->product_id,'$s_desc','<p>$desc</p>','$name','$s_desc','$s_desc','$customtitle','".$alias."'");				
			//кодирока - в данном случае приводим все к utf-8, чтобы не менять кодировку в базе
			//mysql_query("SET NAMES 'cp-1251'");
			//mysql_query("SET CHARACTER SET 'cp-1251'");
					
			//3. product_categories категории продукта
			// id 	virtuemart_product_id 	virtuemart_category_id 	ordering

			$table_name = $this->table_prefix."product_categories";
			$html.=$this->db->insert($table_name, "$this->prod_cat_id,$this->product_id,$category_d->id,0");	
			$this->prod_cat_id++;
		
			//4. product_manufacturers Производитель
			//id virtuemart_product_id virtuemart_manufacturer_id
			$table_name = $this->table_prefix."product_manufacturers";
			$html.=$this->db->insert($table_name, "$this->prod_man_id,$this->product_id,$manufacturer_d->id");
			$this->prod_man_id++;						

			//5. product_media Картинки,	сначала нужна загрузка картинки в таблицу  	
			//	`tcp86_virtuemart_medias`, потом привязка по media_id
			//id virtuemart_product_id virtuemart_media_id ordering
			$table_name = $this->table_prefix."product_media"; 
			$query_insert = "insert into ".$table_name." values($this->prod_med_id,$this->product_id,$image_id,0)";
			//mysql_query($query_insert, $this->link);	
	
			//6. product_prices Цены
			//virtuemart_product_price_id 	virtuemart_product_id virtuemart_shoppergroup_id  product_price override 		
			//product_override_price product_tax_id product_discount_id product_currency product_price_vdate 
			//price_quantity_start	price_quantity_end 	created_on created_by 	modified_on modified_by locked_on locked_by
			$table_name = $this->table_prefix."product_prices";
			$html.=$this->db->insert($table_name, "$this->prod_price_id,$this->product_id,NULL,$price,NULL,0,0,0,131,NULL,NULL,NULL,NULL,'$dateNow',42,'$dateNow',42,'$NULL_DATE',0");
			$this->prod_price_id++;

			//7. product_customfields поля
			//virtuemart_product_id	virtuemart_customfield_id virtuemart_custom_id custom_value
			//custom_param published 	created_on 	created_by 	modified_on 	modified_by 	locked_on 	locked_by 	ordering		
			$table_name = $this->table_prefix."product_customfields";
			$virtuemart_custom_id = '16';//default value
			$html.=$this->db->insert($table_name, "$this->prod_cf_id,$this->product_id,$virtuemart_custom_id,'$full_name',NULL,'',0,'$dateNow',42,'$dateNow',42,'$NULL_DATE',0,0");			
			$this->prod_cf_id++;	
			
			$virtuemart_custom_id = '21';//default value
			$html.=$this->db->insert($table_name, "$this->prod_cf_id,$this->product_id,$virtuemart_custom_id,'$warehouse',NULL,'',0,'$dateNow',42,'$dateNow',42,'$NULL_DATE',0,0");			
			$this->prod_cf_id++;	

			return $html;		
		}
		
		private function updateExist($data, $productId){
			$html = "";
			
			$inStock = $data[4];	
			$price = floatval(trim(preg_replace('/[ р]+/', '', trim($data[5]))));
			
			$table_name = $this->table_prefix."products";
			$query_update = "UPDATE ".$table_name." SET product_in_stock='$inStock' WHERE virtuemart_product_id=$productId";
			mysql_query($query_update, $this->link);	
			if(mysql_error() != ""){
				$html.="<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_update."\n"."</p>";
			}
			$table_name = $this->table_prefix."product_prices";
			$query_update = "UPDATE ".$table_name." SET product_price='$price' WHERE virtuemart_product_id=$productId";
			mysql_query($query_update, $this->link);	
			if(mysql_error() != ""){
				$html.="<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_update."\n"."</p>";
			}	
			return $html;
		}
		
		private function getRightCategoryName($source){
			$result = '';
			$result =  str_replace ('\\', '', $source);
			$result = preg_replace('/\s+/', ' ', trim($result));
			return $result;
		}
	}
?>

>>>>>>> 1e1b309bbf9e27aa09f1eef63d7bfeed85150451
