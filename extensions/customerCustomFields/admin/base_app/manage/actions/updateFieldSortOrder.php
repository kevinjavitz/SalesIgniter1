<?php
	$groupId = $_GET['group_id'];

	if(strstr($_GET['field'],',')){
		$newOrders = explode(',',$_GET['field']);
	}
	else{
		$newOrders = $_GET['field'];
	}

	for($i=0; $i<sizeof($newOrders); $i++){
		Doctrine_Query::create()
		->update('CustomerCustomFieldsToGroups')
		->set('sort_order', '?', ($i + 1))
		->where('field_id = ?', $newOrders[$i])
		->andWhere('group_id = ?', $groupId)
		->execute();
	}

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>