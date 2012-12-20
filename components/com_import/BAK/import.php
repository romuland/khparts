<?php
	include($_SERVER['DOCUMENT_ROOT'].'/libraries/lib.php');
	
	include "import_products.php";
	include "import_categories.php";
	include "import_manufacturers.php";
	
		/*
	Доработки:
	1. Сделать интерфейс (компонент)
	2. Сделать возможным апдейт где необходимо
	3. избавиться от рекорд_ид
	4. загрузка картинок и отображение уже загруженных картинок
	*/
function import($db){	
	echo "<p><a href='javascript:history.back();'><--- ".iconv('UTF-8', 'windows-1251', 'Back')."</a></p>";
	
	ini_set("max_execution_time", "300"); //5 мин
	
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
		if($makeImportCategories) $category_import->init($link,$table_prefix);
		if($makeImportManufacturers) $manufacturer_import->init($link,$table_prefix);
		if($makeImportProducts) $product_import->init($link,$table_prefix);

		$row = 1;//счетчик
		if (isset($_POST['wh'])) $warehouse = $_POST['wh'];

		if (isset($_POST['maxrow'])) {
			if ($_POST['maxrow'] > 0) $max_row = intval($_POST['maxrow']);
			else $max_row = 100000;//максимальное количество строк для импорта
		}else $max_row = 100000;//максимальное количество строк для импорта

		if (isset($_POST['startrow'])) {
			if ($_POST['startrow'] > 0) $init_row = intval($_POST['startrow']);
			else $init_row = 2;//пропускаем все строки до данной строки (начинаем прием с этой строки)
		}else $init_row = 2;//пропускаем все строки до данной строки (начинаем прием с этой строки)
	
		//2. read csv file
		if (($handle = fopen($file_name, "r")) !== FALSE) {

   			while (($data = fgetcsv_file($handle, 1000, ";")) !== FALSE) {

				if($row >= $init_row && ($row < $max_row)){

					if(trim($data[0]) != "" ){
						//основной импорт
	
						for($i = 0; $i < count($data);$i++){
							$dataUTF[$i] = iconv('windows-1251', 'UTF-8',$data[$i]);
						}
						if($makeImportCategories) $category_import->process($dataUTF);
						if($makeImportManufacturers) $manufacturer_import->process($dataUTF);

						if($makeImportProducts) $product_import->process($dataUTF, $category_import->getCategoryId(), $category_import->getParentCategoryId, $manufacturer_import->getManufacturerId(), $warehouse);
					}
				}
    	    	$row++;
			}
    	}
	    fclose($handle);
		echo iconv('UTF-8', 'windows-1251', "Import End!");
	}
}
?>