<?php
	$OrdersPaymentsHistory = Doctrine_Core::getTable('OrdersPaymentsHistory');
	
	$addColumns = array();
	if ($OrdersPaymentsHistory->hasColumn('success') === false){
		$addColumns['success'] = array(
			'type' => 'integer',
			'length' => 1,
			'primary' => false,
			'autoincrement' => false
		);
	}
	
	$commandArr = array();	
	if (!empty($addColumns)){
		$commandArr['add'] = $addColumns;
	}
	
	if (!empty($commandArr)){
		$DoctrineExport->alterTable($OrdersPaymentsHistory->getTableName(), $commandArr);
	}
