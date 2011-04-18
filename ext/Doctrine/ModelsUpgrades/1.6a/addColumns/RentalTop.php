<?php
	$RentalTop = Doctrine_Core::getTable('RentalTop')->getTableName();
	
	$addColumns = array();
	if ($DoctrineImport->tableColumnExists($RentalTop, 'date_modified') === false){
		$addColumns['date_modified'] = array(
			'type' => 'timestamp',
			'length' => null
		);
	}
	
	$commandArr = array();	
	if (!empty($addColumns)){
		$commandArr['add'] = $addColumns;
	}
	
	if (!empty($commandArr)){
		$DoctrineExport->alterTable($RentalTop, $commandArr);
	}
