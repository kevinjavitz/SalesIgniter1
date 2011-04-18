<?php
	$Languages = Doctrine_Core::getTable('Languages');
	
	$addColumns = array();
	if ($Languages->hasColumn('name_real') === false){
		$addColumns['name_real'] = array(
			'type' => 'string',
			'length' => 32
		);
	}
	
	$changeColumns = array();
	if ($Languages->hasColumn('code') === true){
		$changeColumns['code'] = array(
			'length' => 8,
			'definition' => array(
				'type'   => 'string',
				'length' => 8
			)
		);
	}
	
	$commandArr = array();	
	if (!empty($addColumns)){
		$commandArr['add'] = $addColumns;
	}
	
	if (!empty($changeColumns)){
		$commandArr['change'] = $changeColumns;
	}
	
	if (!empty($commandArr)){
		$DoctrineExport->alterTable($Languages->getTableName(), $commandArr);
	}
