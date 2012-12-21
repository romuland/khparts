<<<<<<< HEAD
<?php 
	include_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'lib_db.php');
	
	/*ГОВНОКОД*/
	show_links();
	
	function show_links(){
		$NEW_VALUE_FORM = 'catlinksnew';
		if(isset($_GET['result'])) $result_field = $_GET['result'];
		else $result_field = "tablelinks";
		
		$options = array();
		$options["border"] = "1";
		$options["cellpadding"] = "3";
		$options["bgcolor"] = "#CCCCCC";

		$colNum = 'TitleNum';
	
		$colName = 'TitleName';

		$colParent = 'TitleParent';
	
		$colSyn = 'TitleSynonym';
		
		$colDel = 'Delete';

		
	//	$name = 'TitleDesc';
	//	$grid->addColumn($name);
	//	$grid->setRowCell($name, 'Описание');


		$db = new Dbprocess();
		$db->connectToDb();
		$table_prefix = $db->getTablePrefix();
		
		$grid = new JGrid($options);
		$grid->addRow($options = array());

		$grid->addColumn($colNum);
		$grid->setRowCell($colNum, 'ID');
	
		$grid->addColumn($colName);
		$grid->setRowCell($colName, 'Наименование');

		$grid->addColumn($colParent);
		$grid->setRowCell($colParent, 'Родитель');
	
		$grid->addColumn($colSyn);
		$grid->setRowCell($colSyn, 'Синоним');
		
		$grid->addColumn($colDel);
		$grid->setRowCell($colDel, 'Добавить');
		
		$grid->addRow($options = array());
		$max_result = $db->select($table_prefix."virtuemart_category_links", "category_links_ID", "", 'max');
		//include_once("fb.php");
		//fb($max_result, "max_result");
		$html.=$max_result['html'];
		$max = $max_result['value'] + 1;
		$grid->setRowCell($colNum, "<input name='category_links_ID' id='category_links_ID' value='".$max."' type='input' />");
		$grid->setRowCell($colName, "<input name='category_name' value='' type='input' />");

		$grid->setRowCell($colParent, "<input name='parent_name' value='' type='input' />");
		$grid->setRowCell($colSyn, "<input name='synonym_name' value='' type='input' />");

		$grid->setRowCell($colDel, '<a onclick="return onClickAjax_input(\''.$NEW_VALUE_FORM.'\', \''.$table_prefix."virtuemart_category_links".'\',\''."/components/com_import/make_input_request.php".'\', \''.$result_field.'\')"" href="">добавить</a>');	

		echo "<form name='".$NEW_VALUE_FORM."' id='".$NEW_VALUE_FORM."'>";
		echo "<h4>Введите новое значение:</h4>";
		echo $grid->toString();
		
		echo "</form>";
		$options = array();
		$options["border"] = "1";
		$options["cellpadding"] = "7";
		$grid = new JGrid($options);
		$grid->addRow($options = array());
		$grid->addColumn($colNum);
		$grid->setRowCell($colNum, 'ID');
	
		$grid->addColumn($colName);
		$grid->setRowCell($colName, 'Наименование');

		$grid->addColumn($colParent);
		$grid->setRowCell($colParent, 'Родитель');
	
		$grid->addColumn($colSyn);
		$grid->setRowCell($colSyn, 'Синоним');
		
		$grid->addColumn($colDel);
		$grid->setRowCell($colDel, 'Удалить');		
		$where = "parent_name IS NULL";
		$result = $db->select($table_prefix."virtuemart_category_links", "*", "$where", 'std', "'category_name'");


		
		$i = 1;
		if ($result['rows'] > 0) {
			foreach($result["value"] as $row){
				$grid->addRow($options = array());
				
				$grid->setRowCell($colNum, $row["category_links_ID"]);
				$grid->setRowCell($colName, $row["category_name"]);	
				$grid->setRowCell($colParent, $row["parent_name"]);
				$grid->setRowCell($colSyn, $row["synonym_name"]);				
				$grid->setRowCell($colDel, '');	
				
				$where = "parent_name='".$row['category_name']."'";
				$result_childs = $db->select($table_prefix."virtuemart_category_links", "*", "$where", 'std', "'category_name'");
				if ($result_childs['rows'] > 0) {
					foreach($result_childs["value"] as $row_childs){
						$grid->addRow($options = array());
				
						$grid->setRowCell($colNum, $row_childs["category_links_ID"]);
						$grid->setRowCell($colName, $row_childs["category_name"]);	
						$grid->setRowCell($colParent, $row_childs["parent_name"]);
						$grid->setRowCell($colSyn, $row_childs["synonym_name"]);
						$query = "category_links_ID = ".$row_childs["category_links_ID"];
						$grid->setRowCell($colDel, '<a onclick="return onClickAjax('."'".$query."'".', '."'"."/components/com_import/delete_links.php"."'".', '."'".$result_field."'".')"" href="">удалить</a>');	
				
					}
				}
				$i++;
			}
		}
		echo "<h4>Текущая таблица:</h4>";								
		echo $grid->toString();
	}
