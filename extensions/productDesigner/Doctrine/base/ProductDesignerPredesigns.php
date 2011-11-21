<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductDesignerPredesigns extends Doctrine_Record {
	
	public function setUp(){
		$this->hasMany('ProductDesignerPredesignsToPredesignCategories', array(
			'local'      => 'predesign_id',
			'foreign'    => 'predesign_id',
			'cascade'    => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('product_designer_predesigns');
		$this->hasColumn('predesign_id', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'primary' => true,
		'autoincrement' => true,
		));
		$this->hasColumn('predesign_name', 'string', 64, array(
		'type' => 'string',
		'length' => 64,
		'primary' => false,
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('predesign_cost', 'decimal', 15, array(
			'type'          => 'decimal',
			'length'        => 15,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0.0000',
			'notnull'       => true,
			'autoincrement' => false,
			'scale'         => 4
		));
		$this->hasColumn('predesign_location', 'string', 12, array(
		'type' => 'string',
		'length' => 12,
		'primary' => false,
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('predesign_settings', 'string', null, array(
		'type' => 'string',
		'fixed' => false,
		'primary' => false,
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('predesign_activities', 'string', null, array(
		'type' => 'string',
		'fixed' => false,
		'primary' => false,
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('predesign_classes', 'string', null, array(
		'type' => 'string',
		'fixed' => false,
		'primary' => false,
		'notnull' => true,
		'autoincrement' => false,
		));
	}
}