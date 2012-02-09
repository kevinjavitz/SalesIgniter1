<?php
foreach($_POST['selectProduct'] as $k => $product){
		$productClass = new product($product);
		$purchaseTypeClass = $productClass->getPurchaseType($_POST['pType']);
		if($purchaseTypeClass->hasInventory()){
			if(isset($_POST['selectQty'][$k])){
				$rQty = $_POST['selectQty'][$k];
			}else{
				$rQty = 1;
			}
			if(isset($_POST['selectStartDate'][$k])){
				$_POST['start_date'] = $_POST['selectStartDate'][$k];
			}else{
				unset($_POST['start_date']);
			}
			if(isset($_POST['selectEndDate'][$k])){
				$_POST['end_date'] = $_POST['selectEndDate'][$k];
			}else{
				unset($_POST['end_date']);
			}
			if(isset($_POST['selectDaysBefore'][$k])){
				$_POST['days_before'] = $_POST['selectDaysBefore'][$k];
			}else{
				unset($_POST['days_before']);
			}
			if(isset($_POST['selectDaysAfter'][$k])){
				$_POST['days_after'] = $_POST['selectDaysAfter'][$k];
			}else{
				unset($_POST['days_after']);
			}
			if(isset($_POST['selectPickup'][$k])){
				$_POST['pickup'] = $_POST['selectPickup'][$k];
			}else{
				unset($_POST['pickup']);
			}
			if(isset($_POST['selectDropoff'][$k])){
				$_POST['dropoff'] = $_POST['selectDropoff'][$k];
			}else{
				unset($_POST['dropoff']);
			}
			$ShoppingCart->addProduct($product, $_POST['pType'], $rQty);
		}
	}
$json = array(
	'success' => true
);
EventManager::attachActionResponse($json, 'json');
?>