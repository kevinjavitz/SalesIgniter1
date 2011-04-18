<?php
/*
$Id: CustomersBasketAttributes.php

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class CustomersBasketAttributes extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
	}
	
	public function setUpParent(){
		$CustomersBasket = Doctrine::getTable('CustomersBasket')->getRecordInstance();
		$CustomersBasket->hasMany('CustomersBasketAttributes', array(
			'local'   => 'customers_basket_id',
			'foreign' => 'customers_basket_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('customers_basket_attributes');
		
		$this->hasColumn('customers_basket_attributes_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('customers_basket_id', 'integer', 11, array(
			'type'          => 'integer',
			'length'        => 11,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('products_options_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('products_options_value_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
	}
}