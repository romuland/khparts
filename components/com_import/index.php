<?php 
//header("Content-Type: content=text/html; charset=utf-8"); 
//mb_internal_encoding("UTF-8");
	include($_SERVER['DOCUMENT_ROOT'].'/libraries/lib_db.php');
	include "delete_all.php";
 	include "import.php";
	include "importopt.php";
	
	//$_REQUEST Version 	
	if(isset($_REQUEST['operation'])){
		if($_REQUEST['operation']=='buttonopt'){
	 		importopt();
		} else{
			//$process = new Dbprocess();
			echo $_REQUEST['operation'];
			if($_REQUEST['operation']=='buttonimport'){
		   	  import($process);
			}
			if($_REQUEST['operation']=='buttondelete') {
			//  	  deleteAll($process);
			} 
		 unset($process);
		 }
	}
		//POST Form version
	else{
		echo "1";
		if(isset($_POST['buttonopt'])){
			importopt();
		} else{
		///	echo 'isset(_POST[isImport])'.isset($_POST['buttonimport']);
			$process = new Dbprocess();
		//	print_r($_POST);
		//	 import($process);
			if(isset($_POST['buttonimport'])){
		   	  import($process);
			}
			if(isset($_POST['buttondelete'])){
			  	  deleteAll($process);
			} 
		 unset($process);
		 }
	}
?>