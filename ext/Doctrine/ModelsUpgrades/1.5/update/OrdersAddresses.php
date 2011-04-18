<?php
	$OrdersAddresses = Doctrine_Core::getTable('OrdersAddresses');
	
	$changeColumns = array();
	if ($OrdersAddresses->hasColumn('orders_id') === true){
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
	
	if ($OrdersAddresses->hasColumn('address_type') === true){
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
		$DoctrineExport->alterTable($OrdersAddresses->getTableName(), $commandArr);
	}
