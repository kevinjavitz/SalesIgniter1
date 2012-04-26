<?php
	$Orders = Doctrine_Core::getTable('Orders');
	$success = false;
	if (isset($_POST['oID']) && $_POST['oID'] != 'undefined'){
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