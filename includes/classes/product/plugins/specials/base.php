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
		$Qspecial = Doctrine_Query::create()
		->from('Specials')
		->where('products_id = ?', $this->productId)
		->andWhere('status = ?', '1')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qspecial){
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
		return $this->special[0]['specials_new_products_price'];
	}

	function displayPrice($priceOrig){
		global $currencies;
		return '<s>' . $priceOrig . '</s><br /><span class="productSpecialPrice">' . $currencies->format($this->getPrice(), $this->productInfo['taxRate']) . '</span>';
	}
}
?>