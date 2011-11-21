<?php
/*
	Quantity Discount Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductsQuantityDiscounts extends Doctrine_Record {
	
	public function setUp(){
		$this->setUpParent();
		
		$this->hasOne('Products', array(
			'local' => 'products_id',
			'foreign' => 'products_id'
		));
	}
	
	public function setUpParent(){
		$Products = Doctrine::getTable('Products')->getRecordInstance();
		
		$Products->hasMany('ProductsQuantityDiscounts', array(
			'local' => 'products_id',
			'foreign' => 'products_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('products_quantity_discounts');
		
		$this->hasColumn('products_quantity_discount_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('products_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('quantity_from', 'integer', 3, array(
			'type' => 'integer',
			'length' => 3,
			'unsigned' => 0,
			'primary' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('quantity_to', 'integer', 3, array(
			'type' => 'integer',
			'length' => 3,
			'unsigned' => 0,
			'primary' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('price', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => 4,
		));
		
		$this->hasColumn('purchase_type', 'string', 16, array(
			'type' => 'string',
			'length' => 16,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
	}
}