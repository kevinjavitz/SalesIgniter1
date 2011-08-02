<?php

class pointsRewardsOrderStatuses extends Doctrine_Record {
	public function setUp(){
		parent::setUp();
		$this->setUpParent();
		$this->hasOne('Orders', array(
									 'local' => 'orders_status_id',
									 'foreign' => 'orders_status'
								));
	}

	public function setUpParent(){
		$Orders = Doctrine::getTable('Orders')->getRecordInstance();
		$Orders->hasMany('pointsRewardsOrderStatuses', array(
															  'local' => 'orders_status',
															  'foreign' => 'orders_status_id'
														 ));
	}

	public function setTableDefinition(){
		$this->setTableName('pointsRewardsOrderStatuses');
		$this->hasColumn('orders_status_id', 'integer', 4, array(
																 'type' => 'integer',
																 'length' => 4,
																 'unsigned' => 0,
																 'primary' => true,
																 'notnull' => true,
																 'autoincrement' => false,
															));
	}
}



?>