<?php
	$BannerManagerGroupsToStores =& $Group->BannerManagerGroupsToStores;
	$BannerManagerGroupsToStores->delete();
	if (isset($_POST['store'])){
		foreach($_POST['store'] as $storeId){
			$BannerManagerGroupsToStores[]->stores_id = $storeId;
		}
	}
	$Group->save();
?>