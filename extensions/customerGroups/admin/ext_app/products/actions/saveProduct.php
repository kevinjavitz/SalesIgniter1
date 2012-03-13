<?php
	//if (isset($_POST['banner_group_is_rotator'])){
		//$PayPerRental = $Product->ProductsPayPerRental;
		

	$ProductsToGroups =& $Product->ProductsToCustomerGroups;
	$ProductsToGroups->delete();
	if (isset($_POST['groups'])){
		foreach($_POST['groups'] as $groupId){
			$ProductsToGroups[]->customer_groups_id = $groupId;
		}
	}
	$Product->save();
		//$Product->save();
	//}
?>