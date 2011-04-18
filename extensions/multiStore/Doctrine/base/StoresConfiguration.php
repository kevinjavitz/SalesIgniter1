<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class StoresConfiguration extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
		//$this->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'stores_id');
	}
	
	public function setUpParent(){
	}

	public function setTableDefinition(){
		$this->setTableName('stores_configuration');
		
		$this->hasColumn('configuration_key', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'fixed'         => false,
			'primary'       => true,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('configuration_value', 'string', null, array(
			'type'          => 'string',
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false
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