<?php
class ModulesShippingZoneReservationMethods extends Doctrine_Record {

	public function setUp(){
		$this->hasMany('ModulesShippingZoneReservationMethodsDescription', array(
			'local' => 'method_id',
			'foreign' => 'method_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('modules_shipping_zone_reservation_methods');
		
		$this->hasColumn('method_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('method_status', 'string', 16, array(
			'type'          => 'string',
			'length'        => 16,
			'fixed'         => false,
			'primary'       => false,
			'default'       => 'False',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('method_zipcode', 'string', null, array(
			'type'          => 'string',
			'length'        => null,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('method_cost', 'float', 15, array(
			'type'          => 'float',
			'length'        => 15,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));

		$this->hasColumn('free_delivery_over', 'float', 15, array(
				'type'          => 'float',
				'length'        => 15,
				'fixed'         => false,
				'primary'       => false,
				'default'       => '',
				'notnull'       => true,
				'autoincrement' => false
			));
		
		$this->hasColumn('method_default', 'integer', 2, array(
			'type'          => 'integer',
			'length'        => 2,
			'default'       => '0',
			'autoincrement' => false
		));
		
		$this->hasColumn('method_days_before', 'integer', 2, array(
			'type'          => 'integer',
			'length'        => 2,
			'default'       => '0',
			'autoincrement' => false
		));

		$this->hasColumn('method_days_after', 'integer', 2, array(
			'type'          => 'integer',
			'length'        => 2,
			'default'       => '0',
			'autoincrement' => false
		));
		
		$this->hasColumn('method_zone', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'default'       => '0',
			'autoincrement' => false
		));
		
		$this->hasColumn('sort_order', 'integer', 2, array(
			'type'          => 'integer',
			'length'        => 2,
			'autoincrement' => false
		));
		$this->hasColumn('weight_rates', 'string', null, array(
			'type'          => 'string',
			'length'        => null,
			'autoincrement' => false
		));
		$this->hasColumn('min_rental_number', 'integer', 4, array(
				'type'          => 'integer',
				'length'        => 4,
				'default'       => '0',
				'autoincrement' => false
		));
		$this->hasColumn('min_rental_type', 'integer', 4, array(
				'type'          => 'integer',
				'length'        => 4,
				'default'       => '0',
				'autoincrement' => false
		));
	}
}