class JGrid
{
	/**
	 * Array of columns
	 * @var array
	 */
	protected $columns = array();

	/**
	 * Current active row
	 * @var int
	 */
	protected $activeRow = 0;

	/**
	 * Rows of the table (including header and footer rows)
	 * @var array
	 */
	protected $rows = array();

	/**
	 * Header and Footer row-IDs
	 * @var array
	 */
	protected $specialRows = array('header' => array(), 'footer' => array());

	/**
	 * Associative array of attributes for the table-tag
	 * @var array
	 */
	protected $options;

	/**
	 * Constructor for a JGrid object
	 *
	 * @param   array  $options  Associative array of attributes for the table-tag
	 *
	 */
	public function __construct($options = array())
	{
		$this->setTableOptions($options, true);
	}

	/**
	 * Magic function to render this object as a table.
	 *
	 * @return  string
	 *
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Method to set the attributes for a table-tag
	 *
	 * @param   array  $options  Associative array of attributes for the table-tag
	 * @param   bool   $replace  Replace possibly existing attributes
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function setTableOptions($options = array(), $replace = false)
	{
		if ($replace)
		{
			$this->options = $options;
		}
		else
		{
			$this->options = array_merge($this->options, $options);
		}
		return $this;
	}

	/**
	 * Get the Attributes of the current table
	 *
	 * @return  array Associative array of attributes
	 *
	 */
	public function getTableOptions()
	{
		return $this->options;
	}

	/**
	 * Add new column name to process
	 *
	 * @param   string  $name  Internal column name
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function addColumn($name)
	{
		$this->columns[] = $name;

		return $this;
	}

	/**
	 * Returns the list of internal columns
	 *
	 * @return  array List of internal columns
	 *
	 */
	public function getColumns()
	{
		return $this->columns;
	}

	/**
	 * Delete column by name
	 *
	 * @param   string  $name  Name of the column to be deleted
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function deleteColumn($name)
	{
		$index = array_search($name, $this->columns);
		if ($index !== false)
		{
			unset($this->columns[$index]);
			$this->columns = array_values($this->columns);
		}

		return $this;
	}

	/**
	 * Method to set a whole range of columns at once
	 * This can be used to re-order the columns, too
	 *
	 * @param   array  $columns  List of internal column names
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function setColumns($columns)
	{
		$this->columns = array_values($columns);

		return $this;
	}

	/**
	 * Adds a row to the table and sets the currently
	 * active row to the new row
	 *
	 * @param   array  $options  Associative array of attributes for the row
	 * @param   int    $special  1 for a new row in the header, 2 for a new row in the footer
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function addRow($options = array(), $special = false)
	{
		$this->rows[]['_row'] = $options;
		$this->activeRow = count($this->rows) - 1;
		if ($special)
		{
			if ($special === 1)
			{
				$this->specialRows['header'][] = $this->activeRow;
			}
			else
			{
				$this->specialRows['footer'][] = $this->activeRow;
			}
		}

		return $this;
	}

	/**
	 * Method to get the attributes of the currently active row
	 *
	 * @return array Associative array of attributes
	 *
	 */
	public function getRowOptions()
	{
		return $this->rows[$this->activeRow]['_row'];
	}

