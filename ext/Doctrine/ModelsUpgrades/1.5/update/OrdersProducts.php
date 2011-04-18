<?php
	$OrdersProducts = Doctrine_Core::getTable('OrdersProducts');
	
	$addColumns = array();
	if ($OrdersProducts->hasColumn('quantity_id') === false){
		$addColumns['quantity_id'] = array(
			'type' => 'integer',
			'length' => 4,
			'primary' => false,
			'autoincrement' => false
		);
	}
	
	$commandArr = array();	
	if (!empty($addColumns)){
		$commandArr['add'] = $addColumns;
	}
	
	if (!empty($commandArr)){
		$DoctrineExport->alterTable($OrdersProducts->getTableName(), $commandArr);
	}
