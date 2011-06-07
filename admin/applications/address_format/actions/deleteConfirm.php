<?php
	Doctrine_Query::create()
	->delete('AddressFormat')
	->where('address_format_id = ?', (int)$_GET['fID'])
	->execute();
	
	EventManager::attachActionResponse(itw_app_link(null, 'address_format', 'default'), 'redirect');
?>