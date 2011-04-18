<?php
class productListing_productsWeight {
	public function sortColumns(){
		$selectSortKeys = array(
								array(
									'value' => 'p.products_weight',
									'name'  => sysLanguage::get('PRODUCT_LISTING_PRODUCT_WEIGHT')
								)

		);
		return $selectSortKeys;
	}
	public function show(&$productClass){
		if ($productClass->hasWeight()){
			return (int)$productClass->getWeight();
		}
		return false;
	}
}
?>