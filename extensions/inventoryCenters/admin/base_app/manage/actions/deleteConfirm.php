<?php
	Doctrine_Query::create()
	->delete('ProductsInventoryCenters')
	->where('inventory_center_id = ?', (int)$_GET['cID'])
	->execute();
	
	Doctrine_Query::create()
	->delete('InventoryCentersLaunchPoints')
	->where('inventory_center_id = ?', (int)$_GET['cID'])
	->execute();
	EventManager::attachActionResponse(itw_app_link('appExt=inventoryCenters', 'manage', 'default'), 'redirect');
?>