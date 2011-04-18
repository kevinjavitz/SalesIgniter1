<?php
	Doctrine_Query::create()
	->delete('GoogleZones')
	->where('google_zones_id = ?', (int)$_GET['zID'])
	->execute();
	
	EventManager::attachActionResponse(itw_app_link(null, 'zones', 'default'), 'redirect');
?>