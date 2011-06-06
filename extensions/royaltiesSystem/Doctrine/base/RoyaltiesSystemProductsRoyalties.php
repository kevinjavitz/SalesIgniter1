<?php
/*
	Royalties System Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class RoyaltiesSystemProductsRoyalties extends Doctrine_Record {

	public function setUp(){
		parent::setUp();
		$this->setUpParent();

		$this->hasOne('Products', array(
		                                'local' => 'products_id',
		                                'foreign' => 'products_id'
		                           ));
		$this->hasOne('Customers', array(
		                               'local' => 'content_provider_id',
		                               'foreign' => 'customers_id'
		                          ));
		$this->hasOne('OrdersProducts', array(
		                                'local' => 'products_id',
		                                'foreign' => 'products_id'
		                           ));
	}

	public function setUpParent(){
		$Products = Doctrine::getTable('Products')->getRecordInstance();

		$Products->hasMany('RoyaltiesSystemProductsRoyalties', array(
		                                                        'local' => 'products_id',
		                                                        'foreign' => 'products_id',
		                                                        'cascade' => array('delete')
		                                                   ));

		$OrdersProducts = Doctrine::getTable('OrdersProducts')->getRecordInstance();
		
		$OrdersProducts->hasMany('RoyaltiesSystemProductsRoyalties', array(
		                                                            'local' => 'products_id',
		                                                            'foreign' => 'products_id',
		                                                            'cascade' => array('delete')
		                                                       ));
		$Customers = Doctrine::getTable('Customers')->getRecordInstance();

		$Customers->hasMany('RoyaltiesSystemProductsRoyalties', array(
		                                                           'local' => 'content_provider_id',
		                                                           'foreign' => 'customers_id',
		                                                           'cascade' => array('delete')
		                                                      ));
	}

	public function setTableDefinition(){
		$this->setTableName('royalties_system_products_royalties');
		$this->hasColumn('products_royalties_id', 'integer', 11, array(
		                                                   'type' => 'integer',
		                                                   'length' => 11,
		                                                   'unsigned' => 0,
		                                                   'primary' => true,
		                                                   'notnull' => true,
		                                                   'autoincrement' => true,
		                                              ));
		$this->hasColumn('products_id', 'integer', 8, array(
		                                                    'type' => 'integer',
		                                                    'length' => 8,
		                                                    'unsigned' => 0,
		                                                    'primary' => false,
		                                                    'notnull' => true,
		                                                    'autoincrement' => false,
		                                               ));
		$this->hasColumn('products_price_rental', 'decimal', 15, array(
		                                                   'type' => 'decimal',
		                                                   'length' => 15,
		                                                   'unsigned' => 0,
		                                                   'primary' => false,
		                                                   'default' => '0.0000',
		                                                   'notnull' => true,
		                                                   'autoincrement' => false,
		                                              ));
		$this->hasColumn('content_provider_id', 'integer', 8, array(
		                                                    'type' => 'integer',
		                                                    'length' => 8,
		                                                    'unsigned' => 0,
		                                                    'primary' => false,
		                                                    'notnull' => true,
		                                                    'autoincrement' => false,
		                                               ));
		$this->hasColumn('purchase_type', 'string', 16, array(
		                                                           'type' => 'string',
		                                                           'length' => 16,
		                                                           'primary' => false,
		                                                           'notnull' => true,
		                                                           'autoincrement' => false,
		                                                      ));

		$this->hasColumn('royalty_fee', 'string', 16, array(
		                                                      'type' => 'string',
		                                                      'length' => 16,
		                                                      'unsigned' => 0,
		                                                      'primary' => false,
		                                                      'default' => '0',
		                                                      'notnull' => true,
		                                                      'autoincrement' => false,
		                                                      'scale' => false,
		                                                 ));
	}
}