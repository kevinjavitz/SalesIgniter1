<?php
/*
Pay Per Rental Products Extension Version 1

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class PricePerRentalPerProducts extends Doctrine_Record {
	
	public function setUp(){
		$this->setUpParent();
		
		$this->hasOne('ProductsPayPerRental', array(
			'local' => 'pay_per_rental_id',
			'foreign' => 'pay_per_rental_id'
		));
		 $this->hasMany('PricePayPerRentalPerProductsDescription', array(
			'local' => 'price_per_rental_per_products_id',
			'foreign' => 'price_per_rental_per_products_id',
			'cascade' => array('delete')
		));
	}
	
	public function setUpParent(){
		$Products = Doctrine::getTable('ProductsPayPerRental')->getRecordInstance();
		
		$Products->hasOne('PricePerRentalPerProducts', array(
			'local' => 'pay_per_rental_id',
			'foreign' => 'pay_per_rental_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('price_per_rental_per_products');
		
		$this->hasColumn('price_per_rental_per_products_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('pay_per_rental_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('number_of', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
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


        $this->hasColumn('pay_per_rental_types_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
	}
}