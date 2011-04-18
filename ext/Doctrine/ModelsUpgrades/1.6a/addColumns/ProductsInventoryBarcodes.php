<?php
	$ProductsInventoryBarcodes = Doctrine_Core::getTable('ProductsInventoryBarcodes')->getTableName();
	
	$addColumns = array();
	if ($DoctrineImport->tableColumnExists($ProductsInventoryBarcodes, 'attributes') === false){
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
		$DoctrineExport->alterTable($ProductsInventoryBarcodes, $commandArr);
	}
