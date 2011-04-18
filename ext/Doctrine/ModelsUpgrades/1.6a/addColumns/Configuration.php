<?php
	$Configuration = Doctrine_Core::getTable('Configuration');
	
	$changeColumns = array(
		'configuration_title' => array(
			'length' => 250,
			'definition' => array(
				'type'    => 'string',
				'length'  => 250,
				'primary' => false,
				'default' => '',
				'notnull' => true
			)
		),
		'configuration_key' => array(
			'length' => 200,
			'definition' => array(
				'type'    => 'string',
				'length'  => 200,
				'primary' => false,
				'default' => '',
				'notnull' => true
			)
		)
	);
	
	if (!empty($changeColumns)){
		$DoctrineExport->alterTable($Configuration->getTableName(), array(
			'change' => $changeColumns
		));
	}