	/**
	 * Method to set the attributes of the currently active row
	 *
	 * @param   array  $options  Associative array of attributes
	 *
	 * @return JGrid This object for chaining
	 *
	 */
	public function setRowOptions($options)
	{
		$this->rows[$this->activeRow]['_row'] = $options;

		return $this;
	}

	/**
	 * Get the currently active row ID
	 *
	 * @return  int ID of the currently active row
	 *
	 */
	public function getActiveRow()
	{
		return $this->activeRow;
	}

	/**
	 * Set the currently active row
	 *
	 * @param   int  $id  ID of the row to be set to current
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function setActiveRow($id)
	{
		$this->activeRow = (int) $id;
		return $this;
	}

	/**
	 * Set cell content for a specific column for the
	 * currently active row
	 *
	 * @param   string  $name     Name of the column
	 * @param   string  $content  Content for the cell
	 * @param   array   $option   Associative array of attributes for the td-element
	 * @param   bool    $replace  If false, the content is appended to the current content of the cell
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function setRowCell($name, $content, $option = array(), $replace = true)
	{
		if ($replace || !isset($this->rows[$this->activeRow][$name]))
		{
			$cell = new stdClass;
			$cell->options = $option;
			$cell->content = $content;
			$this->rows[$this->activeRow][$name] = $cell;
		}
		else
		{
			$this->rows[$this->activeRow][$name]->content .= $content;
			$this->rows[$this->activeRow][$name]->options = $option;
		}

		return $this;
	}

	/**
	 * Get all data for a row
	 *
	 * @param   int  $id  ID of the row to return
	 *
	 * @return  array Array of columns of a table row
	 *
	 */
	public function getRow($id = false)
	{
		if ($id === false)
		{
			$id = $this->activeRow;
		}

		if (isset($this->rows[(int) $id]))
		{
			return $this->rows[(int) $id];
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get the IDs of all rows in the table
	 *
	 * @param   int  $special  false for the standard rows, 1 for the header rows, 2 for the footer rows
	 *
	 * @return  array Array of IDs
	 *
	 */
	public function getRows($special = false)
	{
		if ($special)
		{
			if ($special === 1)
			{
				return $this->specialRows['header'];
			}
			else
			{
				return $this->specialRows['footer'];
			}
		}
		return array_diff(array_keys($this->rows), array_merge($this->specialRows['header'], $this->specialRows['footer']));
	}

	/**
	 * Delete a row from the object
	 *
	 * @param   int  $id  ID of the row to be deleted
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function deleteRow($id)
	{
		unset($this->rows[$id]);

		if (in_array($id, $this->specialRows['header']))
		{
			unset($this->specialRows['header'][array_search($id, $this->specialRows['header'])]);
		}

		if (in_array($id, $this->specialRows['footer']))
		{
			unset($this->specialRows['footer'][array_search($id, $this->specialRows['footer'])]);
		}

		if ($this->activeRow == $id)
		{
			end($this->rows);
			$this->activeRow = key($this->rows);
		}

		return $this;
	}

	/**
	 * Render the HTML table
	 *
	 * @return  string The rendered HTML table
	 *
	 */
	public function toString()
	{
		$output = array();
		$output[] = '<table' . $this->renderAttributes($this->getTableOptions()) . '>';

		if (count($this->specialRows['header']))
		{
			$output[] = $this->renderArea($this->specialRows['header'], 'thead', 'th');
		}

		if (count($this->specialRows['footer']))
		{
			$output[] = $this->renderArea($this->specialRows['footer'], 'tfoot');
		}

		$ids = array_diff(array_keys($this->rows), array_merge($this->specialRows['header'], $this->specialRows['footer']));
		if (count($ids))
		{
			$output[] = $this->renderArea($ids);
		}

		$output[] = '</table>';
		return implode('', $output);
	}

	/**
	 * Render an area of the table
	 *
	 * @param   array   $ids   IDs of the rows to render
	 * @param   string  $area  Name of the area to render. Valid: tbody, tfoot, thead
	 * @param   string  $cell  Name of the cell to render. Valid: td, th
	 *
	 * @return string The rendered table area
	 *
	 */
	protected function renderArea($ids, $area = 'tbody', $cell = 'td')
	{
		$output = array();
		$output[] = '<' . $area . ">\n";
		foreach ($ids as $id)
		{
			$output[] = "\t<tr" . $this->renderAttributes($this->rows[$id]['_row']) . ">\n";
			foreach ($this->getColumns() as $name)
			{
				if (isset($this->rows[$id][$name]))
				{
					$column = $this->rows[$id][$name];
					$output[] = "\t\t<" . $cell . $this->renderAttributes($column->options) . '>' . $column->content . '</' . $cell . ">\n";
				}
			}

			$output[] = "\t</tr>\n";
		}
		$output[] = '</' . $area . '>';

		return implode('', $output);
	}

	/**
	 * Renders an HTML attribute from an associative array
	 *
	 * @param   array  $attributes  Associative array of attributes
	 *
	 * @return  string The HTML attribute string
	 *
	 */
	protected function renderAttributes($attributes)
	{
		if (count((array) $attributes) == 0)
		{
			return '';
		}
		$return = array();
		foreach ($attributes as $key => $option)
		{
			$return[] = $key . '="' . $option . '"';
		}
		return ' ' . implode(' ', $return);
	}
}

=======
<?php 
	include_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'lib_db.php');
	
