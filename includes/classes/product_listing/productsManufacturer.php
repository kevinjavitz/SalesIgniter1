<?php
class productListing_productsManufacturer {

	public function sortColumns(){
		$selectSortKeys = array(
								array(
									'value' => 'm.manufacturers_name',
									'name'  => sysLanguage::get('PRODUCT_LISTING_MANUFACTURERES')
								)

		);
		return $selectSortKeys;
	}

	public function show(&$productClass){
		if ($productClass->hasManufacturer()){
			return '<a href="' . htmlspecialchars(itw_app_link('manufacturers_id=' . $productClass->getManufacturerID(), 'index', 'default')) . '">' . htmlspecialchars($productClass->getManufacturerName()) . '</a>';
		}
		return false;
	}
}
?>