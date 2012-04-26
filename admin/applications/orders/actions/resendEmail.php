<?php
	$Orders = Doctrine_Core::getTable('Orders');
	$success = false;
	require(sysConfig::getDirFsCatalog()  . 'extensions/orderCreator/admin/classes/Order/Base.php');

	if (isset($_POST['oID']) && $_POST['oID'] != 'undefined'){
		$Editor = new OrderCreator((int) $_POST['oID']);
		$NewOrder = $Orders->find((int) $_POST['oID']);

			if(isset($_POST['isEstimate'])&& $_POST['isEstimate'] == '1'){
				$Editor->sendNewEstimateEmail($NewOrder);
			}else{
				$Editor->sendNewOrderEmail($NewOrder);
			}
			$success = true;

	}

EventManager::attachActionResponse(array(
		'success' => $success,
	), 'json');


?>