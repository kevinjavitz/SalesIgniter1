<?php
	$ProductsInventory = Doctrine_Core::getTable('ProductsInventory')->getTableName();
	
	$addColumns = array();
	if ($DoctrineImport->tableColumnExists($ProductsInventory, 'controller') === false){
		$addColumns['controller'] = array(
			'type' => 'string',
			'length' => 32,
			'default' => 'normal'
		);
	}
	
	$commandArr = array();	
	if (!empty($addColumns)){
		$commandArr['add'] = $addColumns;
	}
	
	if (!empty($commandArr)){
		$DoctrineExport->alterTable($ProductsInventory, $commandArr);
	}
