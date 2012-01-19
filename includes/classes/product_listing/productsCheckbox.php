<?php
class productListing_productsCheckbox {

	public function sortColumns(){
		$selectSortKeys = array(
								array(
									'value' => '',
									'name'  => ''
								)
		);
		return $selectSortKeys;
	}

	public function show(&$productClass, &$purchaseTypesCol){

		return  '<input type="checkbox" value="'.$productClass->getID().'" name="selectProduct[]" class="selectProductsID"/>';
	}
}
?>