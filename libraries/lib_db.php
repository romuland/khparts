<?php
$NULL_DATE = '0000-00-00 00:00:00';

class Dbprocess{
	private $sitename = "";
	private $host = "";
	private $user = "";
	private $db = "";
	private $password = "";
	private $table_prefix = "";
	private $vm_table_prefix = "virtuemart_";
	private $image_dir = "images";
	private $image_ext = ".jpg";
	private $default_image = "hyn_kia.png";
	private $link;
	
	private $current_query = '';
	
	function __construct(){
		include_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'configuration.php');
		$jc = new JConfig();
		$this->sitename = $js->sitename;
		$this->host = $jc->host;
		$this->user = $jc->user;
		$this->password = $jc->password;
		$this->db = $jc->db;
		$this->table_prefix = $jc->dbprefix;
}
	
	function getTablePrefix(){
		return $this->table_prefix;
	}
	function getVMTablePrefix(){
		return $this->vm_table_prefix;
	}
	function connectToDb(){
		//1. connect to database
		$this->link = mysql_connect($this->host, $this->user, $this->password)
		or die("Could not connect: " . mysql_error());
		// Выбираем нашу базу данных
		mysql_select_db($this->db, $this->link);
		mysql_query("set names utf8;");
		return $this->link;
	}
	function __destruct(){
		if ($this->link && isset( self::$link )) mysql_close($this->link);
	}
	
	function select($table_name, $columns, $where, $whatReturned, $orderBy = ''){
		$columns_separator = ",";
		if($columns == '') $columns = "*";
		if($where == '') $where = 1;
		
		if ($whatReturned == "max")	{
			$query_select = "SELECT MAX($columns) FROM ".$table_name." where $where";
			$default_value = 1;
		}
		elseif ($whatReturned == "count"){
			$query_select = "SELECT COUNT($columns) FROM ".$table_name." where $where";
			$default_value = 0;
		}
		else 
			$query_select = "SELECT $columns FROM ".$table_name." where $where";

		if ($orderBy != '') $query_select.= " ORDER BY ".$orderBy." ASC";
		$this->current_query = $query_select;
		$select_result = mysql_query($this->current_query, $this->link);

		if(mysql_error() != ""){
			$result['html'] = "<p style='color:red;'>Error select from ".$table_name.":".mysql_error()."<br />".$this->current_query."</p>";
			$result['value'] = $default_value;
		}else{
			$result['html'] = '';
			if ($whatReturned == 'std'){
				$rows_num = mysql_num_rows($select_result);
				$result['rows'] = $rows_num;
				if ($rows_num >= 1){
					$r = 0;
					while($row=mysql_fetch_array($select_result, MYSQL_ASSOC)){
						$select_array[$r] = $row;
						$r++;
					}
				}
				//если в массиве один элемент (нет разделителя), то принимаем возвращаемое значение как значение этого столбца
				if ($rows_num >= 1){
					if($columns == "*" || strpos($columns, $columns_separator) !== FALSE) $result['value'] = $select_array;
					else {
						foreach($select_array as $value){
							$result['value'][] = $value[$columns];	
						}
					}
				}
			}
			else{
				$select_array = mysql_fetch_array($select_result, MYSQL_NUM);
				$result['value'] = $select_array[0] + $default_value;
			}
		}
		return $result;		
	}
	
	function insert($table_name, $values_set){
		$query_insert = "insert into ".$table_name." values($values_set)";
		$this->current_query = $query_insert ;
		mysql_query($this->current_query, $this->link);
		if(mysql_error() != ""){
			return "<p style='color:red;'>Error in insert to ".$table_name.": ".mysql_error()."<br />".$this->current_query."</p>";
		}
		else return "";
	}
	
	function get_image($name){
		$dir = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$this->image_dir.DIRECTORY_SEPARATOR;
		foreach (glob($dir."*".$this->image_ext) as $filename) {
			$file = substr($filename, strrpos($filename, DIRECTORY_SEPARATOR) + 1);
			$temp = substr($temp, 0, strpos($temp, "."));
    		if(strpos($name,$temp) > 0) {
				$image = $filename; 
				break;
			}
		}
		if ($image == ""){
			$image = $dir.$this->default_image;
		}	
		return $image;
	}
	
	function query_toString(){
		return $this->current_query;
	}
}
?>