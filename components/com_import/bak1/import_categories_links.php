<?php
/*требуется РЕФАКТОРИНГ*/
/*1. селекты разбить на методы или метод*/
/*2. добавление картинок сделать отдельной функцией*/
/*3. Человеческая разбивка на категории: категория верхнего уровня, например GETZ, далее подкатегории - название в таблице*/
	include($_SERVER['DOCUMENT_ROOT'].'/libraries/lib.php');
	include($_SERVER['DOCUMENT_ROOT'].'/libraries/lib_db.php');
	
	$process = new Dbprocess();
	importCategoryLinks($process);

	function importCategoryLinks($db){
		$html = '';
		$upload_failure = false;
		$UPLOAD_FILE_FIELD = "uploadedfilecatlinks";
		$TARGET_FILE_NAME = "category_links.csv";
		$uploaddir = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF'])."/"."upload"."/";
		if(strpos($_FILES[$UPLOAD_FILE_FIELD]['name'], ".csv")=== false){
			$html.=iconv('UTF-8', 'windows-1251', "<p style='color:red;'>Выберите файл в формате *.csv file!</p>");
			$upload_failure = true;
		}
		else{
			if (move_uploaded_file($_FILES[$UPLOAD_FILE_FIELD]['tmp_name'], $uploaddir.$_FILES[$UPLOAD_FILE_FIELD]['name'])) {
				rename($uploaddir.$_FILES[$UPLOAD_FILE_FIELD]['name'],$uploaddir.$TARGET_FILE_NAME);
    			$html.=iconv('UTF-8', 'windows-1251', "Файл успешно загружен! Импорт начинается...")."<br />";
				$file_name = $uploaddir.$_FILES[$UPLOAD_FILE_FIELD]['name'];
			} else {
				$html.=iconv('UTF-8', 'windows-1251', "<p style='color:red;'>Что-то пошло не так при загрузке файла: ").$_FILES['uploadedfile']['error']."</p>";
				$upload_failure = true;
			}
		}
		if(!$upload_failure){
			$html.="";
			$link = $db->connectToDb();
			$table_prefix = $db->getTablePrefix().$db->getVMTablePrefix();
			$category_links = new CategoriesLinks();
			$html.=$category_links->init($link,$table_prefix);
			//2. read csv file
			if (($handle = fopen($file_name, "r")) !== FALSE) {
				$html.="";
				$row = 1;
	   			while (($data = fgetcsv_file($handle, 1000, ";")) !== FALSE) {
					if($row > 1){
						if(trim($data[0]) != "" ){
							//основной импорт
							for($i = 0; $i < count($data);$i++){
								$dataUTF[$i] = iconv('windows-1251', 'UTF-8',$data[$i]);
							}
							$category_links->process($dataUTF);
						}
					}
					$row++;
				}
    		}
	   		fclose($handle);
		}
		$html.=iconv('UTF-8', 'windows-1251', "Импорт завершен!")."<br />";
		echo $html;
	}
	
	unset($process);
	
	class CategoriesLinks{
		private $category_link_id = 1;
		private $table_prefix = "";
		private $link;

		function init($l, $tp){
			
			$html = "";
			
			$this->link = $l;
			$this->table_prefix = $tp;
			
			$table_name = $this->table_prefix."category_links";
			$query_select = "SELECT MAX(category_links_id) FROM ".$table_name;
			$category_max_select = mysql_query($query_select, $this->link);
			if(mysql_error() != ""){
				$html.="<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."\n".$query_select."</p>";
			}else{
				$category_max = mysql_fetch_array($category_max_select, MYSQL_NUM);
				$this->category_link_id = $category_max[0] + 1;
			}
			
			return $html;
		}
		
		function process($data){
			
			$html = "";
			
			$name = trim(preg_replace('/\s+/', ' ', trim($data[0])));
			$name = str_replace ('\\', '', $name);

			//check if record exist
			$table_name = $this->table_prefix."category_links";
			$query_select = "select category_links_id from ".$table_name." where category_name='$name'";

			$result = mysql_query($query_select, $this->link);
			$rows_num = mysql_num_rows($result);

			//if record doesn't exist insert record in tables
			if ($rows_num == 0){
				//category_links_ID category_name parent_name synonym_name
				$query_insert = "insert into ".$table_name." values($this->category_link_id,'$name','$data[1]','$data[2]')";
				mysql_query($query_insert, $this->link);
				if(mysql_error() != ""){
					$html.="<p style='color:red;'>Error in insert to ".$table_name.":".mysql_error()."\n".$query_insert."</p>";
				}
			}
			
			$this->category_link_id = $this->category_link_id + 1;
			
			return $html;
    }
}
?>

