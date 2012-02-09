<?php

class RentedProductsToPickupRequests extends Doctrine_Record {

	public function setUp(){
		parent::setUp();
		$this->setUpParent();
		$this->hasOne('RentedQueue', array(
				'local'   => 'rented_products_id',
				'foreign' => 'customers_queue_id'
		));
		$this->hasOne('PickupRequests', array(
				'local'   => 'pickup_requests_id',
				'foreign' => 'pickup_requests_id'
		));
	}

	public function setUpParent(){
		$RentedProducts = Doctrine_Core::getTable('RentedQueue')->getRecordInstance();
		$RentedProducts->hasMany('RentedProductsToPickupRequests', array(
				'local' => 'customers_queue_id',
				'foreign' => 'rented_products_id',
				'cascade' => array('delete')
		));
		$PickupRequests = Doctrine_Core::getTable('PickupRequests')->getRecordInstance();
		$PickupRequests->hasMany('RentedProductsToPickupRequests', array(
				'local' => 'pickup_requests_id',
				'foreign' => 'pickup_requests_id',
				'cascade' => array('delete')
		));
	}


	public function setTableDefinition(){
		$this->setTableName('rented_products_pickup_requests');

		$this->hasColumn('rented_products_pickup_requests_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => true,
				'autoincrement' => true,
		));

		$this->hasColumn('rented_products_id', 'integer', 4, array(
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