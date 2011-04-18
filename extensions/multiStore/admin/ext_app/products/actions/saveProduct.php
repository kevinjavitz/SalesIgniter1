<?php
	$ProductsToStores = $Product->ProductsToStores;
	$ProductsToStores->delete();
	if (isset($_POST['store'])){
		foreach($_POST['store'] as $storeId){
			$ProductsToStores[]->stores_id = $storeId;
		}
	}
	$Product->save();
?>