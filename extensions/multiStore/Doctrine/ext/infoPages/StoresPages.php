<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class StoresPages extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
		$this->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'stores_id');
		
		$this->hasMany('StoresPagesDescription', array(
			'local' => 'stores_pages_id',
			'foreign' => 'stores_pages_id',
			'cascade' => array('delete')
		));
	}
	
	public function setUpParent(){
		$Pages = Doctrine_Core::getTable('Pages')->getRecordInstance();
		$Stores = Doctrine_Core::getTable('Stores')->getRecordInstance();
		
		$Pages->hasMany('StoresPages', array(
			'local'   => 'pages_id',
			'foreign' => 'pages_id',
			'cascade' => array('delete')
		));
		
		$Stores->hasMany('StoresPages', array(
			'local'   => 'stores_id',
			'foreign' => 'stores_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('stores_pages');
		
		$this->hasColumn('stores_pages_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));

		$this->hasColumn('pages_id', 'integer', 4, array(
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
	}
}