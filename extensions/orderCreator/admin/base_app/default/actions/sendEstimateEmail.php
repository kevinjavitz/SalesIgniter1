<?php
	$Orders = Doctrine_Core::getTable('Orders');
	$success = false;

	if (isset($_POST['oID']) && $_POST['oID'] != 'undefined'){
		$NewOrder = $Orders->find((int) $_POST['oID']);
		if(isset($_POST['email']) && !empty($_POST['email'])){

			$Editor->sendNewEstimateEmail($NewOrder, $_POST['email']);
			$success = true;
		}
	}

	EventManager::attachActionResponse(array(
		'success' => $success,
	), 'json');

?>