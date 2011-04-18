<?php
class productPlugin_packages {
	function __construct($pId){
		$this->productId = $pId;
		$this->packagedProducts = array();
	}

	public function loadProductInfo(){
	}

	function hasPackageProducts(){
		$Qproducts = dataAccess::setQuery('select * from {packages} where parent_id = {product_id}');
		$Qproducts->setTable('{packages}', TABLE_PRODUCTS_PACKAGES);
		$Qproducts->setValue('{product_id}', $this->productId);
		$Qproducts->runQuery();
		if ($Qproducts->numberOfRows() > 0){
			$this->packagedProducts = array();
			while($Qproducts->next() !== false){
				/*
				* @todo: Only here until the system can be made to support all purchase types
				*/
				if ($Qproducts->getVal('purchase_type') != 'reservation') die('Only "Pay Per Rental" products may be inside packages.');
				$this->packagedProducts[] = array(
				'productClass'    => new product($Qproducts->getVal('products_id'), $Qproducts->getVal('purchase_type')),
				'packageQuantity' => $Qproducts->getVal('quantity')
				);
			}
			return true;
		}
		return false;
	}

	function getProductsInventoryItems(){
		if (sizeof($this->packagedProducts) > 0){
			$barcodes = array();
			foreach($this->packagedProducts as $pInfo){
				$productID = $pInfo['productClass']->getID();
				if (!isset($barcodes[$productID])){
					$barcodes[$productID] = array();
				}
				$barcodes[$productID] = $pInfo['productClass']->getProductsBarcodes();
			}
			return $barcodes;
		}
		return false;
	}

	function getCurrentStock(){
		if (sizeof($this->packagedProducts) > 0){
			$hasStock = true;
			$returnStock = 0;
			foreach($this->packagedProducts as $pInfo){
				$thisProductStock = $pInfo['productClass']->classes['plugin']['inventory']->getCurrentStock($pInfo['productClass']->onlyLoadType);
				if ($thisProductStock > $returnStock){
					$returnStock = $thisProductStock;
				}
				if ($thisProductStock < $pInfo['packageQuantity']){
					$hasStock = false;
					break;
				}
			}

			if ($hasStock === true){
				return $returnStock;
			}else{
				return 0;
			}
		}
		return 0;
	}

	function getPackageProducts(){
		return $this->packagedProducts;
	}
}
?>