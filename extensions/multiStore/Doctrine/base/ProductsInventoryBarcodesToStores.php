<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductsInventoryBarcodesToStores extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
		
		$this->hasOne('Stores', array(
			'local' => 'inventory_store_id',
			'foreign' => 'stores_id'
		));
	}
	
	public function setUpParent(){
		$ProductsInventoryBarcodes = Doctrine::getTable('ProductsInventoryBarcodes')->getRecordInstance();
		$Stores = Doctrine::getTable('Stores')->getRecordInstance();

		$ProductsInventoryBarcodes->hasOne('ProductsInventoryBarcodesToStores', array(
			'local'   => 'barcode_id',
			'foreign' => 'barcode_id',
			'cascade' => array('delete')
		));
		
		$Stores->hasMany('ProductsInventoryBarcodesToStores', array(
			'local'   => 'stores_id',
			'foreign' => 'inventory_store_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('products_inventory_barcodes_to_stores');
		
		$this->hasColumn('barcode_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('inventory_store_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'notnull'       => true,
			'autoincrement' => false,
		));
	}
}