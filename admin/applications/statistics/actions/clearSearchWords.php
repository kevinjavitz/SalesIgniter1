<?php
	Doctrine_Query::create()
	->delete('SearchQueriesSorted')
	->execute();
	
	EventManager::attachActionResponse(itw_app_link(null, 'statistics', 'keywords'), 'redirect');
?>