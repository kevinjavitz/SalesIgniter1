<?php
/*
	Products Inventory Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductsInventoryQuantity extends Doctrine_Record {

	public function setUp(){
		parent::setUp();
		$this->setUpParent();
		
		$this->hasOne('ProductsInventory', array(
			'local'   => 'inventory_id',
			'foreign' => 'inventory_id'
		));
	}
	
	public function setUpParent(){
		$ProductsInventory = Doctrine::getTable('ProductsInventory')->getRecordInstance();
		
		$ProductsInventory->hasMany('ProductsInventoryQuantity', array(
			'local' => 'inventory_id',
			'foreign' => 'inventory_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('products_inventory_quantity');
		
		$this->hasColumn('quantity_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('inventory_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('available', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('qty_out', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('broken', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('purchased', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('attributes', 'string', 999, array(
			'type'          => 'string',
			'length'        => 999,
			'primary'       => false,
			'default'       => null,
			'notnull'       => false,
			'autoincrement' => false,
		));
	}
}