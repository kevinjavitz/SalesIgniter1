<?php
	Doctrine_Query::create()
	->delete('CustomerGroups')
	->where('customer_groups_id = ?', (int)$_GET['cID'])
	->execute();
	
	EventManager::attachActionResponse(itw_app_link('appExt=customerGroups', 'manage', 'default'), 'redirect');
?>