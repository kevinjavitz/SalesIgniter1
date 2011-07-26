<?php
	$Issue = Doctrine_Core::getTable('RentIssues')->find((int) $_GET['fID']);
	$Issue->status = 'C';
	$Issue->save();

	EventManager::attachActionResponse(itw_app_link('fID=' . (int)$_GET['fID'], 'rental_queue', 'issues'), 'redirect');
?>