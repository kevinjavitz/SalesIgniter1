<?php
	$ProductsListing = Doctrine_Core::getTable('ProductsListing');
	
	$addColumns = array();
	if ($ProductsListing->hasColumn('products_listing_default_sorting') === false){
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
		$DoctrineExport->alterTable($ProductsListing->getTableName(), $commandArr);
	}
