<?php
	Doctrine_Query::create()
	->delete('ProductsOptionsToProductsOptionsGroups')
	->where('products_options_groups_id = ?', $_GET['group_id'])
	->andWhere('products_options_id = ?', str_replace('option_', '', $_GET['option_id']))
	->execute();

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>