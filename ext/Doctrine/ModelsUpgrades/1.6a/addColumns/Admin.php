<?php
	$Admin = Doctrine_Core::getTable('Admin')->getTableName();
	
	$addColumns = array();
	if ($DoctrineImport->tableColumnExists($Admin, 'config_home') === false){
		$addColumns['config_home'] = array(
			'type' => 'string',
			'notnull' => true,
			'length' => 999
		);
	}
	
	if ($DoctrineImport->tableColumnExists($Admin, 'favorites_links') === false){
		$addColumns['favorites_links'] = array(
			'type' => 'string',
			'notnull' => true,
			'length' => 999
		);
	}
	
	if ($DoctrineImport->tableColumnExists($Admin, 'favorites_names') === false){
		$addColumns['favorites_names'] = array(
			'type' => 'string',
			'notnull' => true,
			'length' => 999
		);
	}
	
	if (!empty($addColumns)){
		$DoctrineExport->alterTable($Admin, array(
			'add' => $addColumns
		));
	}
