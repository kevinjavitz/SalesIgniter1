<?php
/*
$Id: OrdersProductsAttributes.php

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class OrdersProductsAttributes extends Doctrine_Record {
	
	public function setUp(){
		$this->hasOne('ProductsOptions', array(
			'local'   => 'options_id',
			'foreign' => 'products_options_id'
		));
		
		$OrdersProducts = Doctrine_Core::getTable('OrdersProducts')->getRecordInstance();
		
		$OrdersProducts->hasMany('OrdersProductsAttributes', array(
			'local'   => 'orders_products_id',
			'foreign' => 'orders_products_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition() {
		$this->setTableName('orders_products_attributes');
		
		$this->hasColumn('orders_products_attributes_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('orders_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('orders_products_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('products_options', 'string', 32, array(
			'type'          => 'string',
			'length'        => 32,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('products_options_values', 'string', 32, array(
			'type'          => 'string',
			'length'        => 32,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('options_values_price', 'decimal', 15, array(
			'type'          => 'decimal',
			'length'        => 15,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0.0000',
			'notnull'       => true,
			'autoincrement' => false,
			'scale'         => 4,
		));
		
		$this->hasColumn('price_prefix', 'string', 1, array(
			'type'          => 'string',
			'length'        => 1,
			'fixed'         => true,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('options_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('options_values_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
	}
}
?>