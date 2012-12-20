<?php
	include($_SERVER['DOCUMENT_ROOT'].'/libraries/lib_db.php');
	
	$process = new Dbprocess();
	$link = $process->connectToDb();
	$table_prefix = $process->getTablePrefix().$process->getVMTablePrefix();
	
	$searchValue = trim($_GET['search']);
	$searchValue = strip_tags($searchValue);               // Удаляем HTML и PHP теги
	$searchValue = mysql_real_escape_string($searchValue); // Экранируем специальные символы
	
	$table_name = $table_prefix."import";
	$field_name = "product_sku";
	
	$query_select = 'SELECT * FROM '.$table_name.' WHERE '.$field_name.' LIKE "%'.addslashes($searchValue).'%" group by '.$field_name;
	$result = mysql_query($query_select);
	if(mysql_error() != ""){
		echo "<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
	}else{
		/*HTML Version*/
		$count = 0;
		while ($row = mysql_fetch_array($result)){
		if($count==0) $html.="<div id='firstDiv' className='optionDiv' onmouseover='rollOverActiveDiv(this,false)' onclick='selectDiv(this)'>".$row[$field_name]."</div>";
		else $html.="<div className='optionDiv' onmouseover='rollOverActiveDiv(this,false)' onclick='selectDiv(this)'>".$row[$field_name]."</div>";
		$count++;}
		}
		/* JSON Version
		$html = '{"row":[';
			while ($row = mysql_fetch_array($result))
		    	//$html.='"row":{"'.$field_name.'":'.'"'.$row[$field_name].'"'. '},';
				$html.='{"'.$field_name.'":'.'"'.$row[$field_name].'"'. '},';
		}
		$html=substr($html, 0, strlen($html) - 1);
		$html.=']}';*/
		echo $html;
?>
