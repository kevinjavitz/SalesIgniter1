<?php
/*
	Products Custom Fields Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductsCustomFieldsGroupsToProducts extends Doctrine_Record {

	public function setUp(){
		$Products = Doctrine::getTable('Products')->getRecordInstance();
		
		$Products->hasMany('ProductsCustomFieldsGroupsToProducts', array(
			'local'   => 'products_id',
			'foreign' => 'product_id',
			'cascade' => array('delete')
		));
		
		$this->hasOne('ProductsCustomFieldsGroups', array(
			'local' => 'group_id',
			'foreign' => 'group_id'
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('products_custom_fields_groups_to_products');
		
		$this->hasColumn('group_id', 'integer', 4, array(
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
	}
}