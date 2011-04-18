<?php
	$CustomersMembership = Doctrine_Core::getTable('CustomersMembership');
	
	$addColumns = array();
	if ($CustomersMembership->hasColumn('card_cvv') === false){
		$addColumns['card_cvv'] = array(
			'type' => 'string',
			'length' => 64,
			'notnull' => false
		);
	}
	
	if (!empty($addColumns)){
		$DoctrineExport->alterTable($CustomersMembership->getTableName(), array(
			'add' => $addColumns
		));
	}
