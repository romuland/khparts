<?php 
	include($_SERVER['DOCUMENT_ROOT'].'/libraries/lib_db.php');
	include "delete_all.php";
 	include "import.php";
	include "importopt.php";
	
	if(isset($_POST['buttonopt'])){
   	  importopt();
	} else{
	$process = new Dbprocess();
	
	if(isset($_POST['buttonimport'])){
   	  import($process);
	}

	if(isset($_POST['buttondelete'])){
   	  deleteAll($process);
	 
	} 
	 unset($process);
	 }
?>