	/*ГОВНОКОД*/
	show_links();
	
	function show_links(){
		$NEW_VALUE_FORM = 'catlinksnew';
		if(isset($_GET['result'])) $result_field = $_GET['result'];
		else $result_field = "tablelinks";
		
		$options = array();
		$options["border"] = "1";
		$options["cellpadding"] = "3";
		$options["bgcolor"] = "#CCCCCC";

		$colNum = 'TitleNum';
	
		$colName = 'TitleName';

		$colParent = 'TitleParent';
	
		$colSyn = 'TitleSynonym';
		
		$colDel = 'Delete';

		
	//	$name = 'TitleDesc';
	//	$grid->addColumn($name);
	//	$grid->setRowCell($name, 'Описание');


		$db = new Dbprocess();
		$db->connectToDb();
		$table_prefix = $db->getTablePrefix();
		
		$grid = new JGrid($options);
		$grid->addRow($options = array());

		$grid->addColumn($colNum);
		$grid->setRowCell($colNum, 'ID');
	
		$grid->addColumn($colName);
		$grid->setRowCell($colName, 'Наименование');

		$grid->addColumn($colParent);
		$grid->setRowCell($colParent, 'Родитель');
	
		$grid->addColumn($colSyn);
		$grid->setRowCell($colSyn, 'Синоним');
		
		$grid->addColumn($colDel);
		$grid->setRowCell($colDel, 'Добавить');
		
		$grid->addRow($options = array());
		$max_result = $db->select($table_prefix."virtuemart_category_links", "category_links_ID", "", 'max');
		//include_once("fb.php");
		//fb($max_result, "max_result");
		$html.=$max_result['html'];
		$max = $max_result['value'] + 1;
		$grid->setRowCell($colNum, "<input name='category_links_ID' id='category_links_ID' value='".$max."' type='input' />");
		$grid->setRowCell($colName, "<input name='category_name' value='' type='input' />");

		$grid->setRowCell($colParent, "<input name='parent_name' value='' type='input' />");
		$grid->setRowCell($colSyn, "<input name='synonym_name' value='' type='input' />");

		$grid->setRowCell($colDel, '<a onclick="return onClickAjax_input(\''.$NEW_VALUE_FORM.'\', \''.$table_prefix."virtuemart_category_links".'\',\''."/components/com_import/make_input_request.php".'\', \''.$result_field.'\')"" href="">добавить</a>');	

		echo "<form name='".$NEW_VALUE_FORM."' id='".$NEW_VALUE_FORM."'>";
		echo "<h4>Введите новое значение:</h4>";
		echo $grid->toString();
		
		echo "</form>";
		$options = array();
		$options["border"] = "1";
		$options["cellpadding"] = "7";
		$grid = new JGrid($options);
		$grid->addRow($options = array());
		$grid->addColumn($colNum);
		$grid->setRowCell($colNum, 'ID');
	
		$grid->addColumn($colName);
		$grid->setRowCell($colName, 'Наименование');

		$grid->addColumn($colParent);
		$grid->setRowCell($colParent, 'Родитель');
	
		$grid->addColumn($colSyn);
		$grid->setRowCell($colSyn, 'Синоним');
		
		$grid->addColumn($colDel);
		$grid->setRowCell($colDel, 'Удалить');		
		$where = "parent_name IS NULL";
		$result = $db->select($table_prefix."virtuemart_category_links", "*", "$where", 'std', "'category_name'");


		
		$i = 1;
		if ($result['rows'] > 0) {
			foreach($result["value"] as $row){
				$grid->addRow($options = array());
				
				$grid->setRowCell($colNum, $row["category_links_ID"]);
				$grid->setRowCell($colName, $row["category_name"]);	
				$grid->setRowCell($colParent, $row["parent_name"]);
				$grid->setRowCell($colSyn, $row["synonym_name"]);				
				$grid->setRowCell($colDel, '');	
				
				$where = "parent_name='".$row['category_name']."'";
				$result_childs = $db->select($table_prefix."virtuemart_category_links", "*", "$where", 'std', "'category_name'");
				if ($result_childs['rows'] > 0) {
					foreach($result_childs["value"] as $row_childs){
						$grid->addRow($options = array());
				
						$grid->setRowCell($colNum, $row_childs["category_links_ID"]);
						$grid->setRowCell($colName, $row_childs["category_name"]);	
						$grid->setRowCell($colParent, $row_childs["parent_name"]);
						$grid->setRowCell($colSyn, $row_childs["synonym_name"]);
						$query = "category_links_ID = ".$row_childs["category_links_ID"];
						$grid->setRowCell($colDel, '<a onclick="return onClickAjax('."'".$query."'".', '."'"."/components/com_import/delete_links.php"."'".', '."'".$result_field."'".')"" href="">удалить</a>');	
				
					}
				}
				$i++;
			}
		}
		echo "<h4>Текущая таблица:</h4>";								
		echo $grid->toString();
	}
class JGrid
{
	/**
	 * Array of columns
	 * @var array
	 */
	protected $columns = array();

