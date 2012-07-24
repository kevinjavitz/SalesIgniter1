<?php
	Doctrine_Query::create()
	->delete('CustomTags')
	->where('tag_id = ?', (int)$_GET['tID'])
	->execute();
	
	EventManager::attachActionResponse(itw_app_link('appExt=customTags', 'tags', 'default'), 'redirect');
?>