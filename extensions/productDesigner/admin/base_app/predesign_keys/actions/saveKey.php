<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Keys = Doctrine_Core::getTable('ProductDesignerPredesignKeys');
	if (isset($_GET['kID'])){
		$Key = $Keys->findOneByKeyId((int)$_GET['kID']);
	}else{
		$Key = $Keys->create();
	}
	
	$Key->key_text = $_POST['key_text'];
	$Key->key_type = $_POST['key_type'];
	$Key->set_from = $_POST['set_from'];
	$Key->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'kID')) . 'kID=' . $Key->key_id, null, 'default'), 'redirect');
?>