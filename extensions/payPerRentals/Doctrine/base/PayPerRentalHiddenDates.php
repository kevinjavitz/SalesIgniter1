<?php
/*
Pay Per Rental Products Extension Version 1

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class PayPerRentalHiddenDates extends Doctrine_Record {
	
	public function setUp(){
		$this->setUpParent();
		$this->hasOne('Products', array(
			'local' => 'products_id',
			'foreign' => 'products_id'
		));
	}

	public function setUpParent(){
		$Products = Doctrine::getTable('Products')->getRecordInstance();

		$Products->hasMany('PayPerRentalHiddenDates', array(
			'local' => 'products_id',
			'foreign' => 'products_id'
		));
	}

	public function setTableDefinition(){
		$this->setTableName('pay_per_rental_hidden_dates');
		
		$this->hasColumn('hidden_dates_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));

		$this->hasColumn('hidden_start_date', 'datetime', null, array(
			'type'          => 'datetime',
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false
		));

		$this->hasColumn('hidden_end_date', 'datetime', null, array(
			'type'          => 'datetime',
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false
		));

		$this->hasColumn('products_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));


		/*$this->hasColumn('recurring', 'int', 1, array(
			'type'          => 'string',
			'length'        => 1,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));*/

	}
}