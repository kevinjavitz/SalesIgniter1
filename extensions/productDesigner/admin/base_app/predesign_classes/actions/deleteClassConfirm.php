<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Class = Doctrine_Core::getTable('ProductDesignerPredesignClasses')->findOneByClassId((int)$_POST['class_id']);
	if ($Class){
		$Class->delete();
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('cID', 'action'))), 'redirect');
?>