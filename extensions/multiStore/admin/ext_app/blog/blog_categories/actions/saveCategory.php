<?php
	$BlogCategoriesToStores =& $Category->BlogCategoriesToStores;
	$BlogCategoriesToStores->delete();
	if (isset($_POST['store'])){
		foreach($_POST['store'] as $storeId){
			$BlogCategoriesToStores[]->stores_id = $storeId;
		}
	}
	$Category->save();
?>