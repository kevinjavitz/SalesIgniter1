<?php
	$ConfigurationGroup = Doctrine_Core::getTable('ConfigurationGroup')->getTableName();
	
	$addColumns = array();
	if ($DoctrineImport->tableColumnExists($ConfigurationGroup, 'configuration_group_key') === false){
		$addColumns['configuration_group_key'] = array(
			'type' => 'string',
			'notnull' => true,
			'length' => 128
		);
	}
	
	if (!empty($addColumns)){
		$DoctrineExport->alterTable($ConfigurationGroup, array(
			'add' => $addColumns
		));
	}
	
