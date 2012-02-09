<?php

class CustomersToPickupRequests extends Doctrine_Record {

	public function setUp(){
		parent::setUp();
		$this->setUpParent();
		$this->hasOne('Customers', array(
				'local'   => 'customers_id',
				'foreign' => 'customers_id'
		));
		$this->hasOne('PickupRequests', array(
				'local'   => 'pickup_requests_id',
				'foreign' => 'pickup_requests_id'
		));
	}

	public function setUpParent(){
		$Customers = Doctrine_Core::getTable('Customers')->getRecordInstance();
		$Customers->hasMany('CustomersToPickupRequests', array(
				'local' => 'customers_id',
				'foreign' => 'customers_id',
				'cascade' => array('delete')
		));
		$PickupRequests = Doctrine_Core::getTable('PickupRequests')->getRecordInstance();
		$PickupRequests->hasMany('CustomersToPickupRequests', array(
				'local' => 'pickup_requests_id',
				'foreign' => 'pickup_requests_id',
				'cascade' => array('delete')
		));
	}


	public function setTableDefinition(){
		$this->setTableName('customers_pickup_requests');

		$this->hasColumn('customers_pickup_requests_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => true,
				'autoincrement' => true,
		));

		$this->hasColumn('customers_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'default' => 0,
				'primary' => false,
				'notnull' => false,
				'autoincrement' => false,
			));

		$this->hasColumn('pickup_requests_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'default' => 0,
				'primary' => false,
				'notnull' => false,
				'autoincrement' => false,
		));

	}
}
?>