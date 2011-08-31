<?php

class productListing_productsDescription {

   public function sortColumns(){

		$selectSortKeys = array(

								array(

									'value' => 'p.products_description',

									'name'  => sysLanguage::get('PRODUCT_LISTING_PRODUCT_MODEL')

								)



		);

		return $selectSortKeys;

	}



	public function show(&$productClass){

			return $productClass->getDescription();

		return false;

	}

}

?>