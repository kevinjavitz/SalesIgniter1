<?php
	foreach($_POST['qty'] as $pID_string => $qInfo){
		foreach($qInfo as $purchaseType => $qty){
			$ShoppingCart->updateProduct($pID_string, array(
				'quantity'      => $qty,
				'purchase_type' => $purchaseType
			));
		}
	}

	if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
		EventManager::attachActionResponse(array(
			'success' => true
		), 'json');
	}else{
		EventManager::attachActionResponse(itw_app_link(null, 'checkout', 'default'), 'redirect');
	}
?>