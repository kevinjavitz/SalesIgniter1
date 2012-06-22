<?php
/*
	Inventory Centers Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class CustomTags extends Doctrine_Record {
	
	public function setUp(){

	}
	
	public function setTableDefinition(){
		$this->setTableName('custom_tags');
		
		$this->hasColumn('tag_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));

		$this->hasColumn('tag_status', 'integer', 1, array(
				'type'          => 'integer',
				'length'        => 1,
				'unsigned'      => 0,
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false,
			));


		$this->hasColumn('tag_name', 'string', 250, array(
			'type'          => 'string',
			'length'        => 250,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

	}
}