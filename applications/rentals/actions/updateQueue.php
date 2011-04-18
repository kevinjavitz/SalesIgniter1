<?php
/*
	SalesIgniter E-Commerce System v1

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2010 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$queuePriority = array();
	$queueRemove = array();

	if (isset($_POST['queue_priority'])){
		$queuePriority = $_POST['queue_priority'];
	}
	if (isset($_POST['queue_delete'])){
		$queueRemove = $_POST['queue_delete'];
	}

	foreach($queuePriority as $idx => $pElement){
		$rentalQueueBase->updatePriority($idx, $pElement);
	}

	$rentalQueueBase->fixPriorities();

	foreach($queueRemove as $rElement){
		$rentalQueueBase->removeFromQueue($rElement);
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>