<?php
	$EmailTemplates = Doctrine_Core::getTable('EmailTemplates')->getTableName();
	
	$addColumns = array();
	if ($DoctrineImport->tableColumnExists($EmailTemplates, 'email_templates_attach') === false){
		$addColumns['email_templates_attach'] = array(
			'type' => 'string',
			'length' => 255
		);
	}
	
	$renameColumns = array();
	if ($DoctrineImport->tableColumnExists($EmailTemplates, 'email_templates_file') === true){
		$renameColumns['email_templates_file'] = array(
			'name' => 'email_templates_name',
			'definition' => array(
				'type' => 'string',
				'length' => 32
			)
		);
	}
	
	$changeColumns = array();
	if ($DoctrineImport->tableColumnExists($EmailTemplates, 'email_templates_event') === true){
		$changeColumns['email_templates_event'] = array(
			'length' => 32,
			'definition' => array(
				'type'   => 'string',
				'length' => 32
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
	
	if (!empty($changeColumns)){
		$commandArr['change'] = $changeColumns;
	}
	
	if (!empty($commandArr)){
		$DoctrineExport->alterTable($EmailTemplates, $commandArr);
	}
