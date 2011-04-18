<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Categories = Doctrine_Core::getTable('ProductDesignerPredesignCategories')->findOneByCategoriesId((int)$_POST['categories_id']);
	if ($Categories){
		$Categories->delete();
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('cID', 'action'))), 'redirect');
?>