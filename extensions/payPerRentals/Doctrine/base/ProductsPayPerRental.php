<?php
/*
Pay Per Rental Products Extension Version 1

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class ProductsPayPerRental extends Doctrine_Record {
	
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
		
		$Products->hasOne('ProductsPayPerRental', array(
			'local' => 'products_id',
			'foreign' => 'products_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('products_pay_per_rental');
		
		$this->hasColumn('pay_per_rental_id', 'integer', 4, array(
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
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('price_daily', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => false,
		));
		
		$this->hasColumn('price_weekly', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => false,
		));
		
		$this->hasColumn('price_monthly', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => false,
		));
		
		$this->hasColumn('price_six_month', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => false,
		));
		
		$this->hasColumn('price_year', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => false,
		));
		
		$this->hasColumn('price_three_year', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => false,
		));
		
		$this->hasColumn('quantity', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('combo_products', 'string', null, array(
			'type' => 'string',
			'fixed' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('combo_price', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => false,
		));
		
		$this->hasColumn('max_months', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('max_days', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('shipping', 'string', null, array(
			'type' => 'string',
			'fixed' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('overbooking', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('deposit_amount', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => false,
		));

		$this->hasColumn('insurance', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => false,
		));
		
        $this->hasColumn('min_rental_days', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));

		$this->hasColumn('min_period', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));

		$this->hasColumn('max_period', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));

		$this->hasColumn('min_type', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));

		$this->hasColumn('max_type', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));

	}
}