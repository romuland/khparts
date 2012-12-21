<?php
	include($_SERVER['DOCUMENT_ROOT'].'/libraries/lib.php');
	include($_SERVER['DOCUMENT_ROOT'].'/libraries/lib_db.php');
		
	include "import_products.php";
	include "import_categories.php";
	include "import_manufacturers.php";
	
		/*
	Доработки:
	4. загрузка картинок и отображение уже загруженных картинок
	*/
	
	$process = new Dbprocess();
		//	print_r($_POST);
	import($process);
		
	
function import($db){	

	$IMPORT_STEP = 100; //переменная, определяющая сколько будет обработано строк за 1 прогон
	
	//ini_set("max_execution_time", "300"); //5 мин
	
	$html = ""; //html который возвращаем клиенту
	echo "import";
	//ключевые параметры, обозначают текущую строку в обработке, если 1, то еще не было загрузки
	$file_name = "";
	if (isset($_POST['filename'])) 
		$file_name = stripslashes($_POST['filename']);
	
	$upload_failure = false;
	
	//загрузку файла и подсчет количества строк делаем 1 раз при первой загрузке
	if($file_name == "") {
				
		$upload_file_field = "uploadedfile";
		$uploaddir = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF'])."/"."upload"."/";
		if(strpos($_FILES[$upload_file_field]['name'], ".csv")=== false){
			$html.=iconv('UTF-8', 'windows-1251', "<p style='color:red;'>Выберите файл в формате *.csv file!</p>");
			$upload_failure = true;
		}
		else{
			if (move_uploaded_file($_FILES[$upload_file_field]['tmp_name'], $uploaddir.$_FILES[$upload_file_field]['name'])) {
    			$html.=iconv('UTF-8', 'windows-1251', "File was uploaded successfully! Import is begining...")."<br />";
				$file_name = $uploaddir.$_FILES[$upload_file_field]['name'];
				$rowCount = count(file($file_name));
			} else {
				$html.=iconv('UTF-8', 'windows-1251', "<p style='color:red;'>Что-то пошло не так при загрузке файла: ").$_FILES['uploadedfile']['error']."</p>";
				$upload_failure = true;
			}
		}
	}
	
	if(!$upload_failure){
		
		$makeImportCategories = false;
		$makeImportManufacturers = false;
		$makeImportProducts = false;
		//Комментируем блок, если это необходимо	
		$makeImportCategories = true;
		$makeImportManufacturers = true;
		$makeImportProducts = true;
	
		//подключение к БД
		$link = $db->connectToDb();
		$table_prefix = $db->getTablePrefix().$db->getVMTablePrefix();

		if($makeImportCategories) $category_import = new Categories();
		if($makeImportManufacturers) $manufacturer_import = new Manufacturers();
		if($makeImportProducts) $product_import = new Products();
		//инициализируем начальные номера основных записей таблиц
		if($makeImportCategories) $html.=$category_import->init($link,$table_prefix);
		if($makeImportManufacturers) $html.=$manufacturer_import->init($link,$table_prefix);
		if($makeImportProducts) $html.=$product_import->init($link,$table_prefix);

		
		//принимаени POST переменные
		
			//ключевые параметры, обозначают текущую строку в обработке, если 1, то еще не было загрузки
		if (isset($_POST['currow'])) $currentRow = intval($_POST['currow']);
		else  $currentRow = 1;
		
		$row = 1;//счетчик
		
		if (isset($_POST['wh'])) $warehouse = $_POST['wh'];

		if (isset($_POST['maxrow'])) {
			if ($_POST['maxrow'] > 0) $max_row = intval($_POST['maxrow']);
			else $max_row = 100000;//максимальное количество строк для импорта
		}else $max_row = 100000;//максимальное количество строк для импорта

		if (isset($_POST['startrow'])) {
			if (intval($_POST['startrow']) > 0) $init_row = intval($_POST['startrow']);
			else $init_row = 2;//пропускаем все строки до данной строки (начинаем прием с этой строки)
		}else $init_row = 2;//пропускаем все строки до данной строки (начинаем прием с этой строки)

		//если начальная строка пользователя меньше текущей строки обработки, то должны обрабатывать с текущей строки
		if($init_row < $currentRow) $init_row = $currentRow;
		//2. read csv file
		if (($handle = fopen($file_name, "r")) !== FALSE) {

   			while (($data = fgetcsv_file($handle, 1000, ";")) !== FALSE) {

				if($row >= $init_row && ($row < $max_row)){

					if(trim($data[0]) != "" ){
						//основной импорт
	
							for($i = 0; $i < count($data);$i++){
							$dataUTF[$i] = iconv('windows-1251', 'UTF-8',$data[$i]);
						}
						if($makeImportCategories) $html.=$category_import->process($dataUTF);
						if($makeImportManufacturers) $html.=$manufacturer_import->process($dataUTF);

						if($makeImportProducts) $html.=$product_import->process($dataUTF, $category_import->getCategoryId(), $category_import->getParentCategoryId, $manufacturer_import->getManufacturerId(), $warehouse);
						
					}
				}
    	    	$row++;
				if($row > $IMPORT_STEP + $currentRow) {
					echo '{"currow":"'.$row.'", "rowcount":"'.$rowCount.'", "filename":"'.addslashes($file_name).'", "state":"process", "log":"'.$html.'"}';
					flush();
					fclose($handle);
					return;
				}
			}
    	}
	    fclose($handle);
		echo '{"currow":"'.$row.'", "rowcount":"'.$rowCount.'", "filename":"'.addslashes($file_name).'", "state":"complete", "log":"'.$html.'"}';
	}
}
		 unset($process);
?>