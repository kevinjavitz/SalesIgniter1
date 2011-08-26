<?php
/*
	Inventory Centers Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductQtyToEvents extends Doctrine_Record {
	
	public function setUp(){
		$this->setUpParent();

		$this->hasOne('PayPerRentalEvents', array(
				'local' => 'events_id',
				'foreign' => 'events_id'
		));
	}

	public function setUpParent(){
		$PayPerRentalEvents = Doctrine::getTable('PayPerRentalEvents')->getRecordInstance();

		$PayPerRentalEvents->hasMany('ProductQtyToEvents', array(
				'local' => 'events_id',
				'foreign' => 'events_id',
				'cascade' => array('delete')
			));
	}
	 
	public function setTableDefinition(){
		$this->setTableName('product_qty_to_events');
		
		$this->hasColumn('product_event_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));

		$this->hasColumn('events_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

		$this->hasColumn('qty', 'integer', 4, array(
				'type'          => 'integer',
				'length'        => 4,
				'fixed'         => false,
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false,
		));

		$this->hasColumn('products_model', 'string', 250, array(
				'type'          => 'string',
				'length'        => 250,
				'fixed'         => false,
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false,
		));





	}
}