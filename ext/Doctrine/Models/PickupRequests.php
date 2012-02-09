<?php

class PickupRequests extends Doctrine_Record {

	public function setUp(){
		parent::setUp();
		$this->setUpParent();
		$this->hasOne('PickupRequestsTypes', array(
				'local'   => 'pickup_requests_types_id',
				'foreign' => 'pickup_requests_types_id'
			));
	}

	public function setUpParent(){

		$PickupRequestsTypes = Doctrine_Core::getTable('PickupRequestsTypes')->getRecordInstance();
		$PickupRequestsTypes->hasMany('PickupRequests', array(
				'local' => 'pickup_requests_types_id',
				'foreign' => 'pickup_requests_types_id',
				'cascade' => array('delete')
			));
	}


	public function setTableDefinition(){
		$this->setTableName('pickup_requests');

		$this->hasColumn('pickup_requests_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => true,
				'autoincrement' => true,
		));

		$this->hasColumn('start_date', 'datetime', null, array(
				'type' => 'datetime',
				'primary' => false,
				'notnull' => true,
				'autoincrement' => false,
		));

		$this->hasColumn('pickup_requests_types_id', 'integer', 4, array(
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