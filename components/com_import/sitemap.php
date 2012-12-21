<<<<<<< HEAD
<?php 
	include($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'lib_db.php');
	
	$sm = new Sitemap();
	$sm->createSitemap();
	
	class Sitemap{
		private $SITE_NAME = '';
		private $CHANGE_FREQ = 'weekly';
		private $VM_PRODUCT_SUFFIX = "-detail";
		private $SITEMAP_FILE_NAME = "sitemap.xml";
		private $PRIORITY = '1';
		private $db;
		private $table_prefix = '';
		
		public function __construct(){
			$this->SITE_NAME = "http://".$_SERVER['SERVER_NAME'];
			$this->db = new Dbprocess();
			$this->db->connectToDb();
			$this->table_prefix = $this->db->getTablePrefix();
		}
	
		public function createSitemap(){
			$where = "published = 1 and menutype = 'mainmenu' and access IN (1)";
			$result = $this->db->select($this->table_prefix."menu", "*", "$where", 'std');

			$dom = new DOMDocument('1.0');
			$dom->formatOutput = true;
	
			$root = $dom->createElement('urlset');
			$root = $dom->appendChild($root);
			$ns = $dom->createAttributeNS("http://www.sitemaps.org/schemas/sitemap/0.9", "xmlns");
			

			foreach($result["value"] as $row ){
				$link_arr = parse_url($row['link']);
				if (isset($link_arr['query'])) {
			
					parse_str(html_entity_decode($link_arr['query']), $link_vars);
					$option = HELPER::getValue($link_vars, 'option', '');
					$view = HELPER::getValue($link_vars, 'view', '');
					$layout = HELPER::getValue($link_vars, 'layout', '');
					
					if($option == "com_virtuemart" && $view == "category") $id = HELPER::getValue($link_vars, 'virtuemart_category_id', 0);
					elseif($option == "com_fabrik" && $view == "form") $id = HELPER::getValue($link_vars, 'formid', 0);
					else $id = HELPER::getValue($link_vars, 'id', 0);
					
					$url = $dom->createElement('url');
					$url = $root->appendChild($url);
					$this->setMenuUrl($view, $row, $option, $id, $dom, $root, $url);
				}
			}
			$str = $dom->saveXML();
			echo "Создана карта сайта sitemap.xml, размер ".$dom->save($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$this->SITEMAP_FILE_NAME)." байт.";
		}

	private function setMenuUrl($view, $row, $option, $id, $dom, $root, $url){
		if($row['home']=='1') 	$link = $this->SITE_NAME;
		else					$link = $this->SITE_NAME."/".$row['path'];
		
		switch ($view) {
            case 'category':
				if ($option == "com_virtuemart") {
					$this->appendVMHierarchy($dom, $root, $url, $id, $this->SITE_NAME);
				}
				else{
					$where = "id = $id";
					$result = $this->db->select($this->table_prefix."categories", "*", "$where", 'std');		
					$lastmod = $result["value"][0]['modified_time'];	
					$this->appendSitemapUrlData($dom ,$url, $link, $lastmod);					
					$where = "catid = $id";
					$result = $this->db->select($this->table_prefix."content", "*", "$where", 'std');
					if ($result['rows'] > 0) {
						foreach($result["value"] as $article){
							$child_url = $dom->createElement('url');
							$child_url = $root->appendChild($child_url);
							//$child_url = $url->appendChild($child_url); - Иерархическая структура
							$this->appendSitemapUrlData($dom ,$child_url, $link."/".$article["alias"], $article['modified']);
						}
					}
				}
                break;
			case "virtuemart":
				$where = "virtuemart_vendor_id = 1";
				$result = $this->db->select($this->table_prefix."virtuemart_vendors", "*", "$where", 'std');		
				$lastmod = $result["value"][0]['modified_on'];
				
				$this->appendSitemapUrlData($dom, $url, $link, $lastmod);
				break;
			
			case 'form':	
				if ($option == "com_fabrik") {
					$where = "id = $id";
					$result = $this->db->select($this->table_prefix."fabrik_forms", "*", "$where", 'std');
					$lastmod = $result["value"][0]['created'];
				
					$this->appendSitemapUrlData($dom ,$url, $link, $lastmod);
				}			
				break;
            case 'article':
				
				$where = "id = $id";
				$result = $this->db->select($this->table_prefix."content", "*", "$where", 'std');		
				$lastmod = $result["value"][0]['modified'];
				
				$this->appendSitemapUrlData($dom, $url, $link, $lastmod);
                break;
			default:
				$loc = $dom->createElement('loc', $view);
				$loc = $url->appendChild($loc);
				break;
			}	
	}
	function appendVMHierarchy($dom, $root, $url, $id, $root_link){
//Категории
		$new_root_link = $this->appendVMCategory($dom, $root, $url, $id, $root_link);
		$where = "category_parent_id = $id";
		$result = $this->db->select($this->table_prefix."virtuemart_category_categories", "*", "$where", 'std');
		if ($result['rows'] > 0) {
			foreach($result["value"] as $category){
				$child_url = $dom->createElement('url');
				$child_url = $root->appendChild($child_url);
				$this->appendVMHierarchy($dom, $root, $child_url, $category['id'], $new_root_link);
			}
		}
//Продукты		
		$where = "virtuemart_category_id = $id";
		$result = $this->db->select($this->table_prefix."virtuemart_product_categories", "*", "$where", 'std');
		if ($result['rows'] > 0) {
			foreach($result["value"] as $product){
				$child_url = $dom->createElement('url');
				$child_url = $root->appendChild($child_url);
				$this->appendVMProduct($dom, $root, $child_url, $product['virtuemart_product_id'], $new_root_link);
			}
		}		
	}
	
	function appendVMCategory($dom, $root, $url, $id, $root_link){
		$where = "virtuemart_category_id=$id";
		
		$result = $this->db->select($this->table_prefix."virtuemart_categories_ru_ru", "*", "$where", 'std');	
		$link = $root_link."/".$result["value"][0]['slug'];

		$result = $this->db->select($this->table_prefix."virtuemart_categories", "*", "$where", 'std');	
		$lastmod = $result["value"][0]['modified_on'];	
				
		$this->appendSitemapUrlData($dom ,$url, $link, $lastmod);	
		return $link;			
	}
	
	function appendVMProduct($dom, $root, $url, $id, $root_link){
		$where = "virtuemart_product_id=$id";
		
		$result = $this->db->select($this->table_prefix."virtuemart_products_ru_ru", "*", "$where", 'std');	
		$link = $root_link."/".$result["value"][0]['slug'].$this->VM_PRODUCT_SUFFIX;

		$result = $this->db->select($this->table_prefix."virtuemart_products", "*", "$where", 'std');	
		$lastmod = $result["value"][0]['modified_on'];	
				
		$this->appendSitemapUrlData($dom ,$url, $link, $lastmod);	
		
	}	
	
	function appendSitemapUrlData($dom ,$url, $link, $lastmod){
		$loc = $dom->createElement('loc', $link);
		$loc = $url->appendChild($loc);
		$lastmod = $dom->createElement('lastmod', $lastmod);
		$lastmod = $url->appendChild($lastmod);
		$freq = $dom->createElement('changefreq', $this->CHANGE_FREQ);
		$freq = $url->appendChild($freq );
		$priority = $dom->createElement('priority', $this->PRIORITY);
		$priority = $url->appendChild($priority);
	}
	
}

