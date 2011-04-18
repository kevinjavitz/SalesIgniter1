<?php
/*
	Quantity Discount Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class RoyaltiesSystemProvidersFees extends Doctrine_Record {
	
	public function setUp(){
		$this->setUpParent();
		
		$this->hasOne('Customers', array(
			'local' => 'customers_id',
			'foreign' => 'customers_id'
		));
	}
	
	public function setUpParent(){
		$Customers = Doctrine::getTable('Customers')->getRecordInstance();
		
		$Customers->hasOne('RoyaltiesSystemProvidersFees', array(
			'local' => 'customers_id',
			'foreign' => 'customers_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('royalties_system_providers_fees');

		$this->hasColumn('customers_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'notnull' => true,
			'autoincrement' => false,
		));

		$this->hasColumn('royalty_total', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => false,
		));

		/*$this->hasColumn('payed_total', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => false,
		));

		//it should have a last payed amount...actually all the transactions should be kept in a separate table

		$this->hasColumn('last_payed_date', 'timestamp', null, array(
			'type'          => 'timestamp',
			'default'       => '0000-00-00 00:00:00',
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));*/
	}
}