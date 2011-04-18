<?php
/*
	CustomersBasket Table

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class CustomersBasket extends Doctrine_Record {
	
	public function setUp(){
		$this->setUpParent();
	}
	
	public function setUpParent(){
		$Customers = Doctrine::getTable('Customers')->getRecordInstance();
		$Products = Doctrine::getTable('Products')->getRecordInstance();
		
		$Customers->hasMany('CustomersBasket', array(
			'local' => 'customers_id',
			'foreign' => 'customers_id',
			'cascade' => array('delete')
		));
		
		$Products->hasMany('CustomersBasket', array(
			'local' => 'products_id',
			'foreign' => 'products_id',
			'cascade' => array('delete')
		));
	}
	
	public function preInsert($event){
		$this->customers_basket_date_added = date('Y-m-d H:i:s');
	}
	
	public function setTableDefinition(){
		$this->setTableName('customers_basket');
		
		$this->hasColumn('customers_basket_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('customers_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('products_id', 'string', null, array(
			'type'          => 'string',
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('customers_basket_quantity', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('customers_basket_date_added', 'date', null, array(
			'type'          => 'date',
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('purchase_type', 'string', 12, array(
			'type'          => 'string',
			'length'        => 12,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
	}
}