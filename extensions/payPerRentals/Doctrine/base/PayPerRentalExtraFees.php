<?php

class PayPerRentalExtraFees extends Doctrine_Record {
	
	public function setUp(){

	}
	 
	public function setTableDefinition(){
		$this->setTableName('pay_per_rental_extrafees');
		
		$this->hasColumn('timefees_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
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