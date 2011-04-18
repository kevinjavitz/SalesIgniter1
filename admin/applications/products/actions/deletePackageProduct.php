<?php
	Doctrine_Query::create()
	->delete('ProductsPackages')
	->where('products_id = ?', (int)$_GET['packageProductID'])
	->andWhere('parent_id = ?', (int)$_GET['packageParentID'])
	->andWhere('purchase_type = ?', $_GET['packageProductType'])
	->execute();
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>