class HELPER{
		function getValue(&$array, $name, $default = null)
	{
		// Initialise variables.
		$result = null;

		if (isset($array[$name]))
		{
			$result = $array[$name];
		}

		// Handle the default case
		if (is_null($result))
		{
			$result = $default;
		}
		return $result;
	}
}
=======
<?php 
	include($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'lib_db.php');
	
	$sm = new Sitemap();
	$sm->createSitemap();
	
	class Sitemap{
		private $SITE_NAME = '';
		private $CHANGE_FREQ = 'weekly';
		private $VM_PRODUCT_SUFFIX = "-detail";
		private $SITEMAP_FILE_NAME = "sitemap.xml";
		private $PRIORITY = '1';
		private $db;
		private $table_prefix = '';
		
		public function __construct(){
			$this->SITE_NAME = "http://".$_SERVER['SERVER_NAME'];
			$this->db = new Dbprocess();
			$this->db->connectToDb();
			$this->table_prefix = $this->db->getTablePrefix();
		}
	
		public function createSitemap(){
			$where = "published = 1 and menutype = 'mainmenu' and access IN (1)";
			$result = $this->db->select($this->table_prefix."menu", "*", "$where", 'std');

			$dom = new DOMDocument('1.0');
			$dom->formatOutput = true;
	
			$root = $dom->createElement('urlset');
			$root = $dom->appendChild($root);
			$ns = $dom->createAttributeNS("http://www.sitemaps.org/schemas/sitemap/0.9", "xmlns");
			

			foreach($result["value"] as $row ){
				$link_arr = parse_url($row['link']);
				if (isset($link_arr['query'])) {
			
					parse_str(html_entity_decode($link_arr['query']), $link_vars);
					$option = HELPER::getValue($link_vars, 'option', '');
					$view = HELPER::getValue($link_vars, 'view', '');
					$layout = HELPER::getValue($link_vars, 'layout', '');
					
					if($option == "com_virtuemart" && $view == "category") $id = HELPER::getValue($link_vars, 'virtuemart_category_id', 0);
					elseif($option == "com_fabrik" && $view == "form") $id = HELPER::getValue($link_vars, 'formid', 0);
					else $id = HELPER::getValue($link_vars, 'id', 0);
					
					$url = $dom->createElement('url');
					$url = $root->appendChild($url);
					$this->setMenuUrl($view, $row, $option, $id, $dom, $root, $url);
				}
			}
			$str = $dom->saveXML();
			echo "Создана карта сайта sitemap.xml, размер ".$dom->save($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$this->SITEMAP_FILE_NAME)." байт.";
		}

	private function setMenuUrl($view, $row, $option, $id, $dom, $root, $url){
		if($row['home']=='1') 	$link = $this->SITE_NAME;
		else					$link = $this->SITE_NAME."/".$row['path'];
		
		switch ($view) {
            case 'category':
				if ($option == "com_virtuemart") {
					$this->appendVMHierarchy($dom, $root, $url, $id, $this->SITE_NAME);
				}
				else{
					$where = "id = $id";
					$result = $this->db->select($this->table_prefix."categories", "*", "$where", 'std');		
					$lastmod = $result["value"][0]['modified_time'];	
					$this->appendSitemapUrlData($dom ,$url, $link, $lastmod);					
					$where = "catid = $id";
					$result = $this->db->select($this->table_prefix."content", "*", "$where", 'std');
					if ($result['rows'] > 0) {
						foreach($result["value"] as $article){
							$child_url = $dom->createElement('url');
							$child_url = $root->appendChild($child_url);
							//$child_url = $url->appendChild($child_url); - Иерархическая структура
							$this->appendSitemapUrlData($dom ,$child_url, $link."/".$article["alias"], $article['modified']);
						}
					}
				}
                break;
			case "virtuemart":
				$where = "virtuemart_vendor_id = 1";
				$result = $this->db->select($this->table_prefix."virtuemart_vendors", "*", "$where", 'std');		
				$lastmod = $result["value"][0]['modified_on'];
				
				$this->appendSitemapUrlData($dom, $url, $link, $lastmod);
				break;
			
			case 'form':	
				if ($option == "com_fabrik") {
					$where = "id = $id";
					$result = $this->db->select($this->table_prefix."fabrik_forms", "*", "$where", 'std');
					$lastmod = $result["value"][0]['created'];
				
					$this->appendSitemapUrlData($dom ,$url, $link, $lastmod);
				}			
				break;
            case 'article':
				
				$where = "id = $id";
				$result = $this->db->select($this->table_prefix."content", "*", "$where", 'std');		
				$lastmod = $result["value"][0]['modified'];
				
				$this->appendSitemapUrlData($dom, $url, $link, $lastmod);
                break;
			default:
				$loc = $dom->createElement('loc', $view);
				$loc = $url->appendChild($loc);
				break;
			}	
	}
	function appendVMHierarchy($dom, $root, $url, $id, $root_link){
//Категории
		$new_root_link = $this->appendVMCategory($dom, $root, $url, $id, $root_link);
		$where = "category_parent_id = $id";
		$result = $this->db->select($this->table_prefix."virtuemart_category_categories", "*", "$where", 'std');
		if ($result['rows'] > 0) {
			foreach($result["value"] as $category){
				$child_url = $dom->createElement('url');
				$child_url = $root->appendChild($child_url);
				$this->appendVMHierarchy($dom, $root, $child_url, $category['id'], $new_root_link);
			}
		}
//Продукты		
		$where = "virtuemart_category_id = $id";
		$result = $this->db->select($this->table_prefix."virtuemart_product_categories", "*", "$where", 'std');
		if ($result['rows'] > 0) {
			foreach($result["value"] as $product){
				$child_url = $dom->createElement('url');
				$child_url = $root->appendChild($child_url);
				$this->appendVMProduct($dom, $root, $child_url, $product['virtuemart_product_id'], $new_root_link);
			}
		}		
	}
	
	function appendVMCategory($dom, $root, $url, $id, $root_link){
		$where = "virtuemart_category_id=$id";
		
		$result = $this->db->select($this->table_prefix."virtuemart_categories_ru_ru", "*", "$where", 'std');	
		$link = $root_link."/".$result["value"][0]['slug'];

		$result = $this->db->select($this->table_prefix."virtuemart_categories", "*", "$where", 'std');	
		$lastmod = $result["value"][0]['modified_on'];	
				
		$this->appendSitemapUrlData($dom ,$url, $link, $lastmod);	
		return $link;			
	}
	
	function appendVMProduct($dom, $root, $url, $id, $root_link){
		$where = "virtuemart_product_id=$id";
		
		$result = $this->db->select($this->table_prefix."virtuemart_products_ru_ru", "*", "$where", 'std');	
		$link = $root_link."/".$result["value"][0]['slug'].$this->VM_PRODUCT_SUFFIX;

		$result = $this->db->select($this->table_prefix."virtuemart_products", "*", "$where", 'std');	
		$lastmod = $result["value"][0]['modified_on'];	
				
		$this->appendSitemapUrlData($dom ,$url, $link, $lastmod);	
		
	}	
	
	function appendSitemapUrlData($dom ,$url, $link, $lastmod){
		$loc = $dom->createElement('loc', $link);
		$loc = $url->appendChild($loc);
		$lastmod = $dom->createElement('lastmod', $lastmod);
		$lastmod = $url->appendChild($lastmod);
		$freq = $dom->createElement('changefreq', $this->CHANGE_FREQ);
		$freq = $url->appendChild($freq );
		$priority = $dom->createElement('priority', $this->PRIORITY);
		$priority = $url->appendChild($priority);
	}
	
}

class HELPER{
		function getValue(&$array, $name, $default = null)
	{
		// Initialise variables.
		$result = null;

		if (isset($array[$name]))
		{
			$result = $array[$name];
		}

		// Handle the default case
		if (is_null($result))
		{
			$result = $default;
		}
		return $result;
	}
}
>>>>>>> 1e1b309bbf9e27aa09f1eef63d7bfeed85150451
?>