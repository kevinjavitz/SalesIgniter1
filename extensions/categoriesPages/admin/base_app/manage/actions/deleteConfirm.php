<?php
	Doctrine_Query::create()
	->delete('CategoriesPages')
	->where('categories_pages_id = ?', (int)$_GET['cID'])
	->execute();
	
	EventManager::attachActionResponse(itw_app_link('appExt=categoriesPages', 'manage', 'default'), 'redirect');
?>