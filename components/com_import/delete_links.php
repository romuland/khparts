<?php 
	defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
	include_once($_SERVER['DOCUMENT_ROOT'].DS.'libraries'.DS.'lib_db.php');
	
	
	$process = new Dbprocess();
	deleteLinks($process);
	
function deleteLinks($db){
	
	//подключение к БД
	$link = $db->connectToDb();
	$table_prefix = $db->getTablePrefix().$db->getVMTablePrefix();
	
	$table_delete = $table_prefix."category_links";
	$isError = false;
	$table_error = '';
	if ($_GET["data"]=='') $where = "1";
	else $where = $_GET["data"];
	$query_delete = "DELETE FROM ".$table_delete." WHERE ".$where;
	mysql_query($query_delete, $link); 
	if(mysql_error() != "")
		echo "<p style='color:red;'>".'Ошибка удаления в таблице!'.$table_name.": ".mysql_error()."\n".$query_delete."\n"."</p>";
	else echo "<p style='color:blue;'>".'Удаление прошло успешно!'."</p>";
	/*ГОВНОКОД*/
	if( $_GET["data"]!='' ) require_once "show_links.php";
}

unset($process);
?>

