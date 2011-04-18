<?php
	$OrdersStatus = Doctrine_Core::getTable('OrdersStatus');
	if (isset($_GET['sID'])){
		$Status = $OrdersStatus->find((int) $_GET['sID']);
	}else{
		$Status = $OrdersStatus->getRecord();
	}
	
	$Description = $Status->OrdersStatusDescription;
	foreach($_POST['orders_status_name'] as $langId => $statusName){
		$Description[$langId]->language_id = $langId;
		$Description[$langId]->orders_status_name = $statusName;
	}

	$Status->save();
	
	if (isset($_POST['default']) && ($_POST['default'] == 'on')){
		Doctrine_Query::create()
		->update('Configuration')
		->set('configuration_value', '?', $Status->orders_status_id)
		->where('configuration_key = ?', 'DEFAULT_ORDERS_STATUS_ID')
		->execute();
	}

	//EventManager::attachActionResponse(itw_app_link((isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'oID=' . $Status->orders_status_id, 'orders_status', 'default'), 'redirect');
 	EventManager::attachActionResponse(array(
		'success' => true,
		'sID'	=>  $Status->orders_status_id
	), 'json');
?>