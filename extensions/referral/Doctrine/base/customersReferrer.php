<?php
class customersReferrer extends Doctrine_Record {
	public function setUp(){
		$this->setUpParent();
		parent::setUp();
		$this->hasOne('Customers', array(
		                               'local' => 'customers_id',
		                               'foreign' => 'customers_id'
		                          ));
	}

	public function setUpParent(){
		$Customers = Doctrine::getTable('Customers')->getRecordInstance();

		$Customers->hasMany('customersReferrer', array(
													  'local' => 'customers_id',
													  'foreign' => 'customers_id'
												 ));
	}

	public function setTableDefinition(){
		$this->setTableName('customersReferrer');
		$this->hasColumn('customers_id', 'integer', 4, array(
		                                                     'type' => 'integer',
		                                                     'length' => 4,
		                                                     'default' => '',
		                                                     'primary' => true,
		                                                     'notnull' => true,
		                                                ));
		$this->hasColumn('referrer_code', 'string', 80, array(
		                                                   'type' => 'string',
															'default' => '',
															'length' => 80,
		                                                   'primary' => false,
		                                                   'notnull' => true
		                                              ));
	}
}

