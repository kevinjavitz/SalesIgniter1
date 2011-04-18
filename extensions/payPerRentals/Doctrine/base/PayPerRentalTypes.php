<?php
/*
Pay Per Rental Products Extension Version 1

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class PayPerRentalTypes extends Doctrine_Record {
	
	public function setUp(){

	}

	
	public function setTableDefinition(){
		$this->setTableName('pay_per_rental_types');
		
		$this->hasColumn('pay_per_rental_types_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));

        $this->hasColumn('minutes', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));

		$this->hasColumn('pay_per_rental_types_name', 'string', 64, array(
		'type' => 'string',
		'length' => 64,
		'fixed' => false,
		'primary' => false,
		'default' => '',
		'notnull' => true,
		'autoincrement' => false,
		));
	}
}