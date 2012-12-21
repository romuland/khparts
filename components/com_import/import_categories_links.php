<<<<<<< HEAD
<?php
	include($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'lib.php');
	include($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'lib_db.php');
	
	$process = new Dbprocess();
	$process->connectToDb();
	importCategoryLinks($process);

	function importCategoryLinks($db){
		$html = '';
		$upload_failure = false;
		$UPLOAD_FILE_FIELD = "uploadedfilecatlinks";
		$TARGET_FILE_NAME = "category_links.csv";
		$uploaddir = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF'])."/"."upload"."/";
		if(strpos($_FILES[$UPLOAD_FILE_FIELD]['name'], ".csv")=== false){
			$html.="<p style='color:red;'>Выберите файл в формате *.csv file!</p>";
			$upload_failure = true;
		}
		else{
			if (move_uploaded_file($_FILES[$UPLOAD_FILE_FIELD]['tmp_name'], $uploaddir.$_FILES[$UPLOAD_FILE_FIELD]['name'])) {
				rename($uploaddir.$_FILES[$UPLOAD_FILE_FIELD]['name'],$uploaddir.$TARGET_FILE_NAME);
    			$html.=iconv('UTF-8', 'windows-1251', "Файл успешно загружен! Импорт начинается...")."<br />";
				$file_name = $uploaddir.$_FILES[$UPLOAD_FILE_FIELD]['name'];
			} else {
				$html.="<p style='color:red;'>Что-то пошло не так при загрузке файла: ".$_FILES['uploadedfile']['error']."</p>";
				$upload_failure = true;
			}
		}
		if(!$upload_failure){
			$html.="";
			$table_prefix = $db->getTablePrefix().$db->getVMTablePrefix();
			$category_links = new CategoriesLinks();
			$html.=$category_links->init($db,$table_prefix);
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
							$html.=$category_links->process($dataUTF);
						}
					}
					$row++;
				}
    		}
	   		fclose($handle);
		}
		$html.="Импорт завершен!"."<br />";
		echo $html;
	}
	
	unset($process);
	
	class CategoriesLinks{
		private $category_link_id = 1;
		private $table_prefix = "";
		private $db;
		
		function init($d, $tp){
			
			$html = "";
			
			$this->db = $d;
			$this->table_prefix = $tp;
			
			$result_select = $this->db->select($this->table_prefix."category_links", "category_links_id", "", 'max');
			$html.=$result_select['html'];
			$this->category_link_id = $result_select['value'] + 1;
						
			return $html;
		}
		
		function process($data){
			
			$html = "";
			
			$name = trim(preg_replace('/\s+/', ' ', trim($data[0])));
			$name = str_replace ('\\', '', $name);

			//check if record exist
			$table_name = $this->table_prefix."category_links";
			$where = '';
			if ($data[1] != '') $where = " and parent_name='$data[1]'";
			$result_select = $this->db->select($table_name, "category_links_id", "category_name='$name'".$where, 'count');
			$html.=$result_select['html'];
			$rows_num = $result_select['value'];
						
			//if record doesn't exist insert record in tables
			if ($rows_num == 0){
				//category_links_ID category_name parent_name synonym_name
				$insert_values = "$this->category_link_id,'$name',";
				if ($data[1]=='') $insert_values .= "NULL,";
				else $insert_values .= "'$data[1]'," ;
				if ($data[2]=='') $insert_values .= "NULL";
				else $insert_values .= "'$data[2]'" ;
				$html.=$this->db->insert($table_name, $insert_values, "");
			}
			
			$this->category_link_id = $this->category_link_id + 1;
			
			return $html;
    }
}
=======
<?php
	include($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'lib.php');
	include($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'lib_db.php');
	
	$process = new Dbprocess();
	$process->connectToDb();
	importCategoryLinks($process);

	function importCategoryLinks($db){
		$html = '';
		$upload_failure = false;
		$UPLOAD_FILE_FIELD = "uploadedfilecatlinks";
		$TARGET_FILE_NAME = "category_links.csv";
		$uploaddir = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF'])."/"."upload"."/";
		if(strpos($_FILES[$UPLOAD_FILE_FIELD]['name'], ".csv")=== false){
			$html.="<p style='color:red;'>Выберите файл в формате *.csv file!</p>";
			$upload_failure = true;
		}
		else{
			if (move_uploaded_file($_FILES[$UPLOAD_FILE_FIELD]['tmp_name'], $uploaddir.$_FILES[$UPLOAD_FILE_FIELD]['name'])) {
				rename($uploaddir.$_FILES[$UPLOAD_FILE_FIELD]['name'],$uploaddir.$TARGET_FILE_NAME);
    			$html.=iconv('UTF-8', 'windows-1251', "Файл успешно загружен! Импорт начинается...")."<br />";
				$file_name = $uploaddir.$_FILES[$UPLOAD_FILE_FIELD]['name'];
			} else {
				$html.="<p style='color:red;'>Что-то пошло не так при загрузке файла: ".$_FILES['uploadedfile']['error']."</p>";
				$upload_failure = true;
			}
		}
		if(!$upload_failure){
			$html.="";
			$table_prefix = $db->getTablePrefix().$db->getVMTablePrefix();
			$category_links = new CategoriesLinks();
			$html.=$category_links->init($db,$table_prefix);
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
							$html.=$category_links->process($dataUTF);
						}
					}
					$row++;
				}
    		}
	   		fclose($handle);
		}
		$html.="Импорт завершен!"."<br />";
		echo $html;
	}
	
	unset($process);
	
	class CategoriesLinks{
		private $category_link_id = 1;
		private $table_prefix = "";
		private $db;
		
		function init($d, $tp){
			
			$html = "";
			
			$this->db = $d;
			$this->table_prefix = $tp;
			
			$result_select = $this->db->select($this->table_prefix."category_links", "category_links_id", "", 'max');
			$html.=$result_select['html'];
			$this->category_link_id = $result_select['value'] + 1;
						
			return $html;
		}
		
		function process($data){
			
			$html = "";
			
			$name = trim(preg_replace('/\s+/', ' ', trim($data[0])));
			$name = str_replace ('\\', '', $name);

			//check if record exist
			$table_name = $this->table_prefix."category_links";
			$where = '';
			if ($data[1] != '') $where = " and parent_name='$data[1]'";
			$result_select = $this->db->select($table_name, "category_links_id", "category_name='$name'".$where, 'count');
			$html.=$result_select['html'];
			$rows_num = $result_select['value'];
						
			//if record doesn't exist insert record in tables
			if ($rows_num == 0){
				//category_links_ID category_name parent_name synonym_name
				$insert_values = "$this->category_link_id,'$name',";
				if ($data[1]=='') $insert_values .= "NULL,";
				else $insert_values .= "'$data[1]'," ;
				if ($data[2]=='') $insert_values .= "NULL";
				else $insert_values .= "'$data[2]'" ;
				$html.=$this->db->insert($table_name, $insert_values, "");
			}
			
			$this->category_link_id = $this->category_link_id + 1;
			
			return $html;
    }
}
>>>>>>> 1e1b309bbf9e27aa09f1eef63d7bfeed85150451
?>