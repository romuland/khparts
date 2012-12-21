<?php
	
function deleteAll($db){
	
	//подключение к БД
	$link = $db->connectToDb();
	$table_prefix = $db->getTablePrefix().$db->getVMTablePrefix();
	
	echo "<p><a href='javascript:history.back();'><--- ".iconv('UTF-8', 'windows-1251', 'Назад')."</a></p>";
	echo iconv('UTF-8', 'windows-1251', 'Удаление начинаетя, пожалуйста подождите...<br />');;
	
	$table_delete =array($table_prefix."import",
	$table_prefix."products",$table_prefix."products_ru_ru",
	$table_prefix."product_categories", $table_prefix."product_manufacturers", 
	$table_prefix."product_medias", $table_prefix."product_relations", 
	$table_prefix."product_prices", $table_prefix."product_shoppergroups",
	$table_prefix."product_customfields", 
	$table_prefix."manufacturers", $table_prefix."manufacturers_ru_ru", 
	$table_prefix."manufacturer_medias",
	$table_prefix."categories", $table_prefix."categories_ru_ru", 
	$table_prefix."category_categories", $table_prefix."category_medias");
	$isError = false;
	$table_error = '';
	for($i = 0; $i <count($table_delete); $i++){
		$query_delete = "DELETE FROM ".$table_delete[$i]." WHERE 1";
		if (!mysql_query($query_delete, $link)) {$isError = true;$table_error = $table_delete[$i];}
		if(mysql_error() != ""){
			echo "<p style='color:red;'>Error in insert to ".$table_name.": ".mysql_error()."\n".$query_delete."\n"."</p>";
		}					
	}
	if($isError ) echo iconv('UTF-8', 'windows-1251', "При удалении возникли ошибки, таблицы: $table_error<br />");
	else echo iconv('UTF-8', 'windows-1251', "Удаление прошло успешно!<br />");
}
?>

