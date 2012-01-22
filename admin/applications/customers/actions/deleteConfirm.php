<?php
	$Customers = Doctrine::getTable('Customers')->find((int)$_GET['cID']);
	if ($Customers){
		$isAllowed = true;
		$errorMessages = array();
		EventManager::notify('AdminDeleteCustomerCheckAllowed', &$isAllowed, &$errorMessages, $Customers);
		if ($isAllowed === true){
			$Customers->delete();
			$response = array(
				'success' => true
			);
		}else{
			$errorMsg = 'Customer account could not be deleted.' . "\n\n" .
				'The following errors were reported:' . "\n";
			foreach($errorMessages as $k => $v){
				$errorMsg .= $k+1 . ': ' . $v . "\n";
			}

			$response = array(
				'success' => false,
				'errorMessage' => $errorMsg
			);
		}
	}
	
	EventManager::attachActionResponse($response, 'json');
?>