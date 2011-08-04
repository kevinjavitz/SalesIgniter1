<?php
	$QdeleteIssue = Doctrine_Query::create()
	->delete('RentIssues')
	->where('issue_id = ?', (int)$_GET['fID'])
	->orWhere('parent_id = ?', (int)$_GET['fID'])
	->execute();

	EventManager::attachActionResponse(itw_app_link(null, 'rental_queue', 'issues'));
?>