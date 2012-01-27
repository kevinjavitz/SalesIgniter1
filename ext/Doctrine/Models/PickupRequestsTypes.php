<?php

class PickupRequestsTypes extends Doctrine_Record {

	public function setUp(){
		parent::setUp();

	}

	public function setUpParent(){

	}


	public function setTableDefinition(){
		$this->setTableName('pickup_requests_types');

		$this->hasColumn('pickup_requests_types_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => true,
				'autoincrement' => true,
		));


		$this->hasColumn('type_name', 'string', 255, array(
				'type' => 'string',
				'length' => 255,
				'primary' => false,
				'notnull' => true,
				'autoincrement' => false,
		));

	}
}
?>