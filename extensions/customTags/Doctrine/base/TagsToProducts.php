<?php
/*
	Custom Tags Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class TagsToProducts extends Doctrine_Record {
	
	public function setUp(){
		$this->setUpParent();
		$this->hasOne('Products', array(
				'local' => 'products_id',
				'foreign' => 'products_id'
			));
		$this->hasOne('Customers', array(
				'local' => 'customers_id',
				'foreign' => 'customers_id'
			));

		$this->hasOne('CustomTags', array(
				'local' => 'tag_id',
				'foreign' => 'tag_id'
			));
	}

	public function setUpParent(){
		$CustomTags= Doctrine::getTable('CustomTags')->getRecordInstance();

		$CustomTags->hasMany('TagsToProducts', array(
				'local' => 'tag_id',
				'foreign' => 'tag_id',
				'cascade' => array('delete')
		));

		$Products= Doctrine::getTable('Products')->getRecordInstance();

		$Products->hasMany('TagsToProducts', array(
				'local' => 'products_id',
				'foreign' => 'products_id',
				'cascade' => array('delete')
		));

		$Customers= Doctrine::getTable('Customers')->getRecordInstance();

		$Customers->hasMany('TagsToProducts', array(
				'local' => 'customers_id',
				'foreign' => 'customers_id',
				'cascade' => array('delete')
			));

	}
	
	public function setTableDefinition(){
		$this->setTableName('tags_to_products');
		
		$this->hasColumn('tag_to_products_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));

		$this->hasColumn('tag_id', 'integer', 4, array(
				'type'          => 'integer',
				'length'        => 4,
				'unsigned'      => 0,
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false,
		));

		$this->hasColumn('products_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

		$this->hasColumn('customers_id', 'integer', 4, array(
				'type'          => 'integer',
				'length'        => 4,
				'unsigned'      => 0,
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false,
		));

	}
}