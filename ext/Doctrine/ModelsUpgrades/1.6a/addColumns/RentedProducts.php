<?php
	$RentedProducts = Doctrine_Core::getTable('RentedProducts')->getTableName();
	
	$addColumns = array();
	if ($DoctrineImport->tableColumnExists($RentedProducts, 'rental_status_id') === false){
		$addColumns['rental_status_id'] = array(
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
		$DoctrineExport->alterTable($RentedProducts, $commandArr);
	}
