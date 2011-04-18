<?php
	$ManufacturersInfo = Doctrine_Core::getTable('ManufacturersInfo')->getTableName();
	
	$changeColumns = array();
	if ($DoctrineImport->tableColumnExists($ManufacturersInfo, 'manufacturers_id') === true){
		$changeColumns['manufacturers_id'] = array(
			'length' => 4,
			'definition' => array(
				'type'   => 'integer',
				'length' => 4,
				'primary' => false,
				'autoincrement' => false
			)
		);
	}
	
	if ($DoctrineImport->tableColumnExists($ManufacturersInfo, 'languages_id') === true){
		$changeColumns['languages_id'] = array(
			'length' => 4,
			'definition' => array(
				'type'   => 'integer',
				'length' => 4,
				'primary' => false,
				'autoincrement' => false
			)
		);
	}
	
	$commandArr = array();	
	if (!empty($changeColumns)){
		$commandArr['change'] = $changeColumns;
	}
	
	if (!empty($commandArr)){
		$DoctrineExport->alterTable($ManufacturersInfo, $commandArr);
	}
