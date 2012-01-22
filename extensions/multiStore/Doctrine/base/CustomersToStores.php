<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class CustomersToStores extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
	}
	
	public function setUpParent(){
		$Customers = Doctrine::getTable('Customers')->getRecordInstance();
		$Stores = Doctrine::getTable('Stores')->getRecordInstance();

		$Customers->hasOne('CustomersToStores', array(
			'local'   => 'customers_id',
			'foreign' => 'customers_id',
			'cascade' => array('delete')
		));

		$Stores->hasMany('CustomersToStores', array(
			'local'   => 'stores_id',
			'foreign' => 'stores_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('customers_to_stores');
		
		$this->hasColumn('customers_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('stores_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
	}
}