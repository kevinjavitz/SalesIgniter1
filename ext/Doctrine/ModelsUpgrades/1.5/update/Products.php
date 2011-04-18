<?php
	$Products = Doctrine_Core::getTable('Products');
	
	$addColumns = array();
	if ($Products->hasColumn('products_inventory_controller') === false){
		$addColumns['products_inventory_controller'] = array(
			'type' => 'string',
			'length' => 32,
			'default' => 'normal'
		);
	}
	
	$changeColumns = array();
	if ($Products->hasColumn('products_model') === true){
		$changeColumns['products_model'] = array(
			'length' => 255,
			'definition' => array(
				'type'   => 'string',
				'length' => 255
			)
		);
	}
	
	if ($Products->hasColumn('products_image') === true){
		$changeColumns['products_image'] = array(
			'length' => 255,
			'definition' => array(
				'type'   => 'string',
				'length' => 255
			)
		);
	}
	
	$commandArr = array();	
	if (!empty($addColumns)){
		$commandArr['add'] = $addColumns;
	}
	
	if (!empty($changeColumns)){
		$commandArr['change'] = $changeColumns;
	}
	
	if (!empty($commandArr)){
		$DoctrineExport->alterTable($Products->getTableName(), $commandArr);
	}
