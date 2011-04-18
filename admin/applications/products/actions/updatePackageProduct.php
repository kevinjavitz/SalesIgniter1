<?php
	Doctrine_Query::create()
	->update('ProductsPackages')
	->set('quantity', '?', (int)$_GET['packageQuantity'])
	->where('products_id = ?', (int)$_GET['packageProductID'])
	->andWhere('parent_id = ?', (int)$_GET['packageParentID'])
	->andWhere('purchase_type = ?', $_GET['packageProductType'])
	->execute();
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>