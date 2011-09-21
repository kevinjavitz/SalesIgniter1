<?php
/*
	Multi Stores Extension Version 1.1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Zips extends Doctrine_Record {

	public function setTableDefinition(){
		$this->setTableName('zips');
		
		$this->hasColumn('id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'notnull'       => true,
			'autoincrement' => true,
		));

		$this->hasColumn('country_code', 'string', 2, array(
			'type'          => 'string',
			'length'        => 2,
			'fixed'         => true,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

		$this->hasColumn('zip', 'string', 20, array(
			'type'          => 'string',
			'length'        => 20,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('place_name', 'string', 180, array(
			'type'          => 'string',
			'length'        => 180,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('admin_name1', 'string', 100, array(
			'type'          => 'string',
			'length'        => 100,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('admin_code1', 'string', 20, array(
			'type'          => 'string',
			'length'        => 20,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));		
		
		$this->hasColumn('admin_name2', 'string', 100, array(
			'type'          => 'string',
			'length'        => 100,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('admin_code2', 'string', 20, array(
			'type'          => 'string',
			'length'        => 20,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));		
		
		$this->hasColumn('admin_name3', 'string', 100, array(
			'type'          => 'string',
			'length'        => 100,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('admin_code3', 'string', 20, array(
			'type'          => 'string',
			'length'        => 20,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));		
		
		$this->hasColumn('latitude', 'float', 4, array(
			'type'          => 'float',
			'length'        => 4,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('longitude', 'float', 4, array(
			'type'          => 'float',
			'length'        => 4,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
				
		$this->hasColumn('accuracy', 'boolean', 1, array(
			'type'          => 'boolean',
			'length'        => 1,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));		
	}
	
}