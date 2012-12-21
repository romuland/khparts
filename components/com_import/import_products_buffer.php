<<<<<<< HEAD
<?php
	class Products_Buffer{
		private $table_prefix = "virtuemart_";
		private $db;
		private $update_record = true;	//флажок - обновляем или нет запись, если нашли совпадение
		private $only_add;				//флажок - если True, заносим в базу, даже если нашли совпадение
		private $html = ""; 			//результат выполнения
		private $json_result = "";
		private $buffer_session_id = 0;
		function init($d, $tp, $id, $ses_id){
			$this->db = $d;
			$this->table_prefix = $tp;
			$this->buffer_id = $id;
			$this->buffer_id = $id;
			$this->buffer_session_id = $ses_id;
			if (isset($_POST['onlyadd'])) $this->only_add = true;
			else $this->only_add = false;
		}
		function getResult(){
			return iconv('UTF-8', 'windows-1251', $this->html);
		}
		function getJson(){
			return $this->json_result;
		}		
		function processRecord($buffer_id, $data, $productId, $categoryId, $manufacturerId, $warehouse, $session_id)
		{
			$name = trim($data[0]);
			$name = addslashes($name);
			$carname = str_replace ('\\', '', $data[1]);
			$carname = addslashes($carname);
			$code = trim($data[2]);
			$code  = addslashes($code);
			$inStock = $data[4];	
			$price = floatval(trim(preg_replace('/[ р]+/', '', trim($data[5]))));
			if($data[3] != "") $manufacturer = $manufacturerId."###".$data[3];
			else $manufacturer = $manufacturerId;
			$this->json_result = '{"name":"'.$name.'", "code":"'.$code.'", "inStock":"'.$inStock.'", "price":"'.$price.'", "carname":"'.$carname.'", "warehouse":"'.$warehouse.'"}';
			$table_name = $this->table_prefix."import";
			$where = "product_name='$name'";
			$where = $where." and virtuemart_category_id=".$categoryId;
			$where = $where." and product_sku='$code'";
			$where = $where." and virtuemart_manufacturer_id='$manufacturer'";
			$where = $where." and warehouse='$warehouse'";
				
			//check if record exist
			$result_select = $this->db->select($table_name, "", "$where", '');
			$html.=$result_select['html'];
			$rows_num = $result_select['rows'];
			
			//if record doesn't exist insert record in tables

			if ($rows_num == 0){
				
				$temp = $this->db->insert($table_name, "$buffer_id,$productId,'$name',$categoryId,'$code','$manufacturer',$inStock,$price,'$warehouse'");
				
				if($temp != ""){
					$this->html.= $temp;
					return -1;
				}
				else return 0;
			}
			else{
				if($this->only_add && $this->buffer_session_id != $session_id) {
					$temp = $this->db->insert($table_name, "$this->buffer_id,$productId,'$name',$categoryId,'$code','$manufacturer',$inStock,$price,'$warehouse'");
					if($temp != ""){
						$this->html.= $temp;
						return -1;
					}
					else return 0;
				}
				else{
					if($this->update_record){
						if($result_select['rows']['product_in_stock'] != $inStock or $result_select['rows']['product_price'] != $price){
							$query_update = "UPDATE ".$table_name." SET product_in_stock='$inStock', product_price='$price' WHERE virtuemart_product_id=".$arr['virtuemart_product_id'];
							mysql_query($query_update, $this->link);	
							if(mysql_error() != ""){
								$this->html.="<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_update."\n"."</p>";
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

=======
<?php
	class Products_Buffer{
		private $table_prefix = "virtuemart_";
		private $db;
		private $update_record = true;	//флажок - обновляем или нет запись, если нашли совпадение
		private $only_add;				//флажок - если True, заносим в базу, даже если нашли совпадение
		private $html = ""; 			//результат выполнения
		private $json_result = "";
		private $buffer_session_id = 0;
		function init($d, $tp, $id, $ses_id){
			$this->db = $d;
			$this->table_prefix = $tp;
			$this->buffer_id = $id;
			$this->buffer_id = $id;
			$this->buffer_session_id = $ses_id;
			if (isset($_POST['onlyadd'])) $this->only_add = true;
			else $this->only_add = false;
		}
		function getResult(){
			return iconv('UTF-8', 'windows-1251', $this->html);
		}
		function getJson(){
			return $this->json_result;
		}		
		function processRecord($buffer_id, $data, $productId, $categoryId, $manufacturerId, $warehouse, $session_id)
		{
			$name = trim($data[0]);
			$name = addslashes($name);
			$carname = str_replace ('\\', '', $data[1]);
			$carname = addslashes($carname);
			$code = trim($data[2]);
			$code  = addslashes($code);
			$inStock = $data[4];	
			$price = floatval(trim(preg_replace('/[ р]+/', '', trim($data[5]))));
			if($data[3] != "") $manufacturer = $manufacturerId."###".$data[3];
			else $manufacturer = $manufacturerId;
			$this->json_result = '{"name":"'.$name.'", "code":"'.$code.'", "inStock":"'.$inStock.'", "price":"'.$price.'", "carname":"'.$carname.'", "warehouse":"'.$warehouse.'"}';
			$table_name = $this->table_prefix."import";
			$where = "product_name='$name'";
			$where = $where." and virtuemart_category_id=".$categoryId;
			$where = $where." and product_sku='$code'";
			$where = $where." and virtuemart_manufacturer_id='$manufacturer'";
			$where = $where." and warehouse='$warehouse'";
				
			//check if record exist
			$result_select = $this->db->select($table_name, "", "$where", '');
			$html.=$result_select['html'];
			$rows_num = $result_select['rows'];
			
			//if record doesn't exist insert record in tables

			if ($rows_num == 0){
				
				$temp = $this->db->insert($table_name, "$buffer_id,$productId,'$name',$categoryId,'$code','$manufacturer',$inStock,$price,'$warehouse'");
				
				if($temp != ""){
					$this->html.= $temp;
					return -1;
				}
				else return 0;
			}
			else{
				if($this->only_add && $this->buffer_session_id != $session_id) {
					$temp = $this->db->insert($table_name, "$this->buffer_id,$productId,'$name',$categoryId,'$code','$manufacturer',$inStock,$price,'$warehouse'");
					if($temp != ""){
						$this->html.= $temp;
						return -1;
					}
					else return 0;
				}
				else{
					if($this->update_record){
						if($result_select['rows']['product_in_stock'] != $inStock or $result_select['rows']['product_price'] != $price){
							$query_update = "UPDATE ".$table_name." SET product_in_stock='$inStock', product_price='$price' WHERE virtuemart_product_id=".$arr['virtuemart_product_id'];
							mysql_query($query_update, $this->link);	
							if(mysql_error() != ""){
								$this->html.="<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_update."\n"."</p>";
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

>>>>>>> 1e1b309bbf9e27aa09f1eef63d7bfeed85150451
