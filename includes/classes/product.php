<?php
require(dirname(__FILE__) . '/product/PurchaseTypeAbstract.php');
require(dirname(__FILE__) . '/product/Inventory.php');

class Product {
	public function __construct($pID){
		global $appExtension;
		$this->pluginDir = sysConfig::getDirFsCatalog() . 'includes/classes/product/plugins/';
		$this->valid = false;
		
		$productQuery = Doctrine_Query::create()
		->select('p.*, pd.*, m.*')
		->from('Products p')
		->leftJoin('p.ProductsDescription pd')
		->leftJoin('p.Manufacturers m')
		->where('p.products_id = ?', (int)$pID)
		->andWhere('pd.language_id = ?', Session::get('languages_id'));
		
		EventManager::notify('ProductQueryBeforeExecute', &$productQuery);
		//echo $productQuery->getSqlQuery();
		
		$this->initPlugins((int)$pID, $productQuery);
		
		$productInfo = $productQuery->fetchOne();

		if ($productInfo){
			$this->valid = true;
			$this->productInfo = $productInfo->toArray(true);
			$this->productInfo['taxRate'] = tep_get_tax_rate($this->productInfo['products_tax_class_id']);
			$this->productInfo['typeArr'] = explode(',', $this->productInfo['products_type']);
			
			foreach($this->plugins as $pluginName => $pluginClass){
				$this->plugins[$pluginName]->loadProductInfo($this->productInfo);
			}
			
			EventManager::notify('ProductQueryAfterExecute', &$this->productInfo);
		}
		$productQuery->free();
		$productQuery = null;
		unset($productQuery);
	}
	
	public function getPurchaseType($typeName, $forceEnable = false){
		global $appExtension;
		$className = 'PurchaseType_' . $typeName;

		if (!class_exists($className, false)){
			$purchaseTypesPath = 'classes/product/purchase_types/';
			$baseFilePath = sysConfig::getDirFsCatalog() . 'includes/' . $purchaseTypesPath;
			if (file_exists($baseFilePath . $typeName . '.php')){
				require($baseFilePath . $typeName . '.php');
			}else{
				$extFilePath = sysConfig::getDirFsCatalog() . 'extensions/';
				$Exts = $appExtension->getExtensions();
				foreach($Exts as $extName => $extCls){
					if (file_exists($extFilePath . $extName . '/catalog/' . $purchaseTypesPath . $typeName . '.php')){
						require($extFilePath . $extName . '/catalog/' . $purchaseTypesPath . $typeName . '.php');
						break;
					}
				}
			}
		}
				
		$purchaseType = null;
		if (class_exists($className, false)){
			$purchaseType = new $className($this, $forceEnable);
		}
		return $purchaseType;
	}
	
	public function initPlugins($pID = null, &$productQuery){
		$fileObj = new DirectoryIterator($this->pluginDir);
		while($fileObj->valid()){
			if ($fileObj->isDot() || !$fileObj->isDir()){
				$fileObj->next();
				continue;
			}
			$pluginName = $fileObj->getBasename();
			$className = 'productPlugin_' . $pluginName;
			if (!class_exists($className, false)){
				require($fileObj->getPathname() . '/base.php');
			}
			
			if (!isset($this->plugins[$pluginName])){
				$this->plugins[$pluginName] = new $className($pID, $productQuery);
			}
			$fileObj->next();
		}
		
		EventManager::notify('ProductClassInitPlugins', &$pID, &$this);
		return $this;
	}
	
	function pluginIsLoaded($pluginName){
		if (isset($this->classes['plugin'][$pluginName])){
			return true;
		}
		return false;
	}

	function updateViews(){
		$Qupdate = dataAccess::setQuery('update {products_description} set products_viewed = products_viewed+1 where products_id = {product_id} and language_id = {language_id}');
		$Qupdate->setTable('{products_description}', TABLE_PRODUCTS_DESCRIPTION);
		$Qupdate->setValue('{product_id}', $this->productInfo['products_id']);
		$Qupdate->setValue('{language_id}', Session::get('languages_id'));
		$Qupdate->runQuery();
	}

	function isValid(){
		return isset($this->productInfo['products_id']) && $this->productInfo['products_id'] > 0;
	}

	function isActive(){
		return ($this->productInfo['products_status'] == '1');
	}

