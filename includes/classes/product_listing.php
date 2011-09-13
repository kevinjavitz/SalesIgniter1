<?php
class productListing {

	public function __construct(){
		$this->query = false;
		$this->allowSorting = true;
		$this->usePaging = true;
		$this->showNoProducts = true;
		$this->sortKey = 'sort';
		$this->defaultSortKey = '';
		$this->listedProductsIDS = array();
		
		$this->columnInfo = array();
		$Qcolumns = Doctrine_Query::create()
		->from('ProductsListing p')
		->leftJoin('p.ProductsListingDescription pd')
		->where('p.products_listing_status = ?', '1')
		->andWhere('find_in_set(?, p.products_listing_template)', Session::get('tplDir'))
		->orderBy('p.products_listing_sort_order')
		->execute();
		if ($Qcolumns->count() > 0){
			foreach($Qcolumns->toArray(true) as $cInfo){
				$className = 'productListing_' . $cInfo['products_listing_module'];
				
				if (!class_exists($className)){
					require($this->getShowModule($cInfo['products_listing_module']));
				}
				
				$this->columnInfo[] = array(
					'enabled'       => ($cInfo['products_listing_status'] == '1'),
					'sort_order'    => $cInfo['products_listing_sort_order'],
					'allow_sort'    => ($cInfo['products_listing_allow_sort'] == '1'),
					'sort_key'      => $cInfo['products_listing_sort_key'],
					'sort_sql'      => $cInfo['products_listing_sort_column'],
					'heading'       => $cInfo['ProductsListingDescription'][Session::get('languages_id')]['products_listing_heading_text'],
					'heading_align' => $cInfo['products_listing_heading_align'],
					'heading_valign' => $cInfo['products_listing_heading_valign'],
					'showModule'    => new $className
				);
				if ($cInfo['products_listing_default_sorting'] == '1'){
					$this->defaultSortKey = $cInfo['products_listing_sort_key'];
				}
			}
		}

		EventManager::notify('ProductListingClassInit', &$this);
	}
	
	public function setTemplate($template, $dir){
		$this->templateFile = $template;
		$this->templateDir = $dir;
	}
	
	public function getShowModule($name){
		$module = false;
		
		if (file_exists(DIR_FS_CATALOG . 'includes/classes/product_listing/' . $name . '.php')){
			$module = DIR_FS_CATALOG . 'includes/classes/product_listing/' . $name . '.php';
		}
		
		if ($module === false){
			$dirObj = new DirectoryIterator(DIR_FS_CATALOG . 'extensions/');
			while($dirObj->valid()){
				if ($dirObj->isDot() || $dirObj->isFile()){
					$dirObj->next();
					continue;
				}

				if (is_dir($dirObj->getPathname() . '/catalog/classes/product_listing/')){
					if (file_exists($dirObj->getPathname() . '/catalog/classes/product_listing/' . $name . '.php')){
						$module = $dirObj->getPathname() . '/catalog/classes/product_listing/' . $name . '.php';
					}
				}
		
				$dirObj->next();
			}
		}
		
		if (file_exists(DIR_FS_CATALOG . 'templates/fallback/classes/product_listing/' . $name . '.php')){
			$module = DIR_FS_CATALOG . 'templates/fallback/classes/product_listing/' . $name . '.php';
		}
		
		if (file_exists(DIR_FS_CATALOG . DIR_WS_TEMPLATES . 'classes/product_listing/' . $name . '.php')){
			$module = DIR_FS_CATALOG . DIR_WS_TEMPLATES . 'classes/product_listing/' . $name . '.php';
		}
		
		if ($module !== false){
			return $module;
		}
		die('Product Listing Module Not Found:' . $name);
	}

	function getListedProductIDs(){
		return $this->listedProductsIDS;
	}

	function disableSorting(){
		$this->allowSorting = false;
		return $this;
	}
	
	function dontShowWhenEmpty(){
		$this->showNoProducts = false;
		return $this;
	}
	
	function showWhenEmpty(){
		$this->showNoProducts = true;
		return $this;
	}
	
	public function disablePaging(){
		$this->usePaging = false;
		return $this;
	}

	function getColumnUsingKey($arrKey, $value = false){
		foreach($this->columnInfo as $key => $kInfo){
			if (isset($kInfo[$arrKey]) && $kInfo[$arrKey] == $value){
				return $kInfo;
			}
		}
		return false;
	}

	function columnIsEnabled($colName){
		if (isset($this->columnInfo[$colName])){
			return $this->columnInfo[$colName]['enabled'];
		}
		return false;
	}

