<?php
/*
	Inventory Centers Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ShippingManifest extends Doctrine_Record {
	
	public function setUp(){

	}
	 
	public function setTableDefinition(){
		$this->setTableName('shipping_manifest');
		
		$this->hasColumn('delivery_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));

        $this->hasColumn('orders_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'autoincrement' => false,
		));

        $this->hasColumn('oversized', 'integer', 1, array(
			'type'          => 'integer',
			'length'        => 1,
			'unsigned'      => 0,
			'autoincrement' => false,
		));
        $this->hasColumn('cod', 'integer', 1, array(
			'type'          => 'integer',
			'length'        => 1,
			'unsigned'      => 0,
			'autoincrement' => false,
		));
         $this->hasColumn('multiple', 'integer', 3, array(
			'type'          => 'integer',
			'length'        => 3,
			'unsigned'      => 0,
			'autoincrement' => false,
		));

		$this->hasColumn('pickup_date', 'date', null, array(
			'type'          => 'date',
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false
		));

		$this->hasColumn('delivery_name', 'string', null, array(
			'type'          => 'string',			
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

		$this->hasColumn('delivery_company', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

        $this->hasColumn('delivery_address_1', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
         $this->hasColumn('delivery_address_2', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
         $this->hasColumn('delivery_city', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
        $this->hasColumn('delivery_state', 'string', 2, array(
			'type'          => 'string',
			'length'        => 2,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
         $this->hasColumn('delivery_postcode', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

        $this->hasColumn('delivery_phone', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

         $this->hasColumn('package_weight', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

        $this->hasColumn('package_value', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
        $this->hasColumn('saturday_delivery', 'string', 1, array(
			'type'          => 'string',
			'length'        => 1,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
        $this->hasColumn('hold_at_location', 'string', 1, array(
			'type'          => 'string',
			'length'        => 1,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

        $this->hasColumn('hal_address', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
        $this->hasColumn('hal_city', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
        $this->hasColumn('hal_state', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
        $this->hasColumn('hal_postcode', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
        $this->hasColumn('hal_phone', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
        $this->hasColumn('dim_height', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
        $this->hasColumn('dim_width', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
        $this->hasColumn('dim_length', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
        $this->hasColumn('shipping_type', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
        $this->hasColumn('residential', 'string', 1, array(
			'type'          => 'string',
			'length'        => 1,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
        $this->hasColumn('autopod', 'string', 1, array(
			'type'          => 'string',
			'length'        => 1,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
        $this->hasColumn('tracking_num', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

	}
}