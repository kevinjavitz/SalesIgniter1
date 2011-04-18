<?php
/*
$Id: ProductsAttributesViews.php

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class ProductsAttributesViews extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
	}
	
	public function setUpParent(){
		$ProductAttributes = Doctrine::getTable('ProductsAttributes')->getRecordInstance();
		
		$ProductAttributes->hasMany('ProductsAttributesViews', array(
			'local'   => 'products_attributes_id',
			'foreign' => 'products_attributes_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('products_attributes_views');
		
		$this->hasColumn('products_attributes_views_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('products_attributes_id', 'integer', 11, array(
			'type'          => 'integer',
			'length'        => 11,
			'unsigned'      => 0,
			'primary'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('view_name', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('view_image', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false
		));
	}
}
?>