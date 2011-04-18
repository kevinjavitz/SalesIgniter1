<?php
	$OrdersTotal = Doctrine_Core::getTable('OrdersTotal')->getTableName();
	
	$addColumns = array();
	if ($DoctrineImport->tableColumnExists($OrdersTotal, 'module_type') === false){
		$addColumns['module_type'] = array(
			'type' => 'string',
			'length' => 32
		);
	}
	
	if ($DoctrineImport->tableColumnExists($OrdersTotal, 'method') === false){
		$addColumns['method'] = array(
			'type' => 'string',
			'length' => 64
		);
	}
	
	$renameColumns = array();
	if ($DoctrineImport->tableColumnExists($OrdersTotal, 'class') === true){
		$renameColumns['class'] = array(
			'name' => 'module',
			'definition' => array(
				'type' => 'string',
				'length' => 32
			)
		);
	}
	
	$commandArr = array();	
	if (!empty($addColumns)){
		$commandArr['add'] = $addColumns;
	}
	
	if (!empty($renameColumns)){
		$commandArr['rename'] = $renameColumns;
	}
	
	if (!empty($commandArr)){
		$DoctrineExport->alterTable($OrdersTotal, $commandArr);
	}
