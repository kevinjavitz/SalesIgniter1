<?php
/*
	Inventory Centers Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductsInventoryBarcodesToInventoryCenters extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
		
		$this->hasOne('ProductsInventoryCenters', array(
			'local'   => 'inventory_center_id',
			'foreign' => 'inventory_center_id'
		));
	}
	
	public function setUpParent(){
		$productsInventoryBarcodes = Doctrine::getTable('ProductsInventoryBarcodes')->getRecordInstance();
		$productsInventoryCenters = Doctrine::getTable('ProductsInventoryCenters')->getRecordInstance();

		$productsInventoryBarcodes->hasOne('ProductsInventoryBarcodesToInventoryCenters', array(
			'local'   => 'barcode_id',
			'foreign' => 'barcode_id',
			'cascade' => array('delete')
		));
		
		$productsInventoryCenters->hasMany('ProductsInventoryBarcodesToInventoryCenters', array(
			'local'   => 'inventory_center_id',
			'foreign' => 'inventory_center_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('products_inventory_barcodes_to_inventory_centers');
		
		$this->hasColumn('barcode_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('inventory_center_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'notnull'       => true,
			'autoincrement' => false,
		));
	}
}