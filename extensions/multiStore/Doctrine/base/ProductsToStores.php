<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductsToStores extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
		
		$this->hasOne('Stores', array(
			'local' => 'stores_id',
			'foreign' => 'stores_id'
		));
		
		$this->hasOne('Products', array(
			'local' => 'products_id',
			'foreign' => 'products_id'
		));
	}
	
	public function setUpParent(){
		$Products = Doctrine::getTable('Products')->getRecordInstance();
		$Stores = Doctrine::getTable('Stores')->getRecordInstance();

		$Products->hasMany('ProductsToStores', array(
			'local'   => 'products_id',
			'foreign' => 'products_id',
			'cascade' => array('delete')
		));

		$Stores->hasMany('ProductsToStores', array(
			'local'   => 'stores_id',
			'foreign' => 'stores_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('products_to_stores');
		
		$this->hasColumn('products_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('stores_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'notnull'       => true,
			'autoincrement' => false,
		));
	}
}