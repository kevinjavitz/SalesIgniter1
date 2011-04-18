<?php
	Doctrine_Query::create()
	->update('Newsletters')
	->set('locked', '?', '1')
	->where('newsletters_id = ?', (int) $_GET['nID'])
	->execute();
	
	EventManager::attachActionResponse(itw_app_link((isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'nID=' . (int) $_GET['nID'], 'newsletters', 'default'), 'redirect');
?>