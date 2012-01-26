<?php
/*
$Id: ProductsOptions.php

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class ProductsOptions extends Doctrine_Record {

	public function setUp(){
		$this->hasMany('ProductsOptionsToProductsOptionsGroups', array(
			'local' => 'products_options_id',
			'foreign' => 'products_options_id',
			'cascade' => array('delete')
		));
		$this->hasMany('ProductsOptionsValuesToProductsOptions', array(
				'local' => 'products_options_id',
				'foreign' => 'products_options_id',
				'cascade' => array('delete')
			));
	}
	
	public function setTableDefinition(){
		$this->setTableName('products_options');
		
		$this->hasColumn('products_options_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('option_type', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('use_image', 'integer', 1, array(
			'type'          => 'integer',
			'length'        => 1,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'autoincrement' => false,
		));
		
		$this->hasColumn('use_multi_image', 'integer', 1, array(
			'type'          => 'integer',
			'length'        => 1,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'autoincrement' => false,
		));
		
		$this->hasColumn('update_product_image', 'integer', 1, array(
			'type'          => 'integer',
			'length'        => 1,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '1',
			'autoincrement' => false,
		));
	}
}
?>