<?php
	Doctrine_Query::create()
	->delete('ForcedSetRelations')
	->where('forced_set_id = ?', (int)$_GET['fID'])
	->execute();
	
	EventManager::attachActionResponse(itw_app_link('appExt=forcedSet', 'custom_set', 'default'), 'redirect');
?>