	/**
	 * Current active row
	 * @var int
	 */
	protected $activeRow = 0;

	/**
	 * Rows of the table (including header and footer rows)
	 * @var array
	 */
	protected $rows = array();

	/**
	 * Header and Footer row-IDs
	 * @var array
	 */
	protected $specialRows = array('header' => array(), 'footer' => array());

	/**
	 * Associative array of attributes for the table-tag
	 * @var array
	 */
	protected $options;

	/**
	 * Constructor for a JGrid object
	 *
	 * @param   array  $options  Associative array of attributes for the table-tag
	 *
	 */
	public function __construct($options = array())
	{
		$this->setTableOptions($options, true);
	}

	/**
	 * Magic function to render this object as a table.
	 *
	 * @return  string
	 *
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Method to set the attributes for a table-tag
	 *
	 * @param   array  $options  Associative array of attributes for the table-tag
	 * @param   bool   $replace  Replace possibly existing attributes
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function setTableOptions($options = array(), $replace = false)
	{
		if ($replace)
		{
			$this->options = $options;
		}
		else
		{
			$this->options = array_merge($this->options, $options);
		}
		return $this;
	}

	/**
	 * Get the Attributes of the current table
	 *
	 * @return  array Associative array of attributes
	 *
	 */
	public function getTableOptions()
	{
		return $this->options;
	}

