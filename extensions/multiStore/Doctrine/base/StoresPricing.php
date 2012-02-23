<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class StoresPricing extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
		$this->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'stores_id');

	}
	
	public function setUpParent(){
		$Products = Doctrine_Core::getTable('Products')->getRecordInstance();
		$Stores = Doctrine_Core::getTable('Stores')->getRecordInstance();

		$Products->hasMany('StoresPricing', array(
			'local'   => 'products_id',
			'foreign' => 'products_id',
			'cascade' => array('delete')
		));
		
		$Stores->hasMany('StoresPricing', array(
			'local'   => 'stores_id',
			'foreign' => 'stores_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('stores_pricing');
		
		$this->hasColumn('stores_pricing_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));

		$this->hasColumn('products_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

		$this->hasColumn('products_price', 'decimal', 15, array(
				'type'          => 'decimal',
				'length'        => 15,
				'unsigned'      => 0,
				'primary'       => false,
				'default'       => '0.0000',
				'notnull'       => true,
				'autoincrement' => false,
				'scale'         => 4
			));
		$this->hasColumn('products_keepit_price', 'decimal', 15, array(
				'type'          => 'decimal',
				'length'        => 15,
				'unsigned'      => 0,
				'primary'       => false,
				'default'       => '0.0000',
				'notnull'       => true,
				'autoincrement' => false,
				'scale'         => false
			));
		$this->hasColumn('products_price_used', 'decimal', 15, array(
				'type'          => 'decimal',
				'length'        => 15,
				'unsigned'      => 0,
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false,
				'scale'         => 4
			));
		$this->hasColumn('products_price_stream', 'decimal', 15, array(
				'type'          => 'decimal',
				'length'        => 15,
				'unsigned'      => 0,
				'primary'       => false,
				'default'       => '0.0000',
				'notnull'       => true,
				'autoincrement' => false,
				'scale'         => 4
			));
		$this->hasColumn('products_price_download', 'decimal', 15, array(
				'type'          => 'decimal',
				'length'        => 15,
				'unsigned'      => 0,
				'primary'       => false,
				'default'       => '0.0000',
				'notnull'       => true,
				'autoincrement' => false,
				'scale'         => 4
			));
		$this->hasColumn('products_type', 'string', 255, array(
				'type'          => 'string',
				'length'        => 255,
				'fixed'         => false,
				'primary'       => false,
				'default'       => 'B',
				'notnull'       => true,
				'autoincrement' => false
			));
		
		$this->hasColumn('stores_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('show_method', 'string', 16, array(
			'type'          => 'string',
			'length'        => 16,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
	}
}