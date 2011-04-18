<?php
	$ProductsListing = Doctrine_Core::getTable('ProductsListing')->getTableName();
	
	$addColumns = array();
	if ($DoctrineImport->tableColumnExists($ProductsListing, 'products_listing_default_sorting') === false){
		$addColumns['products_listing_default_sorting'] = array(
			'type' => 'integer',
			'length' => 1,
			'default' => '0',
			'primary' => false,
			'autoincrement' => false
		);
	}
	
	$commandArr = array();	
	if (!empty($addColumns)){
		$commandArr['add'] = $addColumns;
	}
	
	if (!empty($commandArr)){
		$DoctrineExport->alterTable($ProductsListing, $commandArr);
	}
