<?php
class productListing_productsModel {
   public function sortColumns(){
		$selectSortKeys = array(
								array(
									'value' => 'p.products_model',
									'name'  => sysLanguage::get('PRODUCT_LISTING_PRODUCT_MODEL')
								)

		);
		return $selectSortKeys;
	}

	public function show(&$productClass, &$purchaseTypesCol){
		if ($productClass->hasModel()){
			return htmlspecialchars($productClass->getModel());
		}
		return false;
	}
}
?>