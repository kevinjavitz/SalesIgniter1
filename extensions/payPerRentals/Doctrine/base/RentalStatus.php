<?php
/*
	Inventory Centers Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class RentalStatus extends Doctrine_Record {
	
	public function setUp(){

	}
	 
	public function setTableDefinition(){
		$this->setTableName('rental_status');
		
		$this->hasColumn('rental_status_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));

		$this->hasColumn('rental_status_text', 'string', 250, array(
			'type'          => 'string',
			'length'        => 250,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));


		$this->hasColumn('rental_status_color', 'string', 20, array(
			'type'          => 'string',
			'length'        => 20,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

		$this->hasColumn('rental_status_available', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));

	}
}