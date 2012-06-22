<?php
/*
	Inventory Centers Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InventoryCentersLaunchPoints extends Doctrine_Record {
	
	public function setUp(){
		$this->setUpParent();

		$this->hasOne('ProductsInventoryCenters', array(
				'local'   => 'inventory_center_id',
				'foreign' => 'inventory_center_id'
			));
	}

	public function setUpParent(){
		$productsInventoryCenters = Doctrine::getTable('ProductsInventoryCenters')->getRecordInstance();

		$productsInventoryCenters->hasMany('InventoryCentersLaunchPoints', array(
				'local'   => 'inventory_center_id',
				'foreign' => 'inventory_center_id',
				'cascade' => array('delete')
			));
	}

	
	public function setTableDefinition(){
		$this->setTableName('inventory_centers_launch_points');
		
		$this->hasColumn('lp_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));

		$this->hasColumn('lp_name', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

		$this->hasColumn('lp_desc', 'string', null, array(
			'type'          => 'string',
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

		$this->hasColumn('lp_marker_color', 'string', null, array(
			'type'          => 'string',
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

		$this->hasColumn('lp_position', 'string', null, array(
			'type'          => 'string',
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

		$this->hasColumn('inventory_center_id', 'integer', 4, array(
				'type'          => 'integer',
				'fixed'         => false,
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false,
			));

		$this->hasColumn('lp_sort_order', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
	}
}