<?php
class RoyaltiesSystemOrderStatuses extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();

		$this->hasOne('Orders', array(
		                               'local' => 'orders_status_id',
		                               'foreign' => 'orders_status'
		                          ));
	}

	public function setUpParent(){
		$Orders = Doctrine::getTable('Orders')->getRecordInstance();

		$Orders->hasMany('RoyaltiesSystemOrderStatuses', array(
																'local' => 'orders_status',
																'foreign' => 'orders_status_id'
														   ));
	}

	public function setTableDefinition(){
		$this->setTableName('royaltiesSystemOrderStatuses');
		$this->hasColumn('orders_status_id', 'integer', 11, array(
		                                                   'type' => 'integer',
		                                                   'length' => 11,
		                                                   'unsigned' => 0,
		                                                   'primary' => true,
		                                                   'notnull' => true,
		                                                   'autoincrement' => false,
		                                              ));
	}
}

