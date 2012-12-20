<?php
importopt();
function importopt(){	
	
	$UPLOAD_FILE_FIELD = "uploadedfile";
	$upload_failure = false;
	$uploaddir = $_SERVER['DOCUMENT_ROOT']."/"."Files"."/";
	$TARGET_FILE_NAME = 'Opt.xls';
	/*if(strpos(strtolower($_FILES[$upload_file_field]['name']), "opt.xls")=== false){
		echo "<p><a href='javascript:history.back();'><--- ".'Back'."</a></p>";
		echo "<p style='color:red;'>Choose file opt.xls !</p>";
		$upload_failure = true;
	}
	else{*/
		if (move_uploaded_file($_FILES[$UPLOAD_FILE_FIELD]['tmp_name'], $uploaddir.$_FILES[$UPLOAD_FILE_FIELD]['name'])) {
			rename($uploaddir.$_FILES[$UPLOAD_FILE_FIELD]['name'],$uploaddir.$TARGET_FILE_NAME);
			echo "<p style='color:blue;'>". 'Файл успешно загружен!'."</p>";
			$file_name = $uploaddir.$_FILES[$UPLOAD_FILE_FIELD]['name'];
		} else {
			echo "<p style='color:red;'>". 'Что-то пошло не так при загрузке файла: '.$_FILES[$UPLOAD_FILE_FIELD]['error']."</p>";
			$upload_failure = true;
		}
	//}
}
?>