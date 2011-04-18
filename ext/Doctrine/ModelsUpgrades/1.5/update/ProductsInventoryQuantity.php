<?php
	$ProductsInventoryQuantity = Doctrine_Core::getTable('ProductsInventoryQuantity');
	
	$addColumns = array();
	if ($ProductsInventoryQuantity->hasColumn('attributes') === false){
		$addColumns['attributes'] = array(
			'type' => 'string',
			'length' => 999
		);
	}
	
	$commandArr = array();	
	if (!empty($addColumns)){
		$commandArr['add'] = $addColumns;
	}
	
	if (!empty($commandArr)){
		$DoctrineExport->alterTable($ProductsInventoryQuantity->getTableName(), $commandArr);
	}
