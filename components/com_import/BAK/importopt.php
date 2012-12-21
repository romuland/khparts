<?php
function importopt(){	
	
	
	$upload_file_field = "uploadedfile";
	$upload_failure = false;
	$uploaddir = $_SERVER['DOCUMENT_ROOT']."/"."Files"."/";
	/*if(strpos(strtolower($_FILES[$upload_file_field]['name']), "opt.xls")=== false){
		echo "<p><a href='javascript:history.back();'><--- ".'Back'."</a></p>";
		echo "<p style='color:red;'>Choose file opt.xls !</p>";
		$upload_failure = true;
	}
	else{*/
		if (move_uploaded_file($_FILES[$upload_file_field]['tmp_name'], $uploaddir.$_FILES[$upload_file_field]['name'])) {
			rename($uploaddir.$_FILES[$upload_file_field]['name'],$uploaddir.'Opt.xls');
			echo "<p><a href='/'><--- ".'to main page'."</a></p>";
    		echo "File was uploaded successfully!"."<br />";
			$file_name = $uploaddir.$_FILES[$upload_file_field]['name'];
		} else {
			echo "<p><a href='javascript:history.back();'><--- ".'Back'."</a></p>";
			echo "<p style='color:red;'>Something wrong in upload file: ".$_FILES['uploadedfile']['error']."</p>";
			$upload_failure = true;
		}
	//}
}
?>