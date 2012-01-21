<?php
class productListing_productsInventoryCentersSortOrder {

   public function sortColumns(){

		$selectSortKeys = array(
							    array(
									'value' => 'ic.inventory_center_sort_order',
									'name'  => sysLanguage::get('INVENTORY_CENTERS_SORT_ORDER')
								)

		);

		return $selectSortKeys;
	}

	public function show(&$productClass){

		return false;
	}
}
?>