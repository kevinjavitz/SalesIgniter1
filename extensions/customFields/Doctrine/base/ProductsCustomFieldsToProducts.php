<?php
/*
	Products Custom Fields Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductsCustomFieldsToProducts extends Doctrine_Record {
	
	public function setUp(){
		$this->setUpParent();
		
		$this->hasOne('ProductsCustomFields', array(
			'local' => 'field_id',
			'foreign' => 'field_id'
		));
	}

	public function setUpParent(){
		$Products = Doctrine::getTable('Products')->getRecordInstance();
		
		$Products->hasMany('ProductsCustomFieldsToProducts', array(
			'local' => 'products_id',
			'foreign' => 'product_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('products_custom_fields_to_products');
		
		$this->hasColumn('field_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('product_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('value', 'string', null, array(
			'type' => 'string',
			'fixed' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('field_type', 'string', 16, array(
			'type' => 'string',
			'length' => 16,
			'fixed' => false,
			'primary' => false,
			'default' => 'text',
			'notnull' => true,
			'autoincrement' => false,
		));
	}
}