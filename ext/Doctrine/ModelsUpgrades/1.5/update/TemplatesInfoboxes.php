<?php
	$TemplatesInfoboxes = Doctrine_Core::getTable('TemplatesInfoboxes');
	
	$addColumns = array();
	if ($TemplatesInfoboxes->hasColumn('ext_name') === false){
		$addColumns['ext_name'] = array(
			'type' => 'string',
			'length' => 64
		);
	}
	
	$renameColumns = array();
	if ($TemplatesInfoboxes->hasColumn('templates_infoboxes_id') === true){
		$renameColumns['templates_infoboxes_id'] = array(
			'name' => 'box_id',
			'definition' => array(
				'type' => 'integer',
				'length' => 4,
				'primary' => true,
				'autoincrement' => true
			)
		);
	}
	
	if ($TemplatesInfoboxes->hasColumn('box_filename') === true){
		$renameColumns['box_filename'] = array(
			'name' => 'box_code',
			'definition' => array(
				'type' => 'string',
				'length' => 64
			)
		);
	}
	
	$commandArr = array();	
	if (!empty($addColumns)){
		$commandArr['add'] = $addColumns;
	}
	
	if (!empty($renameColumns)){
		$commandArr['rename'] = $renameColumns;
	}
	
	if (!empty($commandArr)){
		$DoctrineExport->alterTable($TemplatesInfoboxes->getTableName(), $commandArr);
	}
