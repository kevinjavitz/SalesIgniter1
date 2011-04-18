<?php
	$Coupons = Doctrine_Core::getTable('Coupons');
	
	$addColumns = array();
	if ($Coupons->hasColumn('restrict_to_purchase_type') === false){
		$addColumns['restrict_to_purchase_type'] = array(
			'type' => 'string',
			'notnull' => true,
			'length' => 255
		);
	}
	
	if (!empty($addColumns)){
		$DoctrineExport->alterTable($Coupons->getTableName(), array(
			'add' => $addColumns
		));
	}