	function isFeatured(){
		return ($this->productInfo['products_featured'] == '1');
	}

	function isNotAvailable(){
		return ($this->getAvailableDate() > date('Y-m-d H:i:s'));
	}

	/* HAS Methods -- Begin -- */
	function hasModel(){ return (tep_not_null($this->productInfo['products_model'])); }
	function hasManufacturer(){ return ($this->productInfo['manufacturers_id'] > 0); }
	function hasURL(){ return tep_not_null($this->productInfo['ProductsDescription'][Session::get('languages_id')]['products_url']); }
	function hasImage(){ return tep_not_null($this->productInfo['products_image']); }
	/* HAS Methods -- End -- */

	/* GET Methods -- Begin --*/
	function getID(){ return (int)$this->productInfo['products_id']; }
	function getStock(){ return (int)$this->productInfo['products_quantity']; }
	function getTaxRate(){ return $this->productInfo['taxRate']; }
	function getModel(){ return $this->productInfo['products_model']; }
	function getName(){ return stripslashes($this->productInfo['ProductsDescription'][Session::get('languages_id')]['products_name']); }
	function getImage(){ return sysConfig::getDirWsCatalog() . sysConfig::get('DIR_WS_IMAGES') . $this->productInfo['products_image']; }
	function getDescription(){ return stripslashes($this->productInfo['ProductsDescription'][Session::get('languages_id')]['products_description']); }
	function getManufacturerID(){ return $this->productInfo['Manufacturers']['manufacturers_id']; }
	function getManufacturerName(){ return stripslashes($this->productInfo['Manufacturers']['manufacturers_name']); }
	function getURL(){ return $this->productInfo['ProductsDescription'][Session::get('languages_id')]['products_url']; }
	function getPreview(){ return $this->productInfo['movie_preview']; }
	function getAvailableDate(){ return $this->productInfo['products_date_available']; }
	function getLastModified(){ return $this->productInfo['products_last_modified']; }
	function getDateAdded(){ return $this->productInfo['products_date_added']; }
	function getWeight(){ return $this->productInfo['products_weight']; }
	function getTaxClassID(){ return $this->productInfo['products_tax_class_id']; }
	function getPType(){ return $this->productInfo['products_ptype']; }
	function getPurchaseTypesArray(){ return explode(',',$this->productInfo['products_type']); }
	//function getAuthMethod(){ return $this->productInfo['products_auth_method']; }
	//function getAuthCharge(){ return $this->productInfo['products_auth_charge']; }
	/* GET Methods -- End --*/

	/* Box Set Methods -- Begin -- */
	function isBox(){
		if (isset($this->plugins['box_sets'])){
			return $this->plugins['box_sets']->isBox();
		}
		return false;
	}

	function isInBox(){
		if (isset($this->plugins['box_sets'])){
			return $this->plugins['box_sets']->isInBox();
		}
		return false;
	}

	function getBoxID(){
		if (isset($this->plugins['box_sets'])){
			return $this->plugins['box_sets']->getBoxID();
		}
		return false;
	}

	function getTotalDiscs(){
		if (isset($this->plugins['box_sets'])){
			return $this->plugins['box_sets']->getTotalDiscs();
		}
		return 0;
	}

	function getDiscs($exclude = false, $onlyIds = false){
		if (isset($this->plugins['box_sets'])){
			return $this->plugins['box_sets']->getDiscs($exclude, $onlyIds);
		}
		return false;
	}

	function getBoxName(){
		if (isset($this->plugins['box_sets'])){
			return $this->plugins['box_sets']->getName();
		}
		return false;
	}

	function getDiscNumber($pID = false){
		if (isset($this->plugins['box_sets'])){
			return $this->plugins['box_sets']->getDiscNumber($pID);
		}
		return false;
	}
	/* Box Set Methods -- End -- */

	/* Package Products -- Begin -- */
	function hasPackageProducts($type = 'new'){
		if (isset($this->classes['plugin']['package'])){
			return $this->classes['plugin']['package']->hasPackageProducts($type);
		}
		return false;
	}

	function getPackageProducts($type = 'new'){
		if (isset($this->classes['plugin']['package'])){
			return $this->classes['plugin']['package']->getPackageProducts($type);
		}
		return false;
	}
	/* Package Products -- End -- */
}
?>