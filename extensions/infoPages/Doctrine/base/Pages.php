<?php
/*
	$Id: Pages.php

	Info Pages Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Pages extends Doctrine_Record {

	public function setUp(){
		$this->hasMany('PagesDescription', array(
			'local'   => 'pages_id',
			'foreign' => 'pages_id',
			'cascade' => array('delete')
		));
		
		$this->hasOne('PagesFields', array(
			'local'   => 'pages_id',
			'foreign' => 'pages_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('pages');
		
		$this->hasColumn('pages_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('sort_order', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('status', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '1',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('infobox_status', 'integer', 1, array(
			'type'          => 'integer',
			'length'        => 1,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '1',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('page_type', 'string', 16, array(
			'type'          => 'string',
			'length'        => 16,
			'fixed'         => true,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('page_key', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'fixed'         => true,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
	}
}
?>