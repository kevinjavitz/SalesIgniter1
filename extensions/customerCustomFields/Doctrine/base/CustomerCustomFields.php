<?php
/*
	Customer Custom Fields Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class CustomerCustomFields extends Doctrine_Record {
	
	public function setUp(){
		$this->hasMany('CustomerCustomFieldsDescription', array(
			'local' => 'field_id',
			'foreign' => 'field_id',
			'cascade' => array('delete')
		));
		
		$this->hasMany('CustomerCustomFieldsOptionsToFields', array(
			'local'   => 'field_id',
			'foreign' => 'field_id',
			'cascade' => array('delete')
		));
		
		$this->hasMany('CustomerCustomFieldsToGroups', array(
			'local'   => 'field_id',
			'foreign' => 'field_id',
			'cascade' => array('delete')
		));
		
		$this->hasMany('CustomerCustomFieldsToCustomers', array(
			'local' => 'field_id',
			'foreign' => 'field_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('customer_custom_fields');
		
		$this->hasColumn('field_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));

		$this->hasColumn('placeholder', 'string', null, array(
			'type' => 'string',
			'length' => null,
			'fixed' => false,
			'primary' => false,
			'default' => 'text',
			'notnull' => true,
			'autoincrement' => false,
		));

		$this->hasColumn('pattern', 'string', null, array(
			'type' => 'string',
			'length' => null,
			'fixed' => false,
			'primary' => false,
			'default' => 'text',
			'notnull' => true,
			'autoincrement' => false,
		));

		$this->hasColumn('custom_message', 'string', null, array(
			'type' => 'string',
			'length' => null,
			'fixed' => false,
			'primary' => false,
			'default' => 'text',
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

		$this->hasColumn('required', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('autofocus', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('novalidate', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('min', 'integer', 3, array(
			'type' => 'integer',
			'length' => 3,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('max', 'integer', 3, array(
			'type' => 'integer',
			'length' => 3,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
	}
}