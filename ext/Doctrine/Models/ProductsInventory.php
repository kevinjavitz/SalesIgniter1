<?php
/*
	Products Inventory Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductsInventory extends Doctrine_Record {

	public function setUp(){
		parent::setUp();

		$this->hasMany('Products', array(
			'local' => 'products_id',
			'foreign' => 'products_id',
			'cascade' => array('delete')
		));
		$this->setUpParent();
	}
	
	public function setUpParent(){
		$Products = Doctrine::getTable('Products')->getRecordInstance();
		
		$Products->hasMany('ProductsInventory', array(
			'local' => 'products_id',
			'foreign' => 'products_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('products_inventory');
		
		$this->hasColumn('inventory_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('products_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('track_method', 'string', 20, array(
			'type'          => 'string',
			'length'        => 20,
			'fixed'         => false,
			'primary'       => false,
			'default'       => 'quantity',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('type', 'string', 20, array(
			'type'          => 'string',
			'length'        => 20,
			'fixed'         => false,
			'primary'       => false,
			'default'       => 'new',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('controller', 'string', 32, array(
			'type'          => 'string',
			'length'        => 32,
			'fixed'         => false,
			'primary'       => false,
			'default'       => 'normal',
			'notnull'       => true,
			'autoincrement' => false
		));
	}
}