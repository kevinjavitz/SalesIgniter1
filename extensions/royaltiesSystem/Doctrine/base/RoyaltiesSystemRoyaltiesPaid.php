<?php

/**
 * RoyaltiesSystemRoyaltiesPaid
 *
*/
class RoyaltiesSystemRoyaltiesPaid extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();

		$this->hasOne('Customers', array(
		                                'local' => 'content_provider_id',
		                                'foreign' => 'customers_id'
		                           ));
	}

	public function setUpParent(){
		$Customers = Doctrine::getTable('Customers')->getRecordInstance();

		$Customers->hasMany('RoyaltiesSystemRoyaltiesPaid', array(
		                                                            'local' => 'customers_id',
		                                                            'foreign' => 'content_provider_id',
		                                                            'cascade' => array('delete')
		                                                       ));
	}

	public function setTableDefinition(){
		$this->setTableName('royalties_royalty_royalties_paid');

		$this->hasColumn('royalties_royalty_royalties_paid_id', 'integer', 4, array(
		                                                           'type' => 'integer',
		                                                           'length' => 4,
		                                                           'unsigned' => 0,
		                                                           'primary' => true,
		                                                           'notnull' => true,
		                                                           'autoincrement' => true,
		                                                      ));
		$this->hasColumn('content_provider_id', 'integer', 4, array(
		                                                           'type' => 'integer',
		                                                           'length' => 4,
		                                                           'unsigned' => 0,
		                                                           'primary' => false,
		                                                           'notnull' => false,
		                                                           'autoincrement' => false,
		                                                      ));
		$this->hasColumn('royalty_amount_paid', 'float', 15, array(
		                                              'type' => 'float',
		                                              'length' => 15,
		                                              'unsigned' => 0,
		                                              'primary' => false,
		                                              'notnull' => true,
		                                              'autoincrement' => false,
		                                         ));
		$this->hasColumn('royalty_payment_date', 'timestamp', null, array(
		                                                                 'type' => 'timestamp',
		                                                                 'default' =>'0000-00-00 00:00:00',
		                                                                 'primary' => false,
		                                                                 'notnull' => true,
		                                                                 'autoincrement' => false,
		                                                            ));
	}
}