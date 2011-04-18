<?php
/*
$Id: ProductsOptionsValues.php

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class ProductsOptionsValues extends Doctrine_Record {
	
	public function setUp(){
		$ProductsAttributes = Doctrine_Core::getTable('ProductsAttributes')->getRecordInstance();
		$OrdersProductsAttributes = Doctrine_Core::getTable('OrdersProductsAttributes')->getRecordInstance();
		
		$ProductsAttributes->hasOne('ProductsOptionsValues', array(
			'local'   => 'options_values_id',
			'foreign' => 'products_options_values_id'
		));
		
		$OrdersProductsAttributes->hasOne('ProductsOptionsValues', array(
			'local'   => 'options_values_id',
			'foreign' => 'products_options_values_id'
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('products_options_values');

		$this->hasColumn('products_options_values_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
	}
}
?>