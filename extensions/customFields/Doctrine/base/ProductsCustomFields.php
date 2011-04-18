<?php
/*
	Products Custom Fields Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductsCustomFields extends Doctrine_Record {
	
	public function setUp(){
		$this->hasMany('ProductsCustomFieldsDescription', array(
			'local' => 'field_id',
			'foreign' => 'field_id',
			'cascade' => array('delete')
		));
		
		$this->hasMany('ProductsCustomFieldsOptionsToFields', array(
			'local'   => 'field_id',
			'foreign' => 'field_id',
			'cascade' => array('delete')
		));
		
		$this->hasMany('ProductsCustomFieldsToGroups', array(
			'local'   => 'field_id',
			'foreign' => 'field_id',
			'cascade' => array('delete')
		));
		
		$this->hasMany('ProductsCustomFieldsToProducts', array(
			'local' => 'field_id',
			'foreign' => 'field_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('products_custom_fields');
		
		$this->hasColumn('field_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('search_key', 'string', 16, array(
			'type' => 'string',
			'length' => 16,
			'fixed' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('show_on_site', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('show_on_listing', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));

		$this->hasColumn('show_name_on_listing', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('show_on_labels', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('labels_max_chars', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('input_type', 'string', 12, array(
			'type' => 'string',
			'length' => 12,
			'fixed' => false,
			'primary' => false,
			'default' => 'text',
			'notnull' => true,
			'autoincrement' => false,
		));
	}
}