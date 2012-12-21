<<<<<<< HEAD
<?php
		defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
		$html = '';
		$headers = apache_request_headers();
		$DEFAULT_IMAGE_PATH = DS."images".DS."stories".DS."virtuemart".DS."category".DS."cars".DS;
		$RESIZED_IMAGE_PATH = DS."images".DS."stories".DS."virtuemart".DS."category".DS."resized".DS."cars".DS;
		$DEFAULT_WIDTH = 100;
		$DEFAULT_HEIGHT = 67;
		$RESIZED_IMAGE_SUFFIX = "_100x100";
		
		$UPLOAD_FILE_FIELD = 'file';
		$uploaddir = $_SERVER['DOCUMENT_ROOT'].$DEFAULT_IMAGE_PATH;
		$file_name = $_FILES['file']['name'];
		if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir.$file_name)) {
   			
			$resized_image = imagecreatetruecolor($DEFAULT_WIDTH, $DEFAULT_HEIGHT);
			$source_image = imagecreatefromjpeg($uploaddir.$file_name);
			if (!imagecopyresampled ($resized_image, $source_image, 0, 0, 0, 0, $DEFAULT_WIDTH, $DEFAULT_HEIGHT, imagesx($source_image), imagesy($source_image)))
				$html.="<p style='color:red;'>Изображение было загружено, но изменение размера не удалось: ".$uploaddir.$file_name." ".$_FILES[$UPLOAD_FILE_FIELD]['error']."</p>";
			$resized_file = $_SERVER['DOCUMENT_ROOT'].$RESIZED_IMAGE_PATH.substr($file_name, 0, strpos($file_name, ".")).$RESIZED_IMAGE_SUFFIX.substr($file_name, strpos($file_name, "."));
			if (!imagejpeg ($resized_image,$resized_file))
				$html.="<p style='color:red;'>Изображение было загружено, но сохранение уменьшенного изображения не удалось: ".$file_name." ".$_FILES[$UPLOAD_FILE_FIELD]['error']."</p>";	
			$html.="Файл успешно загружен!"."<br />";
		} else {
			$html.="<p style='color:red;'>Что-то пошло не так при загрузке файла: ".$file_name." ".$_FILES[$UPLOAD_FILE_FIELD]['error']."</p>";
			$upload_failure = true;
		}
		echo $html;


=======
<?php
		defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
		$html = '';
		$headers = apache_request_headers();
		$DEFAULT_IMAGE_PATH = DS."images".DS."stories".DS."virtuemart".DS."category".DS."cars".DS;
		$RESIZED_IMAGE_PATH = DS."images".DS."stories".DS."virtuemart".DS."category".DS."resized".DS."cars".DS;
		$DEFAULT_WIDTH = 100;
		$DEFAULT_HEIGHT = 67;
		$RESIZED_IMAGE_SUFFIX = "_100x100";
		
		$UPLOAD_FILE_FIELD = 'file';
		$uploaddir = $_SERVER['DOCUMENT_ROOT'].$DEFAULT_IMAGE_PATH;
		$file_name = $_FILES['file']['name'];
		if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir.$file_name)) {
   			
			$resized_image = imagecreatetruecolor($DEFAULT_WIDTH, $DEFAULT_HEIGHT);
			$source_image = imagecreatefromjpeg($uploaddir.$file_name);
			if (!imagecopyresampled ($resized_image, $source_image, 0, 0, 0, 0, $DEFAULT_WIDTH, $DEFAULT_HEIGHT, imagesx($source_image), imagesy($source_image)))
				$html.="<p style='color:red;'>Изображение было загружено, но изменение размера не удалось: ".$uploaddir.$file_name." ".$_FILES[$UPLOAD_FILE_FIELD]['error']."</p>";
			$resized_file = $_SERVER['DOCUMENT_ROOT'].$RESIZED_IMAGE_PATH.substr($file_name, 0, strpos($file_name, ".")).$RESIZED_IMAGE_SUFFIX.substr($file_name, strpos($file_name, "."));
			if (!imagejpeg ($resized_image,$resized_file))
				$html.="<p style='color:red;'>Изображение было загружено, но сохранение уменьшенного изображения не удалось: ".$file_name." ".$_FILES[$UPLOAD_FILE_FIELD]['error']."</p>";	
			$html.="Файл успешно загружен!"."<br />";
		} else {
			$html.="<p style='color:red;'>Что-то пошло не так при загрузке файла: ".$file_name." ".$_FILES[$UPLOAD_FILE_FIELD]['error']."</p>";
			$upload_failure = true;
		}
		echo $html;


>>>>>>> 1e1b309bbf9e27aa09f1eef63d7bfeed85150451
?>