	/**
	 * Add new column name to process
	 *
	 * @param   string  $name  Internal column name
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function addColumn($name)
	{
		$this->columns[] = $name;

		return $this;
	}

	/**
	 * Returns the list of internal columns
	 *
	 * @return  array List of internal columns
	 *
	 */
	public function getColumns()
	{
		return $this->columns;
	}

	/**
	 * Delete column by name
	 *
	 * @param   string  $name  Name of the column to be deleted
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function deleteColumn($name)
	{
		$index = array_search($name, $this->columns);
		if ($index !== false)
		{
			unset($this->columns[$index]);
			$this->columns = array_values($this->columns);
		}

		return $this;
	}

	/**
	 * Method to set a whole range of columns at once
	 * This can be used to re-order the columns, too
	 *
	 * @param   array  $columns  List of internal column names
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function setColumns($columns)
	{
		$this->columns = array_values($columns);

		return $this;
	}

	/**
	 * Adds a row to the table and sets the currently
	 * active row to the new row
	 *
	 * @param   array  $options  Associative array of attributes for the row
	 * @param   int    $special  1 for a new row in the header, 2 for a new row in the footer
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function addRow($options = array(), $special = false)
	{
		$this->rows[]['_row'] = $options;
		$this->activeRow = count($this->rows) - 1;
		if ($special)
		{
			if ($special === 1)
			{
				$this->specialRows['header'][] = $this->activeRow;
			}
			else
			{
				$this->specialRows['footer'][] = $this->activeRow;
			}
		}

		return $this;
	}

	/**
	 * Method to get the attributes of the currently active row
	 *
	 * @return array Associative array of attributes
	 *
	 */
	public function getRowOptions()
	{
		return $this->rows[$this->activeRow]['_row'];
	}

	/**
	 * Method to set the attributes of the currently active row
	 *
	 * @param   array  $options  Associative array of attributes
	 *
	 * @return JGrid This object for chaining
	 *
	 */
	public function setRowOptions($options)
	{
		$this->rows[$this->activeRow]['_row'] = $options;

		return $this;
	}

	/**
	 * Get the currently active row ID
	 *
	 * @return  int ID of the currently active row
	 *
	 */
	public function getActiveRow()
	{
		return $this->activeRow;
	}

