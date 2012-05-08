<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class StoresExtraFees extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
		$this->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'stores_id');
	}
	
	public function setUpParent(){
		$PayPerRentalExtraFees = Doctrine_Core::getTable('PayPerRentalExtraFees')->getRecordInstance();
		$Stores = Doctrine_Core::getTable('Stores')->getRecordInstance();

		$PayPerRentalExtraFees->hasMany('StoresExtraFees', array(
			'local'   => 'timefees_id',
			'foreign' => 'timefees_id',
			'cascade' => array('delete')
		));
		
		$Stores->hasMany('StoresExtraFees', array(
			'local'   => 'stores_id',
			'foreign' => 'stores_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('stores_extrafees');
		
		$this->hasColumn('stores_extrafees_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));

		$this->hasColumn('timefees_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('stores_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('show_method', 'string', 16, array(
			'type'          => 'string',
			'length'        => 16,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
		$this->hasColumn('timefees_fee', 'decimal', 15, array(
				'type' => 'decimal',
				'length' => 15,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0.0000',
				'notnull' => true,
				'autoincrement' => false,
				'scale' => false,
			));


		$this->hasColumn('timefees_hours', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0',
				'notnull' => true,
				'autoincrement' => false,
			));

		$this->hasColumn('timefees_mandatory', 'integer', 1, array(
				'type' => 'integer',
				'length' => 1,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0',
				'notnull' => true,
				'autoincrement' => false,
			));

		$this->hasColumn('timefees_name', 'string', 128, array(
				'type'          => 'string',
				'length'        => 128,
				'fixed'         => false,
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false,
			));
		$this->hasColumn('timefees_description', 'string', null, array(
				'type'          => 'string',
				'length'        => null,
				'fixed'         => false,
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false,
			));
	}
}