<?php
	$Coupons = Doctrine_Core::getTable('Coupons')->getTableName();
	
	$addColumns = array();
	if ($DoctrineImport->tableColumnExists($Coupons, 'restrict_to_purchase_type') === false){
		$addColumns['restrict_to_purchase_type'] = array(
			'type' => 'string',
			'notnull' => true,
			'length' => 255
		);
	}
	
	if (!empty($addColumns)){
		$DoctrineExport->alterTable($Coupons, array(
			'add' => $addColumns
		));
	}
