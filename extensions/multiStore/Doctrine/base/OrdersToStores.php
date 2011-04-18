<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class OrdersToStores extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
		$this->hasOne('Stores', array(
			'local' => 'stores_id',
			'foreign' => 'stores_id'
		));
	}
	
	public function setUpParent(){
		$Orders = Doctrine::getTable('Orders')->getRecordInstance();
		$Stores = Doctrine::getTable('Stores')->getRecordInstance();

		$Orders->hasOne('OrdersToStores', array(
			'local'   => 'orders_id',
			'foreign' => 'orders_id',
			'cascade' => array('delete')
		));

		$Stores->hasMany('OrdersToStores', array(
			'local'   => 'stores_id',
			'foreign' => 'stores_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('orders_to_stores');
		
		$this->hasColumn('orders_id', 'integer', 4, array(
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
	}
}