<?php
	$jsonData = array();

	$QcustomerName = Doctrine_Query::create()
	->from('Customers')
	->where('customers_firstname LIKE ?', $_GET['term'] . '%')
	->orWhere('customers_lastname LIKE ?', $_GET['term'] . '%')
	->orWhere('customers_email_address LIKE ?', $_GET['term'] . '%')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($QcustomerName){
		foreach($QcustomerName as $cInfo){
			$jsonData[] = array(
				'value' => $cInfo['customers_id'],
				'label' => $cInfo['customers_firstname'] . ' ' . $cInfo['customers_lastname'] . ' ( ' . $cInfo['customers_email_address'] . ' )'
			);
		}
	}
	
	EventManager::attachActionResponse($jsonData, 'json');
?>