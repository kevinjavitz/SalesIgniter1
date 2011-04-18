<?php
	$ProductsInventoryBarcodes = Doctrine_Core::getTable('ProductsInventoryBarcodes');
	
	$addColumns = array();
	if ($ProductsInventoryBarcodes->hasColumn('attributes') === false){
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
		$DoctrineExport->alterTable($ProductsInventoryBarcodes->getTableName(), $commandArr);
	}
