<?php
	$TemplatesInfoboxesDescription = Doctrine_Core::getTable('TemplatesInfoboxesDescription')->getTableName();
	
	$renameColumns = array();
	if ($DoctrineImport->tableColumnExists($TemplatesInfoboxesDescription, 'templates_infoboxes_description_id') === true){
		$renameColumns['templates_infoboxes_description_id'] = array(
			'name' => 'box_description_id',
			'definition' => array(
				'type' => 'integer',
				'length' => 4,
				'primary' => true,
				'autoincrement' => true
			)
		);
	}
	
	if ($DoctrineImport->tableColumnExists($TemplatesInfoboxesDescription, 'templates_infoboxes_id') === true){
		$renameColumns['templates_infoboxes_id'] = array(
			'name' => 'box_id',
			'definition' => array(
				'type' => 'integer',
				'length' => 4,
				'primary' => true,
				'autoincrement' => false
			)
		);
	}
	
	if ($DoctrineImport->tableColumnExists($TemplatesInfoboxesDescription, 'infobox_heading') === true){
		$renameColumns['infobox_heading'] = array(
			'name' => 'box_heading',
			'definition' => array(
				'type' => 'string',
				'length' => 255
			)
		);
	}
	
	$commandArr = array();	
	if (!empty($renameColumns)){
		$commandArr['rename'] = $renameColumns;
	}
	
	if (!empty($commandArr)){
		$DoctrineExport->alterTable($TemplatesInfoboxesDescription, $commandArr);
	}