	/**
	 * Set the currently active row
	 *
	 * @param   int  $id  ID of the row to be set to current
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function setActiveRow($id)
	{
		$this->activeRow = (int) $id;
		return $this;
	}

	/**
	 * Set cell content for a specific column for the
	 * currently active row
	 *
	 * @param   string  $name     Name of the column
	 * @param   string  $content  Content for the cell
	 * @param   array   $option   Associative array of attributes for the td-element
	 * @param   bool    $replace  If false, the content is appended to the current content of the cell
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function setRowCell($name, $content, $option = array(), $replace = true)
	{
		if ($replace || !isset($this->rows[$this->activeRow][$name]))
		{
			$cell = new stdClass;
			$cell->options = $option;
			$cell->content = $content;
			$this->rows[$this->activeRow][$name] = $cell;
		}
		else
		{
			$this->rows[$this->activeRow][$name]->content .= $content;
			$this->rows[$this->activeRow][$name]->options = $option;
		}

		return $this;
	}

	/**
	 * Get all data for a row
	 *
	 * @param   int  $id  ID of the row to return
	 *
	 * @return  array Array of columns of a table row
	 *
	 */
	public function getRow($id = false)
	{
		if ($id === false)
		{
			$id = $this->activeRow;
		}

		if (isset($this->rows[(int) $id]))
		{
			return $this->rows[(int) $id];
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get the IDs of all rows in the table
	 *
	 * @param   int  $special  false for the standard rows, 1 for the header rows, 2 for the footer rows
	 *
	 * @return  array Array of IDs
	 *
	 */
	public function getRows($special = false)
	{
		if ($special)
		{
			if ($special === 1)
			{
				return $this->specialRows['header'];
			}
			else
			{
				return $this->specialRows['footer'];
			}
		}
		return array_diff(array_keys($this->rows), array_merge($this->specialRows['header'], $this->specialRows['footer']));
	}

	/**
	 * Delete a row from the object
	 *
	 * @param   int  $id  ID of the row to be deleted
	 *
	 * @return  JGrid This object for chaining
	 *
	 */
	public function deleteRow($id)
	{
		unset($this->rows[$id]);

		if (in_array($id, $this->specialRows['header']))
		{
			unset($this->specialRows['header'][array_search($id, $this->specialRows['header'])]);
		}

		if (in_array($id, $this->specialRows['footer']))
		{
			unset($this->specialRows['footer'][array_search($id, $this->specialRows['footer'])]);
		}

		if ($this->activeRow == $id)
		{
			end($this->rows);
			$this->activeRow = key($this->rows);
		}

		return $this;
	}

	/**
	 * Render the HTML table
	 *
	 * @return  string The rendered HTML table
	 *
	 */
	public function toString()
	{
		$output = array();
		$output[] = '<table' . $this->renderAttributes($this->getTableOptions()) . '>';

		if (count($this->specialRows['header']))
		{
			$output[] = $this->renderArea($this->specialRows['header'], 'thead', 'th');
		}

		if (count($this->specialRows['footer']))
		{
			$output[] = $this->renderArea($this->specialRows['footer'], 'tfoot');
		}

		$ids = array_diff(array_keys($this->rows), array_merge($this->specialRows['header'], $this->specialRows['footer']));
		if (count($ids))
		{
			$output[] = $this->renderArea($ids);
		}

		$output[] = '</table>';
		return implode('', $output);
	}

	/**
	 * Render an area of the table
	 *
	 * @param   array   $ids   IDs of the rows to render
	 * @param   string  $area  Name of the area to render. Valid: tbody, tfoot, thead
	 * @param   string  $cell  Name of the cell to render. Valid: td, th
	 *
	 * @return string The rendered table area
	 *
	 */
	protected function renderArea($ids, $area = 'tbody', $cell = 'td')
	{
		$output = array();
		$output[] = '<' . $area . ">\n";
		foreach ($ids as $id)
		{
			$output[] = "\t<tr" . $this->renderAttributes($this->rows[$id]['_row']) . ">\n";
			foreach ($this->getColumns() as $name)
			{
				if (isset($this->rows[$id][$name]))
				{
					$column = $this->rows[$id][$name];
					$output[] = "\t\t<" . $cell . $this->renderAttributes($column->options) . '>' . $column->content . '</' . $cell . ">\n";
				}
			}

			$output[] = "\t</tr>\n";
		}
		$output[] = '</' . $area . '>';

		return implode('', $output);
	}

	/**
	 * Renders an HTML attribute from an associative array
	 *
	 * @param   array  $attributes  Associative array of attributes
	 *
	 * @return  string The HTML attribute string
	 *
	 */
	protected function renderAttributes($attributes)
	{
		if (count((array) $attributes) == 0)
		{
			return '';
		}
		$return = array();
		foreach ($attributes as $key => $option)
		{
			$return[] = $key . '="' . $option . '"';
		}
		return ' ' . implode(' ', $return);
	}
}

>>>>>>> 1e1b309bbf9e27aa09f1eef63d7bfeed85150451
?>