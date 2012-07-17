<?php
/*
	Customer Custom Fields Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class CustomerCustomFieldsToCustomers extends Doctrine_Record {
	
	public function setUp(){
		parent::setUp();
		$this->setUpParent();

		$this->hasOne('CustomerCustomFields', array(
			'local' => 'field_id',
			'foreign' => 'field_id'
		));
	}

	public function setUpParent(){
		$Customers = Doctrine::getTable('Customers')->getRecordInstance();

		$Customers->hasMany('CustomerCustomFieldsToCustomers', array(
			'local' => 'customers_id',
			'foreign' => 'customers_id',
			'cascade' => array('delete')
		));
		Doctrine_Core::initializeModels(array('Customers'));
		
	}
	
	public function setTableDefinition(){
		$this->setTableName('customer_custom_fields_to_customers');
		
		$this->hasColumn('field_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('customers_id', 'integer', 4, array(
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