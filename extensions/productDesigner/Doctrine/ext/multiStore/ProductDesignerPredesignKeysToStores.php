<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductDesignerPredesignKeysToStores extends Doctrine_Record {

	public function setUp(){
		$ProductDesignerPredesignKeys = Doctrine_Core::getTable('ProductDesignerPredesignKeys')->getRecordInstance();
		$Stores = Doctrine_Core::getTable('Stores')->getRecordInstance();
		
		$ProductDesignerPredesignKeys->hasMany('ProductDesignerPredesignKeysToStores', array(
			'local'      => 'key_id',
			'foreign'    => 'key_id',
			'cascade'    => array('delete')
		));
		
		$Stores->hasMany('ProductDesignerPredesignKeysToStores', array(
			'local'      => 'stores_id',
			'foreign'    => 'stores_id',
			'cascade'    => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('product_designer_predesign_keys_to_stores');
		
		$this->hasColumn('key_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('stores_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('content', 'string', 128, array(
			'type' => 'string',
			'length' => 128,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('content_light', 'string', 128, array(
			'type' => 'string',
			'length' => 128,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('content_dark', 'string', 128, array(
			'type' => 'string',
			'length' => 128,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('use_color_replace', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0,
			'primary' => false,
			'autoincrement' => false,
		));
		$this->hasColumn('use_color_replace_light', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0,
			'primary' => false,
			'autoincrement' => false,
		));
		$this->hasColumn('use_color_replace_dark', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0,
			'primary' => false,
			'autoincrement' => false,
		));
	}
}