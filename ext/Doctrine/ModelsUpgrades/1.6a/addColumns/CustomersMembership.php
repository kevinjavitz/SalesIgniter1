<?php
	$CustomersMembership = Doctrine_Core::getTable('CustomersMembership')->getTableName();
	
	$addColumns = array();
	if ($DoctrineImport->tableColumnExists($CustomersMembership, 'card_cvv') === false){
		$addColumns['card_cvv'] = array(
			'type' => 'string',
			'length' => 64,
			'notnull' => false
		);
	}
	
	if (!empty($addColumns)){
		$DoctrineExport->alterTable($CustomersMembership, array(
			'add' => $addColumns
		));
	}
