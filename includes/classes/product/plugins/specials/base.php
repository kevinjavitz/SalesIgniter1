<?php
class productPlugin_specials {
	function __construct($pId, &$productQuery){
		$this->productId = $pId;
	}
	
	public function loadProductInfo(){
		$this->isSpecial();
	}

	function isNeeded(){
		return $this->isSpecial();
	}

	function isSpecial(){
		$Qspecial = dataAccess::setQuery('select * from {specials} where products_id = {product_id} and status = "1"')
		->setTable('{specials}', TABLE_SPECIALS)
		->setValue('{product_id}', $this->productId)
		->runQuery();
		if ($Qspecial->numberOfRows() > 0){
			$this->special = $Qspecial;
			return true;
		}
		return false;
	}

	function getPrice(){
		if (!isset($this->special)){
			if ($this->isSpecial() === false){
				return false;
			}
		}
		return $this->special->getVal('specials_new_products_price');
	}

	function displayPrice($priceOrig){
		global $currencies;
		return '<s>' . $priceOrig . '</s><br /><span class="productSpecialPrice">' . $currencies->format($this->getPrice(), $this->productInfo['taxRate']) . '</span>';
	}
}
?>