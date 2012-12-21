<?php
    function fgetcsv_file($f, $length, $d=",", $q='"') {
        $list = array();
        $st = fgets($f, $length);
        if ($st === false || $st === null) return $st;
        if (trim($st) === "") return array("");
        while ($st !== "" && $st !== false) {
            if ($st[0] !== $q) {
                # Non-quoted.
                list ($field) = explode($d, $st, 2);
                $st = substr($st, strlen($field)+strlen($d));
            } else {
                # Quoted field.
                $st = substr($st, 1);
                $field = "";
                while (1) {
                    # Find until finishing quote (EXCLUDING) or eol (including)
                    preg_match("/^((?:[^$q]+|$q$q)*)/sx", $st, $p);
                    $part = $p[1];
                    $partlen = strlen($part);
                    $st = substr($st, strlen($p[0]));
                    $field .= str_replace($q.$q, $q, $part);
                    if (strlen($st) && $st[0] === $q) {
                        # Found finishing quote.
                        list ($dummy) = explode($d, $st, 2);
                        $st = substr($st, strlen($dummy)+strlen($d));
                        break;
                    } else {
                        # No finishing quote - newline.
                        $st = fgets($f, $length);
                    }
                }

            }
            $list[] = $field;
        }
        return $list;
    }

  function fgetcsv_string($st, $d=",", $q='"') {
	  $list = array();
	  echo "st'".$st."'<br />";
        if ($st === false || $st === null) return $st;
        if (trim($st) === "") return array("");
        while ($st !== "" && $st !== false) {
            if ($st[0] !== $q) {
                # Non-quoted.
                list ($field) = explode($d, $st, 2);
                $st = substr($st, strlen($field)+strlen($d));
            } else {
                # Quoted field.
                $st = substr($st, 1);
                $field = "";
                while (1) {
                    # Find until finishing quote (EXCLUDING) or eol (including)
					echo "st'".$st."'<br />";
                    preg_match("/^((?:[^$q]+|$q$q)*)/sx", $st, $p);
                    $part = $p[1];
                    $partlen = strlen($part);
                    $st = substr($st, strlen($p[0]));
                    $field .= str_replace($q.$q, $q, $part);
                    if (strlen($st) && $st[0] === $q) {
                        # Found finishing quote.
                        list ($dummy) = explode($d, $st, 2);
                        $st = substr($st, strlen($dummy)+strlen($d));
                        break;
                    }
                }

            }
            $list[] = $field;
        }
        return $list;
    }
	
	 function fgetcsv_array(&$arr, $d=",", $q='"') {
        $list = array();
        $st = array_shift ($arr);
        if ($st === false || $st === null) return $st;
        if (trim($st) === "") return array("");
        while ($st !== "" && $st !== false) {
            if ($st[0] !== $q) {
                # Non-quoted.
                list ($field) = explode($d, $st, 2);
                $st = substr($st, strlen($field)+strlen($d));
            } else {
                # Quoted field.
                $st = substr($st, 1);
                $field = "";
                while (1) {
                    # Find until finishing quote (EXCLUDING) or eol (including)
                    preg_match("/^((?:[^$q]+|$q$q)*)/sx", $st, $p);
                    $part = $p[1];
                    $partlen = strlen($part);
                    $st = substr($st, strlen($p[0]));
                    $field .= str_replace($q.$q, $q, $part);
                    if (strlen($st) && $st[0] === $q) {
                        # Found finishing quote.
                        list ($dummy) = explode($d, $st, 2);
                        $st = substr($st, strlen($dummy)+strlen($d));
                        break;
                    } else {
                        # No finishing quote - newline.
                        $st = array_shift ($arr);
                    }
                }

            }
            $list[] = $field;
        }
        return $list;
    }
	
?>
<?php
  // функция делает из строки правильный alias для отображения в url:
  //1. исправляем кириллицу
  //2. заменяем пробелы на -
  function makeAliasFromName($source)
  {
	  if (preg_match('/[^A-Za-z0-9_\-]/', $source)) {
	    $trans = array(
    	    "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
        	"Д"=>"d","Е"=>"e","Ж"=>"j","З"=>"z","И"=>"i",
        	"Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
        	"О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
        	"У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
       		"Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
        	"Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
        	"в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
        	"з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        	"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        	"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
       		"ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        	"ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya", 
        	" "=> "_", "."=> "", "/"=> "_"
    	);
		$alias = strtr($source, $trans);
	    $alias = preg_replace('/[^A-Za-z0-9_\-]/', '', $alias);
		}
		else $alias = $source;
    	return $alias;
}

?>