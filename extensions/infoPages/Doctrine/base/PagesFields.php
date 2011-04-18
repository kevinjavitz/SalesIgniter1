<?php
/*
	$Id: Pages.php

	Info Pages Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PagesFields extends Doctrine_Record {

	public function setUp(){
	}
	
	public function setTableDefinition(){
		$this->setTableName('pages_fields');
		
		$this->hasColumn('pages_fields_id', 'integer', 4, array(
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
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('top_content_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '1',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('bottom_content_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '1',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('listing_type', 'string', 16, array(
			'type'          => 'integer',
			'length'        => 16,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => 'field',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('listing_field_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'fixed'         => true,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('listing_attribute_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'fixed'         => true,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
	}
}
?>