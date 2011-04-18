<?php
	$CategoriesToStores =& $Category->CategoriesToStores;
	$CategoriesToStores->delete();
	if (isset($_POST['store'])){
		foreach($_POST['store'] as $storeId){
			$CategoriesToStores[]->stores_id = $storeId;
		}
	}
	$Category->save();
?>