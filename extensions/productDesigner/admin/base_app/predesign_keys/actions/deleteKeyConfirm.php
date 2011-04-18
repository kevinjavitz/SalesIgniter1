<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Key = Doctrine_Core::getTable('ProductDesignerPredesignKeys')->findOneByKeyId((int)$_POST['key_id']);
	if ($Key){
		$Key->delete();
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('kID', 'action'))), 'redirect');
?>