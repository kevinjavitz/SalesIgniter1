<?php
/*
	Info Pages Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$Page = Doctrine_Core::getTable('Pages')->find((int)$_POST['page_id']);
	if ($Page){
		$Page->delete();
	}

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>