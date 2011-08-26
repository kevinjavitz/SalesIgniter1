<?php
/*
	Inventory Centers Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PayPerRentalEvents extends Doctrine_Record {
	
	public function setUp(){

	}
	 
	public function setTableDefinition(){
		$this->setTableName('pay_per_rental_events');
		
		$this->hasColumn('events_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));

		$this->hasColumn('events_date', 'datetime', null, array(
			'type'          => 'datetime',
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false
		));

		$this->hasColumn('shipping', 'string', null, array(
			'type'          => 'string',			
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

		$this->hasColumn('gates', 'string', null, array(
				'type'          => 'string',
				'fixed'         => false,
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false,
		));

		$this->hasColumn('default_gate', 'integer', 4, array(
				'type'          => 'integer',
				'length'        => 4,
				'fixed'         => false,
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false,
		));

		$this->hasColumn('events_days', 'integer', 4, array(
				'type'          => 'integer',
				'length'        => 4,
				'fixed'         => false,
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false,
		));

		$this->hasColumn('events_name', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));


		$this->hasColumn('events_details', 'string', null, array(
			'type'          => 'string',
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

        $this->hasColumn('events_state', 'string', 100, array(
			'type'          => 'string',
            'length'        => 100,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

        $this->hasColumn('events_country_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

         $this->hasColumn('events_zone_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

	}
}