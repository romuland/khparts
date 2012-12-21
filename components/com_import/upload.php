<?php
	$upload_file_field = "uploadedfile";
	$upload_failure = false;
	$uploaddir = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF'])."/"."upload"."/";
	if(strpos($_FILES[$upload_file_field]['name'], ".csv")=== false){
		echo iconv('UTF-8', 'windows-1251', "<p style='color:red;'>Выберите файл в формате *.csv file!</p>");
		$upload_failure = true;
	}
	else{
		if (move_uploaded_file($_FILES[$upload_file_field]['tmp_name'], $uploaddir.$_FILES[$upload_file_field]['name'])) {
    		echo iconv('UTF-8', 'windows-1251', "File was uploaded successfully! Import is begining...")."<br />";
			$file_name = $uploaddir.$_FILES[$upload_file_field]['name'];
		} else {
			echo iconv('UTF-8', 'windows-1251', "<p style='color:red;'>Что-то пошло не так при загрузке файла: ").$_FILES['uploadedfile']['error']."</p>";
			$upload_failure = true;
		}
	}
   
   sleep(1);
?>


