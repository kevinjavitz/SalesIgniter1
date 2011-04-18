<?php
	$FeaturedManagerGroupsToStores =& $Group->FeaturedManagerGroupsToStores;
	$FeaturedManagerGroupsToStores->delete();
	if (isset($_POST['store'])){
		foreach($_POST['store'] as $storeId){
			$FeaturedManagerGroupsToStores[]->stores_id = $storeId;
		}
	}
	$Group->save();
?>