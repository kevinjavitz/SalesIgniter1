<?php
	Doctrine_Query::create()
	->delete('ProductsOptionsValuesToProductsOptions')
	->where('products_options_id = ?', $_GET['option_id'])
	->andWhere('products_options_values_id = ?', str_replace('value_', '', $_GET['value_id']))
	->execute();

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>