<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Classes = Doctrine_Core::getTable('ProductDesignerPredesignClasses');
	if (isset($_GET['cID'])){
		$Class = $Classes->findOneByClassId((int)$_GET['cID']);
	}else{
		$Class = $Classes->create();
	}
	
	$Class->class_name = $_POST['class_name'];
	$Class->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $Class->class_id, null, 'default'), 'redirect');
?>