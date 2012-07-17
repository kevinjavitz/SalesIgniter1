<?php
	$jsonData = array();

$QcustomerName = Doctrine_Query::create()
	->from('Customers c')
	->where('c.customers_email_address = ?', $_GET['emailaddr'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$cID = 0;
if(isset($QcustomerName[0])){
	$cID = $QcustomerName[0]['customers_id'];
}

$jsonData = array(
	'success' => true,
	'cID' => $cID,
);

EventManager::attachActionResponse($jsonData, 'json');
?>
