<<<<<<< HEAD
<?php 
	include($_SERVER['DOCUMENT_ROOT'].'/libraries/lib_db.php');
	
	$process = new Dbprocess();
	deleteAll($process);
	
function deleteAll($db){
	$DELETED_MANUFACTER_CATEGORY = 2;
	//подключение к БД
	$link = $db->connectToDb();
	$table_prefix = $db->getTablePrefix().$db->getVMTablePrefix();
	
	$query_parent_categories = "select category_child_id from ".$table_prefix."category_categories where category_parent_id=0";
	
	$result = mysql_query($query_parent_categories, $link);
	$rows_num = mysql_num_rows($result);

	//if record doesn't exist insert record in tables
	if ($rows_num > 0){
		while ($row = mysql_fetch_row($result)) {
			$category_nums[] = $row[0];
		}
	}
	$table_delete =array(
	$table_prefix."categories", $table_prefix."categories_ru_ru", 
	$table_prefix."category_medias");
	
	$table_error .= delete_data($table_delete, "virtuemart_category_id not in('".implode("','", $category_nums)."')", $link);
	
	$table_delete = array($table_prefix."category_categories"); 
	$table_error .= delete_data($table_delete, "category_child_id not in('".implode("','", $category_nums)."')", $link);
	
	$table_delete = array($table_prefix."manufacturers_ru_ru", $table_prefix."manufacturer_medias"); 
	$table_error .= delete_data($table_delete, "virtuemart_manufacturer_id in (SELECT virtuemart_manufacturer_id from ".$table_prefix."manufacturers 
WHERE virtuemart_manufacturercategories_id=2)", $link);
	$table_delete = array($table_prefix."manufacturers"); 
	$table_error .= delete_data($table_delete, "virtuemart_manufacturercategories_id=".$DELETED_MANUFACTER_CATEGORY, $link);

	$table_delete =array($table_prefix."import",
	$table_prefix."products",$table_prefix."products_ru_ru",
	$table_prefix."product_categories", $table_prefix."product_manufacturers", 
	$table_prefix."product_medias", $table_prefix."product_relations", 
	$table_prefix."product_prices", $table_prefix."product_shoppergroups",
	$table_prefix."product_customfields");
	
	$table_error .= delete_data($table_delete, '1', $link);

	if($table_error != '') echo "<p style='color:red;'>".'При удалении возникли ошибки, таблицы: '.$table_error."</p>";
	else echo "<p style='color:blue;'>".'Удаление прошло успешно!'."</p>";
}

function delete_data($table_delete, $where, $link){
	$table_error = '';
	for($i = 0; $i <count($table_delete); $i++){
		$query_delete = "DELETE FROM ".$table_delete[$i]." WHERE ".$where;
		if (!mysql_query($query_delete, $link)) $table_error .= $table_delete[$i].", ";
		if(mysql_error() != ""){
			echo "<p style='color:red;'>".'Ошибка удаления в таблице!'.$table_delete[$i].": ".mysql_error()."\n".$query_delete."\n"."</p>";
		}					
	}
	return $table_error;
}

unset($process);
?>

=======
<?php 
	include($_SERVER['DOCUMENT_ROOT'].'/libraries/lib_db.php');
	
	$process = new Dbprocess();
	deleteAll($process);
	
function deleteAll($db){
	$DELETED_MANUFACTER_CATEGORY = 2;
	//подключение к БД
	$link = $db->connectToDb();
	$table_prefix = $db->getTablePrefix().$db->getVMTablePrefix();
	
	$query_parent_categories = "select category_child_id from ".$table_prefix."category_categories where category_parent_id=0";
	
	$result = mysql_query($query_parent_categories, $link);
	$rows_num = mysql_num_rows($result);

	//if record doesn't exist insert record in tables
	if ($rows_num > 0){
		while ($row = mysql_fetch_row($result)) {
			$category_nums[] = $row[0];
		}
	}
	$table_delete =array(
	$table_prefix."categories", $table_prefix."categories_ru_ru", 
	$table_prefix."category_medias");
	
	$table_error .= delete_data($table_delete, "virtuemart_category_id not in('".implode("','", $category_nums)."')", $link);
	
	$table_delete = array($table_prefix."category_categories"); 
	$table_error .= delete_data($table_delete, "category_child_id not in('".implode("','", $category_nums)."')", $link);
	
	$table_delete = array($table_prefix."manufacturers_ru_ru", $table_prefix."manufacturer_medias"); 
	$table_error .= delete_data($table_delete, "virtuemart_manufacturer_id in (SELECT virtuemart_manufacturer_id from ".$table_prefix."manufacturers 
WHERE virtuemart_manufacturercategories_id=2)", $link);
	$table_delete = array($table_prefix."manufacturers"); 
	$table_error .= delete_data($table_delete, "virtuemart_manufacturercategories_id=".$DELETED_MANUFACTER_CATEGORY, $link);

	$table_delete =array($table_prefix."import",
	$table_prefix."products",$table_prefix."products_ru_ru",
	$table_prefix."product_categories", $table_prefix."product_manufacturers", 
	$table_prefix."product_medias", $table_prefix."product_relations", 
	$table_prefix."product_prices", $table_prefix."product_shoppergroups",
	$table_prefix."product_customfields");
	
	$table_error .= delete_data($table_delete, '1', $link);

	if($table_error != '') echo "<p style='color:red;'>".'При удалении возникли ошибки, таблицы: '.$table_error."</p>";
	else echo "<p style='color:blue;'>".'Удаление прошло успешно!'."</p>";
}

function delete_data($table_delete, $where, $link){
	$table_error = '';
	for($i = 0; $i <count($table_delete); $i++){
		$query_delete = "DELETE FROM ".$table_delete[$i]." WHERE ".$where;
		if (!mysql_query($query_delete, $link)) $table_error .= $table_delete[$i].", ";
		if(mysql_error() != ""){
			echo "<p style='color:red;'>".'Ошибка удаления в таблице!'.$table_delete[$i].": ".mysql_error()."\n".$query_delete."\n"."</p>";
		}					
	}
	return $table_error;
}

unset($process);
?>

>>>>>>> 1e1b309bbf9e27aa09f1eef63d7bfeed85150451
