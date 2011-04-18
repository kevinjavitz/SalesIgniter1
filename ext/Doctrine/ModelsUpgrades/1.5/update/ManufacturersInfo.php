<?php
	$ManufacturersInfo = Doctrine_Core::getTable('ManufacturersInfo');
	
	$changeColumns = array();
	if ($ManufacturersInfo->hasColumn('manufacturers_id') === true){
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
	
	if ($ManufacturersInfo->hasColumn('languages_id') === true){
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
		$DoctrineExport->alterTable($ManufacturersInfo->getTableName(), $commandArr);
	}
