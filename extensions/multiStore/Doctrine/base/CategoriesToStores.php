<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class CategoriesToStores extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
		
		$this->hasOne('Categories', array(
			'local'   => 'categories_id',
			'foreign' => 'categories_id'
		));
		
		$this->hasOne('Stores', array(
			'local'   => 'stores_id',
			'foreign' => 'stores_id'
		));
	}
	
	public function setUpParent(){
		$Categories = Doctrine::getTable('Categories')->getRecordInstance();
		$Stores = Doctrine::getTable('Stores')->getRecordInstance();

		$Categories->hasMany('CategoriesToStores', array(
			'local'   => 'categories_id',
			'foreign' => 'categories_id',
			'cascade' => array('delete')
		));

		$Stores->hasMany('CategoriesToStores', array(
			'local'   => 'stores_id',
			'foreign' => 'stores_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('categories_to_stores');
		
		$this->hasColumn('categories_id', 'integer', 4, array(
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