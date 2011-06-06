<?php
/*
Rental Products Extension Version 1

I.T. Web Experts, Sales Igniter v1
http://www.itwebexperts.com

Copyright (c) 2011 I.T. Web Experts

This script and it's source is not redistributable
*/

class ProductsRentalSettings extends Doctrine_Record {
	
	public function setUp(){
		parent::setUp();
		$this->setUpParent();
		
		$this->hasOne('Products', array(
			'local' => 'products_id',
			'foreign' => 'products_id'
		));
	}
	
	public function setUpParent(){
		$Products = Doctrine::getTable('Products')->getRecordInstance();
		
		$Products->hasOne('ProductsRentalSettings', array(
			'local' => 'products_id',
			'foreign' => 'products_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('products_rental_settings');
		
		$this->hasColumn('rental_id', 'integer', 4, array(
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
			'default' => '0',
			'notnull' => true,
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
			'scale' => false,
		));

		$this->hasColumn('rental_period', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
	}
}