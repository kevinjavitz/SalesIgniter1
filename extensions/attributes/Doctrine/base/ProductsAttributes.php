<?php
/*
$Id: ProductsAttributes.php

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class ProductsAttributes extends Doctrine_Record {

	public function setUp(){
		$this->hasOne('ProductsOptions', array(
			'local'   => 'options_id',
			'foreign' => 'products_options_id'
		));
		
		$this->hasOne('ProductsOptionsGroups', array(
			'local' => 'groups_id',
			'foreign' => 'products_options_groups_id'
		));
		
		$Products = Doctrine::getTable('Products')->getRecordInstance();
		$Products->hasMany('ProductsAttributes', array(
			'local'   => 'products_id',
			'foreign' => 'products_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('products_attributes');
		
		$this->hasColumn('products_attributes_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('products_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('groups_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('options_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('options_values_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('options_values_price', 'decimal', 15, array(
			'type'          => 'decimal',
			'length'        => 15,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0.0000',
			'notnull'       => true,
			'autoincrement' => false,
			'scale'         => false
		));
		
		$this->hasColumn('options_values_image', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
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
		
		$this->hasColumn('sort_order', 'integer', 6, array(
			'type'          => 'integer',
			'length'        => 6,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('purchase_types', 'string', 999, array(
			'type'          => 'string',
			'length'        => 999,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('use_inventory', 'integer', 1, array(
			'type'          => 'integer',
			'length'        => 1,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
	}
}
?>