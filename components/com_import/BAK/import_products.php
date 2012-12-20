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
		private $link;					//коннект с БД
		
		function init($l, $tp){
			$this->link = $l;
			$this->table_prefix = $tp;
			
			$table_name = $this->table_prefix."products";
			$query_select = "SELECT MAX(virtuemart_product_id) FROM ".$table_name;
			$product_max_select = mysql_query($query_select, $this->link);
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
			}else{
				$product_max = mysql_fetch_array($product_max_select, MYSQL_NUM);
				$this->product_id = $product_max[0] + 1;
			}

			$table_name = $this->table_prefix."import";
			$query_select = "SELECT MAX(id) FROM ".$table_name;
			$buffer_max_select = mysql_query($query_select, $this->link);
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
			}else{
				$buffer_max = mysql_fetch_array($buffer_max_select, MYSQL_NUM);
				$this->buffer_id = $buffer_max[0] + 1;
			}
			
			$table_name = $this->table_prefix."product_categories";	
			$query_select = "SELECT MAX(id) FROM ".$table_name;
			$prod_cat_max_select = mysql_query($query_select, $this->link);
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
			}else{
				$prod_cat_max = mysql_fetch_array($prod_cat_max_select, MYSQL_NUM);
				$this->prod_cat_id = $prod_cat_max[0] + 1;
			}		
			
			$table_name = $this->table_prefix."product_manufacturers";
			$query_select = "SELECT MAX(id) FROM ".$table_name;
			$prod_man_max_select = mysql_query($query_select, $this->link);
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
			}else{
				$prod_man_max = mysql_fetch_array($prod_man_max_select, MYSQL_NUM);
				$this->prod_man_id = $prod_man_max[0] + 1;
			}				
			
			$table_name = $this->table_prefix."product_medias"; 
			$query_select = "SELECT MAX(id) FROM ".$table_name;
			$prod_med_max_select = mysql_query($query_select, $this->link);
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
			}else{
				$prod_med_max = mysql_fetch_array($prod_med_max_select, MYSQL_NUM);
				$this->prod_med_id = $prod_med_max[0] + 1;
			}		
						
			$table_name = $this->table_prefix."product_prices";		
			$query_select = "SELECT MAX(virtuemart_product_price_id) FROM ".$table_name;
			$prod_price_max_select = mysql_query($query_select, $this->link);
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
			}else{
				$prod_price_max = mysql_fetch_array($prod_price_max_select, MYSQL_NUM);
				$this->prod_price_id = $prod_price_max[0] + 1;
			}	
					
			$table_name = $this->table_prefix."product_customfields";		
			$query_select = "SELECT MAX(virtuemart_customfield_id) FROM ".$table_name;
			$prod_cf_max_select = mysql_query($query_select, $this->link);
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
			}else{
				$prod_price_max = mysql_fetch_array($prod_cf_max_select, MYSQL_NUM);
				$this->prod_cf_id = $prod_cf_max[0] + 1;
			}		
		}
		
		function process($data, $categoryId, $parentCategoryId, $manufacturerId, $warehouse){
			$buffer = new Products_Buffer();
			$buffer->init($this->link, $this->table_prefix, $this->buffer_id);
			$buffer_result = 0;
			$buffer_result = $buffer->processRecord($data, $this->product_id, $categoryId, $manufacturerId, $warehouse);
			
			//Задача по управлению обновлением/добавлением запчастей лежит на классе буффер, здесь только принимаем результат, и выполняем его
			if($buffer_result > 0) {
				//обновление
				$this->updateExist($data, $buffer_result);
			}elseif($buffer_result == 0	){
				//добавление
				$this->newProduct($data, $categoryId, $parentCategoryId, $manufacturerId, $warehouse);
			}else{
				//обновление, но обновлять не требуется
			}
			$this->product_id = $this->product_id + 1;
			$this->buffer_id = $this->buffer_id + 1;
		}
		
		private function newProduct($data, $categoryId, $parentCategoryId, $manufacturerId, $warehouse){
			$name = trim($data[0]);
			$code = trim($data[2]);
			if ($data[1] == "") {
				$category = "Общая";
				$category_alias = "all";
			}else{
				$category = preg_replace('/\s+/', ' ', trim($data[1]));
				$category_alias = $category;
			}	
			$manufacturer = trim($data[3]);
			$inStock = $data[4];	
			$price = floatval(trim(preg_replace('/[ р]+/', '', trim($data[5]))));

			$desc = $name." (".$code.") для ".$category."\n".$manufacturer;
				
			$table_name = $this->table_prefix."products";
						
			$dateNow = date("Y-m-d H:i:s");
				
			//1. products основная строка продукта
			//virtuemart_product_id virtuemart_vendor_id product_parent_id product_sku product_weight 
			//product_weight_uom product_length product_width product_height
			//product_lwh_uom product_url product_in_stock product_ordered low_stock_notification product_available_date
			// product_availability product_special product_sales
			//product_unit product_packaging product_params hits intnotes metarobot metaauthor layout published created_on
			// created_by modified_on modified_by locked_on	locked_by
					
			$low_stock_notification = 1;
			$query_insert = "insert into ".$table_name." values($this->product_id,1,0,'$code',0,'KG',0,0,0,'M','',$inStock,0,$low_stock_notification,'$dateNow','',0,0,'',0,'min_order_level=0|max_order_level=0|',NULL,'','index,follow','www.khparts.ru','pdf',1,'$dateNow',42,'$dateNow',42,'0000-00-00 00:00:00',0)";
			mysql_query($query_insert, $this->link);	
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."\n".$query_select."</p>";
			}			
			//2. products_ru_ru описание продукта	
			// virtuemart_product_id product_s_desc product_desc product_name metadesc metakey customtitle 	slug
			$table_name = $this->table_prefix."products_ru_ru";
			$alias = makeAliasFromName($name);
					 
			$query_select = "SELECT count(product_name) FROM ".$table_name." where slug LIKE '$alias%'";
			$result_name = mysql_query($query_select, $this->link);
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
			}						
			$max_name = mysql_num_rows($result_name);
			if ($max_name > 0){
				$name_arr = mysql_fetch_array($result_name, MYSQL_NUM);
				$num = $name_arr[0] + 1;
				$alias= $alias."_".$num;
			}
					
			$query_insert = "insert into ".$table_name." values($this->product_id,'$desc','<p>$desc</p>','$name','$desc','$desc','','".$alias."')";
				
			//кодирока - в данном случае приводим все к utf-8, чтобы не менять кодировку в базе
			//mysql_query("SET NAMES 'cp-1251'");
			//mysql_query("SET CHARACTER SET 'cp-1251'");
			mysql_query($query_insert, $this->link);
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error in insert to ".$table_name." =".$num.":".mysql_error()."\n".$query_insert."\n".$query_select."</p>";
			}
					
			//3. product_categories категории продукта
			// id 	virtuemart_product_id 	virtuemart_category_id 	ordering

			$table_name = $this->table_prefix."product_categories";
			if(isset($this->parentCategoryId)){
				$query_insert = "insert into ".$table_name." values($this->prod_cat_id,$this->product_id,$parentCategoryId,0)";
				mysql_query($query_insert, $this->link);	
				if(mysql_error() != ""){
					echo "<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
				}else $this->prod_cat_id++;
			}
			$query_insert = "insert into ".$table_name." values($this->prod_cat_id,$this->product_id,$categoryId,0)";
			mysql_query($query_insert, $this->link);	
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
			}else $this->prod_cat_id++;						

			//4. product_manufacturers Производитель
			//id virtuemart_product_id virtuemart_manufacturer_id
			$table_name = $this->table_prefix."product_manufacturers";
			$query_insert = "insert into ".$table_name." values($this->prod_man_id,$this->product_id,$manufacturerId)";
			mysql_query($query_insert, $this->link);	
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
			}$this->prod_man_id++;						

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
			$query_insert = "insert into ".$table_name." values($this->prod_price_id,$this->product_id,NULL,$price,NULL,0,0,0,131,NULL,NULL,NULL,NULL,'$dateNow',42,'$dateNow',42,'0000-00-00 00:00:00',0)";
			mysql_query($query_insert, $this->link);
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
			}$this->prod_price_id++;

			$virtuemart_custom_id = '21';//default value
			//7. product_customfields поля
			//virtuemart_product_id	virtuemart_customfield_id virtuemart_custom_id custom_value
			//custom_param published 	created_on 	created_by 	modified_on 	modified_by 	locked_on 	locked_by 	ordering		
			$table_name = $this->table_prefix."product_customfields";
			$query_insert = "insert into ".$table_name." values($this->product_id,$this->prod_cf_id,$virtuemart_custom_id,'$warehouse',NULL,'',0,'$dateNow',42,'$dateNow',42,'0000-00-00 00:00:00',0,0)";
			mysql_query($query_insert, $this->link);
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
			}$this->prod_cf_id++;				
		}
		
		private function updateExist($data, $productId){
			$inStock = $data[4];	
			$price = floatval(trim(preg_replace('/[ р]+/', '', trim($data[5]))));
			
			$table_name = $this->table_prefix."products";
			$query_update = "UPDATE ".$table_name." SET product_in_stock='$inStock' WHERE virtuemart_product_id=$productId";
			mysql_query($query_update, $this->link);	
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_update."\n"."</p>";
			}
			$table_name = $this->table_prefix."product_prices";
			$query_update = "UPDATE ".$table_name." SET product_price='$price' WHERE virtuemart_product_id=$productId";
			mysql_query($query_update, $this->link);	
			if(mysql_error() != ""){
				echo "<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_update."\n"."</p>";
			}	
		}
		
	}
?>

