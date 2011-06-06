<?php
/*
Rental Products Extension Version 1

I.T. Web Experts, Sales Igniter v1
http://www.itwebexperts.com

Copyright (c) 2011 I.T. Web Experts

This script and it's source is not redistributable
*/
class OrdersProductsRentals extends Doctrine_Record {

	public function setUp(){
		parent::setUp();
		$this->setUpParent();

		$this->hasOne('OrdersProducts', array(
			'local'   => 'orders_products_id',
			'foreign' => 'orders_products_id'
		));

		$this->hasOne('ProductsInventoryBarcodes', array(
			'local'   => 'barcode_id',
			'foreign' => 'barcode_id'
		));

		$this->hasOne('ProductsInventoryQuantity', array(
			'local'   => 'quantity_id',
			'foreign' => 'quantity_id'
		));
	}

	public function setUpParent(){
		$OrdersProducts = Doctrine_Core::getTable('OrdersProducts')->getRecordInstance();
		$ProductsInventoryBarcodes = Doctrine_Core::getTable('ProductsInventoryBarcodes')->getRecordInstance();
		$ProductsInventoryQuantity = Doctrine_Core::getTable('ProductsInventoryQuantity')->getRecordInstance();

		$OrdersProducts->hasMany('OrdersProductsRentals', array(
			'local' => 'orders_products_id',
			'foreign' => 'orders_products_id'
		));
		
		$ProductsInventoryBarcodes->hasMany('OrdersProductsRentals', array(
			'local'   => 'barcode_id',
			'foreign' => 'barcode_id'
		));
		
		$ProductsInventoryQuantity->hasMany('OrdersProductsRentals', array(
			'local'   => 'quantity_id',
			'foreign' => 'quantity_id'
		));
	}

	public function preInsert($event){
	}

	public function preUpdate($event){
		if ($this->rental_state == 'out'){
			$this->date_shipped = date('Y-m-d H:i:s');
		}elseif ($this->rental_state == 'returned'){
			$this->date_returned = date('Y-m-d H:i:s');
		}
	}

	public function setTableDefinition(){
		$this->setTableName('orders_products_rentals');

		$this->hasColumn('orders_products_rentals_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));
		$this->hasColumn('orders_products_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));

		$this->hasColumn('start_date', 'datetime', null, array(
			'type' => 'datetime',
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('end_date', 'datetime', null, array(
			'type' => 'datetime',
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
				
		$this->hasColumn('rental_state', 'string', 32, array(
			'type' => 'string',
			'length' => 32,
			'fixed' => false,
			'primary' => false,
			'default' => 'reserved',
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('date_shipped', 'date', null, array(
			'type' => 'date',
			'primary' => false,
			'default' => '0000-00-00',
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('date_returned', 'date', null, array(
			'type' => 'date',
			'primary' => false,
			'default' => '0000-00-00',
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('broken', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('quantity_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		$this->hasColumn('barcode_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
	}
}
?>