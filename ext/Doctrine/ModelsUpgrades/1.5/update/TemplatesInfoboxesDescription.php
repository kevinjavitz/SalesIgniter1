<?php
	$TemplatesInfoboxesDescription = Doctrine_Core::getTable('TemplatesInfoboxesDescription');
	
	$renameColumns = array();
	if ($TemplatesInfoboxesDescription->hasColumn('templates_infoboxes_description_id') === true){
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
	
	if ($TemplatesInfoboxesDescription->hasColumn('templates_infoboxes_id') === true){
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
	
	if ($TemplatesInfoboxesDescription->hasColumn('infobox_heading') === true){
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
		$DoctrineExport->alterTable($TemplatesInfoboxesDescription->getTableName(), $commandArr);
	}
