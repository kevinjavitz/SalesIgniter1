<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Activities = Doctrine_Core::getTable('ProductDesignerPredesignActivities');
	if (isset($_GET['aID'])){
		$Activity = $Activities->findOneByActivityId((int)$_GET['aID']);
	}else{
		$Activity = $Activities->create();
	}
	
	$Activity->activity_name = $_POST['activity_name'];
	$Activity->save();
	
	$MultiStore = $appExtension->getExtension('multiStore');
	if ($MultiStore !== false){
		Doctrine_Query::create()
		->delete('ProductDesignerPredesignActivitiesToStores')
		->where('activity_id = ?', $Activity->activity_id)
		->execute();
		if (isset($_POST['stores_id'])){
			foreach($_POST['stores_id'] as $storeId){
				$ActivityToStore = new ProductDesignerPredesignActivitiesToStores();
				$ActivityToStore->stores_id = $storeId;
				$ActivityToStore->activity_id = $Activity->activity_id;
				$ActivityToStore->save();
			}
		}
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'aID')) . 'aID=' . $Activity->activity_id, null, 'default'), 'redirect');
?>