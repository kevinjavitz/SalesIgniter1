<?php
	$OrdersAddresses = Doctrine_Core::getTable('OrdersAddresses')->getTableName();
	
	$changeColumns = array();
	if ($DoctrineImport->tableColumnExists($OrdersAddresses, 'orders_id') === true){
		$changeColumns['orders_id'] = array(
			'length' => 4,
			'definition' => array(
				'type'   => 'integer',
				'length' => 4,
				'primary' => false,
				'autoincrement' => false
			)
		);
	}
	
	if ($DoctrineImport->tableColumnExists($OrdersAddresses, 'address_type') === true){
		$changeColumns['address_type'] = array(
			'length' => 32,
			'definition' => array(
				'type'   => 'string',
				'length' => 32,
				'primary' => false
			)
		);
	}
	
	$commandArr = array();	
	if (!empty($changeColumns)){
		$commandArr['change'] = $changeColumns;
	}
	
	if (!empty($commandArr)){
		$DoctrineExport->alterTable($OrdersAddresses, $commandArr);
	}