	function columnIsSortable($sortKey){
		$sortKey = substr($sortKey, 0, strlen($sortKey) - 2);
		$column = $this->getColumnUsingKey('sort_key', $sortKey);
		if ($column !== false){
			return $column['allow_sort'];
		}
		return false;
	}
	
	function getSortColumnSQL(){
		$currentSort = $_GET[$this->sortKey];
		$sortKey = substr($currentSort, 0, strlen($currentSort) - 2);
		 
		$column = $this->getColumnUsingKey('sort_key', $sortKey);
		if ($column['allow_sort'] === true){
			$sortDir = substr($currentSort, -1);
			if ($sortDir == 'a'){
				$sortDir = 'asc';
			}else{
				$sortDir = 'desc';
			}
			$sortVal = $column['sort_sql'];
			$isWhere = strpos($sortVal, ';');
			if ($isWhere !== false){
				$sortVal = substr($sortVal,0, $isWhere);
			}
			$sortSQL = $sortVal . ' ' . $sortDir;
			return $sortSQL;
		}
		return '';
	}

	function getWhereColumnSQL(){
		$currentSort = $_GET[$this->sortKey];
		$sortKey = substr($currentSort, 0, strlen($currentSort) - 2);
		$column = $this->getColumnUsingKey('sort_key', $sortKey);
		if ($column['allow_sort'] === true){
			$sortDir = substr($currentSort, -1);
			if ($sortDir == 'a'){
				$sortDir = 'asc';
			}else{
				$sortDir = 'desc';
			}

			$sortVal = '';
			$isWhere = strpos($column['sort_sql'], ';');
			if ($isWhere !== false){
				$sortVal = substr($column['sort_sql'],$isWhere + 1, strlen($column['sort_sql']) - $isWhere);
			}

			return $sortVal;
		}
		return '';
	}

	function getColumnList(){
		$columnList = array();
		foreach($this->columnInfo as $key => $kInfo){
			if ($kInfo['enabled'] === true){
				$columnList[$key] = $kInfo['sort_order'];
			}
		}
		asort($columnList);

		$this->columnList = array();
		foreach($columnList as $key => $sortOrder){
			$this->columnList[] = $key;
		}
		return $this->columnList;
	}

	function setQuery(&$doctrineObj){
		if (isset($this->loadedData)) die('Error: Product Listing Cannot Have Loaded Data And An SQL Query.');
		$this->query =& $doctrineObj;
		if ($this->allowSorting === true){
			if (isset($_GET[$this->sortKey])){
			   Session::set('sortingKey', $_GET[$this->sortKey]);
			}
			if (!Session::exists('sortingKey')){
				if (!empty($this->defaultSortKey) && !isset($_GET[$this->sortKey])){
					$_GET[$this->sortKey] = $this->defaultSortKey . '_a';
					Session::set('sortingKey', $this->defaultSortKey . '_a');
				}else if(empty($this->defaultSortKey) && !isset($_GET[$this->sortKey])){
					$_GET[$this->sortKey] = 'products_name_a';
					Session::set('sortingKey', 'products_name_a');					
				}
			}else{
				$_GET[$this->sortKey] = Session::get('sortingKey');
			}
			$sortStr = $this->getSortColumnSQL();
			$whereSql = $this->getWhereColumnSQL();
			if (!empty($sortStr)){
				$this->query->orderBy($sortStr);
			}
			if(!empty($whereSql)){
				$this->query->andWhere($whereSql);
			}

		
			if (isset($_GET['starts_with']) && !empty($_GET['starts_with'])){
				if ($_GET['starts_with'] == 'num'){
					$this->query->andWhere('pd.products_sname LIKE "0%" OR pd.products_sname LIKE "1%" OR pd.products_sname LIKE "2%" OR pd.products_sname LIKE "3%" OR pd.products_sname LIKE "4%" OR pd.products_sname LIKE "5%" OR pd.products_sname LIKE "6%" OR pd.products_sname LIKE "7%" OR pd.products_sname LIKE "8%" OR pd.products_sname LIKE "9%" OR pd.products_sname LIKE "#%"');
				}else{
					$this->query->andWhere('pd.products_sname LIKE ?', $_GET['starts_with'] . '%');
				}
			}
		}
		
		return $this;
	}

	/*where is this used?*/
	public function setData($products){
		if ($this->query !== false) die('Error: Product Listing Cannot Have An SQL Query And Loaded Data.');
		foreach($products as $productId){
			$this->loadedData[] = array(
				'products_id' => $productId
			);
		}
		return $this;
	}
}
?>