<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductDesignerProductImages extends Doctrine_Record {
	
	public function setUp(){
		$Products = Doctrine_Core::getTable('Products')->getRecordInstance();
		
		$Products->hasMany('ProductDesignerProductImages', array(
			'local'      => 'products_id',
			'foreign'    => 'products_id',
			'cascade'    => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('product_designer_product_images');
		$this->hasColumn('images_id', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'primary' => true,
		'autoincrement' => true,
		));
		$this->hasColumn('products_id', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'primary' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('front_image', 'string', 64, array(
		'type' => 'string',
		'length' => 64,
		'primary' => false,
		'notnull' => false,
		'autoincrement' => false,
		));
		$this->hasColumn('back_image', 'string', 64, array(
		'type' => 'string',
		'length' => 64,
		'primary' => false,
		'notnull' => false,
		'autoincrement' => false,
		));
		$this->hasColumn('color_tone', 'string', 12, array(
		'type' => 'string',
		'length' => 12,
		'primary' => false,
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('display_color', 'string', 12, array(
		'type' => 'string',
		'length' => 12,
		'primary' => false,
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('default_set', 'string', 999, array(
		'type' => 'string',
		'length' => 999,
		'primary' => false,
		'notnull' => true,
		'autoincrement' => false,
		));
	}
}