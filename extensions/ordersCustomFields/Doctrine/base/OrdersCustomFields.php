<?php
/*
	Orders Custom Fields Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class OrdersCustomFields extends Doctrine_Record {
	
	public function setUp(){
		$this->hasMany('OrdersCustomFieldsDescription', array(
			'local' => 'field_id',
			'foreign' => 'field_id',
			'cascade' => array('delete')
		));
		
		$this->hasMany('OrdersCustomFieldsOptionsToFields', array(
			'local'   => 'field_id',
			'foreign' => 'field_id',
			'cascade' => array('delete')
		));
		
		$this->hasMany('OrdersCustomFieldsToOrders', array(
			'local' => 'field_id',
			'foreign' => 'field_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('orders_custom_fields');
		
		$this->hasColumn('field_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
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
		
		$this->hasColumn('input_required', 'int', 1, array(
			'type' => 'int',
			'length' => 1,
			'fixed' => false,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));

		$this->hasColumn('sort_order', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'fixed' => false,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
	}
}