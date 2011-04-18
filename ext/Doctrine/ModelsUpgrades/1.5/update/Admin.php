<?php
	$Admin = Doctrine_Core::getTable('Admin');
	
	$addColumns = array();
	if ($Admin->hasColumn('config_home') === false){
		$addColumns['config_home'] = array(
			'type' => 'string',
			'notnull' => true,
			'length' => 999
		);
	}
	
	if ($Admin->hasColumn('favorites_links') === false){
		$addColumns['favorites_links'] = array(
			'type' => 'string',
			'notnull' => true,
			'length' => 999
		);
	}
	
	if ($Admin->hasColumn('favorites_names') === false){
		$addColumns['favorites_names'] = array(
			'type' => 'string',
			'notnull' => true,
			'length' => 999
		);
	}
	
	if (!empty($addColumns)){
		$DoctrineExport->alterTable($Admin->getTableName(), array(
			'add' => $addColumns
		));
	}
