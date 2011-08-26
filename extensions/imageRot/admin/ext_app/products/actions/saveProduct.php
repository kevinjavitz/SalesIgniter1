<?php
	//if (isset($_POST['banner_group_is_rotator'])){
		//$PayPerRental = $Product->ProductsPayPerRental;
		

	$ProductsToGroups =& $Product->BannerManagerProductsToGroups;
	$ProductsToGroups->delete();
	if (isset($_POST['groups'])){
		foreach($_POST['groups'] as $groupId){
			$ProductsToGroups[]->banner_group_id = $groupId;
		}
	}
	$Product->save();
		//$Product->save();
	//}
?>