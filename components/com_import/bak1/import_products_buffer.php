<?php
	class Products_Buffer{
		private $table_prefix = "virtuemart_";
		private $link;
		private $update_record = true;	//флажок - обновляем или нет запись, если нашли совпадение
		private $only_add;				//флажок - если True, заносим в базу, даже если нашли совпадение
		private $html = ""; 			//результат выполнения
		private $json_result = "";
		
		function init($l, $tp, $id){
			$this->link = $l;
			$this->table_prefix = $tp;
			$this->buffer_id = $id;
			$this->json_result = "";
			if (isset($_POST['onlyadd'])) $this->only_add = true;
			else $this->only_add = false;
		}
		function getResult(){
			return iconv('UTF-8', 'windows-1251', $this->html);
		}
		function getJson(){
			return $this->json_result;
		}		
		function processRecord($buffer_id, $data, $productId, $categoryId, $manufacturerId, $warehouse)
		{
			$name = trim($data[0]);
			$code = trim($data[2]);
			$inStock = $data[4];	
			$price = floatval(trim(preg_replace('/[ р]+/', '', trim($data[5]))));
			
			$this->json_result = '{"name":"'.$name.'", "code":"'.$code.'", "inStock":"'.$inStock.'", "price":"'.$price.'", "carname":"'.$data[1].'", "warehouse":"'.$warehouse.'"}';
					
			$table_name = $this->table_prefix."import";
			//check if record exist
			$where = "product_name='$name'";
			$where = $where." and virtuemart_category_id=".$categoryId;
			$where = $where." and product_sku='$code'";
			$where = $where." and virtuemart_manufacturer_id=".$manufacturerId;
			$where = $where." and warehouse='$warehouse'";
			
			$query_select = "select * from ".$table_name." where ".$where;

			$result = mysql_query($query_select, $this->link);
			$rows_num = mysql_num_rows($result);	

			//if record doesn't exist insert record in tables
			if ($rows_num == 0){
				
				$query_insert = "insert into ".$table_name." values($buffer_id,$productId,'$name',$categoryId,'$code',$manufacturerId,$inStock,$price,'$warehouse')";

				mysql_query($query_insert, $this->link);	
				if(mysql_error() != ""){
					$this->html="<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."\n".$query_select."</p>";
					return -1;
				}
				else return 0;
			}
			else{
				if($this->only_add) {
					$query_insert = "insert into ".$table_name." values($this->buffer_id,$productId,'$name',$categoryId,'$code',$manufacturerId,$inStock,$price,'$warehouse')";

					mysql_query($query_insert, $this->link);	
					if(mysql_error() != ""){
						$this->html="<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."\n".$query_select."</p>";
						return -1;
					}
					else return 0;
				}
				else{
					if($this->update_record){
						$arr = mysql_fetch_assoc($result);
						if($arr['product_in_stock'] != $inStock or $arr['product_price'] != $price){
							$query_update = "UPDATE ".$table_name." SET product_in_stock='$inStock', product_price='$price' WHERE virtuemart_product_id=".$arr['virtuemart_product_id'];
							mysql_query($query_update, $this->link);	
							if(mysql_error() != ""){
								$this->html="<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_update."\n"."</p>";
								return -1;
							}
							else return $arr['virtuemart_product_id'];
						}
						else return -1;
					}
					else return -1;
				}
			}
		}
		
	}
?>

