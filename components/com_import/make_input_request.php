<?php
	include_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'lib_db.php');
	
	$process = new Dbprocess();
	$process->connectToDb();
	makeInputQuery($process);
	unset($process);
	
	function makeInputQuery($db){
			
		$html = "";
		$vals = explode (',', $_GET['valuesSet']);
		foreach($vals as $value){
			if($value == "null")
				$newvals[] = $value;
			elseif(is_numeric($value))	
				$newvals[] = $value;
			else
				$newvals[] = "'".$value."'";	
		}
		$temp = $db->insert($_GET['tableName'], implode(",", $newvals)); 
		if($temp !='')	echo $temp;		
		else "<p style='color:blue;'>".'Новое значение добавлено!'."</p>";
			/*ГОВНОКОД*/
	 require_once "show_links.php";		
	 show_links();
	}

?>