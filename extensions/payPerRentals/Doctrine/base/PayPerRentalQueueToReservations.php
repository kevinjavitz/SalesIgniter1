<?php

class PayPerRentalQueueToReservations extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
		$this->hasOne('OrdersProductsReservation', array(
				'local' => 'orders_products_reservations_id',
				'foreign' => 'orders_products_reservations_id'
			));

		$this->hasOne('QueueProductsReservation', array(
				'local' => 'queue_products_reservations_id',
				'foreign' => 'queue_products_reservations_id'
		));

	}

	public function setUpParent(){
		$OrdersProductsReservation = Doctrine_Core::getTable('OrdersProductsReservation')->getRecordInstance();
		$QueueProductsReservation = Doctrine_Core::getTable('QueueProductsReservation')->getRecordInstance();


		$OrdersProductsReservation->hasMany('PayPerRentalQueueToReservations', array(
				'local'   => 'orders_products_reservations_id',
				'foreign' => 'orders_products_reservations_id'
		));

		$QueueProductsReservation->hasMany('PayPerRentalQueueToReservations', array(
				'local'   => 'queue_products_reservations_id',
				'foreign' => 'queue_products_reservations_id'
		));
	}



	public function setTableDefinition(){
		$this->setTableName('pay_per_rental_queue_to_reservations');

		$this->hasColumn('ppr_queue_reservations_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));


		$this->hasColumn('queue_products_reservations_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0',
				'notnull' => true,
				'autoincrement' => false,
		));

		$this->hasColumn('orders_products_reservations_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0',
				'notnull' => true,
				'autoincrement' => false,
			));


	}
}
?>