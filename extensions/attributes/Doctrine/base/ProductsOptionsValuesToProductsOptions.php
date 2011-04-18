<?php
/*
$Id: ProductsOptionsValuesToProductsOptions.php

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class ProductsOptionsValuesToProductsOptions extends Doctrine_Record {
	
	public function setUp(){
		$ProductsOptions = Doctrine::getTable('ProductsOptions')->getRecordInstance();
		$ProductsOptionsValues = Doctrine::getTable('ProductsOptionsValues')->getRecordInstance();
		
		$ProductsOptions->hasMany('ProductsOptionsValuesToProductsOptions', array(
			'local'   => 'products_options_id',
			'foreign' => 'products_options_id',
			'cascade' => array('delete')
		));
		
		$ProductsOptionsValues->hasMany('ProductsOptionsValuesToProductsOptions', array(
			'local'   => 'products_options_values_id',
			'foreign' => 'products_options_values_id',
			'cascade' => array('delete')
		));
		
		$this->hasOne('ProductsOptions', array(
			'local'   => 'products_options_id',
			'foreign' => 'products_options_id'
		));
		
		$this->hasOne('ProductsOptionsValues', array(
			'local'   => 'products_options_values_id',
			'foreign' => 'products_options_values_id'
		));
	}
	
	public function setTableDefinition() {
		$this->setTableName('products_options_values_to_products_options');
		
		$this->hasColumn('products_options_values_to_products_options_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
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
		
		$this->hasColumn('products_options_values_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('sort_order', 'integer', 2, array(
			'type'          => 'integer',
			'length'        => 2,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
	}
}
?>