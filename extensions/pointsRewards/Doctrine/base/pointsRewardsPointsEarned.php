<?php
class pointsRewardsPointsEarned extends Doctrine_Record {
	public function setUp(){
		parent::setUp();
		$this->setUpParent();
		if (sysConfig::exists('EXTENSION_STREAMPRODUCTS_ENABLED') == 'True' && sysConfig::exists('EXTENSION_STREAMPRODUCTS_INSTALLED') == 'True') {
			$this->hasOne('ProductsStreams', array(
												  'local' => 'streaming_id',
												  'foreign' => 'stream_id'
											 ));
		}
		if (sysConfig::exists('EXTENSION_DOWNLOADPRODUCTS_ENABLED') == 'True' && sysConfig::exists('EXTENSION_DOWNLOADPRODUCTS_INSTALLED') == 'True') {
			$this->hasOne('ProductsDownloads', array(
													'local' => 'download_id',
													'foreign' => 'download_id'
											   ));
		}

		$this->hasOne('Products', array(
									   'local' => 'products_id',
									   'foreign' => 'products_id'
								  ));
		$this->hasOne('Products', array(
		                               'local' => 'products_id',
		                               'foreign' => 'products_id'
		                          ));
		$this->hasOne('OrdersProducts', array(
		                                     'local' => 'products_id',
		                                     'foreign' => 'products_id'
		                                ));
		$this->hasOne('Orders', array(
									 'local' => 'orders_id',
									 'foreign' => 'orders_id'
								));
		$this->hasOne('Customers', array(
										'local' => 'customers_id',
										'foreign' => 'customers_id'
								   ));
	}

	public function setUpParent(){
		$Customers = Doctrine::getTable('Customers')->getRecordInstance();
		$Orders = Doctrine::getTable('Orders')->getRecordInstance();
		$Products = Doctrine::getTable('Products')->getRecordInstance();
		$Customers->hasMany('pointsRewardsPointsEarned', array(
																   'local' => 'customers_id',
																   'foreign' => 'customers_id'
															  ));
		$Orders->hasMany('pointsRewardsPointsEarned', array(
																'local' => 'orders_id',
																'foreign' => 'orders_id'
														   ));
		$Products->hasMany('pointsRewardsPointsEarned', array(
																  'local' => 'products_id',
																  'foreign' => 'products_id'
															 ));
		$OrdersProducts = Doctrine::getTable('OrdersProducts')->getRecordInstance();

		$OrdersProducts->hasMany('pointsRewardsPurchaseTypes', array(
		                                                            'local' => 'products_id',
		                                                            'foreign' => 'products_id'
		                                                       ));
		if (sysConfig::exists('EXTENSION_STREAMPRODUCTS_ENABLED') == 'True' && sysConfig::exists('EXTENSION_STREAMPRODUCTS_INSTALLED') == 'True') {
			$Streams = Doctrine::getTable('ProductsStreams')->getRecordInstance();
			$Streams->hasOne('pointsRewardsPointsEarned', array(
												  'local' => 'stream_id',
												  'foreign' => 'streaming_id'
											 ));
		}
		if (sysConfig::exists('EXTENSION_DOWNLOADPRODUCTS_ENABLED') == 'True' && sysConfig::exists('EXTENSION_DOWNLOADPRODUCTS_INSTALLED') == 'True') {
			$Downloads = Doctrine::getTable('ProductsDownloads')->getRecordInstance();
			$Downloads->hasOne('pointsRewardsPointsEarned', array(
													'local' => 'download_id',
													'foreign' => 'download_id'
											   ));
		}
	}

	public function setTableDefinition(){
		$this->setTableName('pointsRewardsPointsEarned');
		$this->hasColumn('pointsEarned_id', 'integer', 4, array(
															   'type' => 'integer',
															   'length' => 4,
															   'unsigned' => 0,
															   'primary' => true,
															   'notnull' => true,
															   'autoincrement' => true,
														  ));
		$this->hasColumn('customers_id', 'integer', 4, array(
															'type' => 'integer',
															'length' => 4,
															'unsigned' => 0,
															'primary' => false,
															'notnull' => true,
															'autoincrement' => false,
													   ));
		$this->hasColumn('points', 'integer', 4, array(
															'type' => 'integer',
															'length' => 4,
															'unsigned' => 0,
															'primary' => false,
															'notnull' => true,
															'autoincrement' => false,
													   ));
		$this->hasColumn('date', 'timestamp', null, array(
															   'type' => 'timestamp',
															   'default' => '0000-00-00 00:00:00',
															   'primary' => false,
															   'notnull' => true,
															   'autoincrement' => false,
														  ));
		$this->hasColumn('purchase_type', 'string', 16, array(
															 'type' => 'string',
															 'length' => 16,
															 'default' => '',
															 'primary' => false,
															 'notnull' => true,
														));
		$this->hasColumn('products_id', 'integer', 4, array(
														   'type' => 'integer',
														   'length' => 4,
														   'unsigned' => 0,
														   'primary' => false,
														   'notnull' => true,
														   'autoincrement' => false,
													  ));
		$this->hasColumn('orders_id', 'integer', 4, array(
														 'type' => 'integer',
														 'length' => 4,
														 'unsigned' => 0,
														 'primary' => false,
														 'notnull' => true,
														 'autoincrement' => false,
													));
		$this->hasColumn('download_id', 'integer', 4, array(
														   'type' => 'integer',
														   'length' => 4,
														   'unsigned' => 0,
														   'primary' => false,
														   'notnull' => true,
														   'autoincrement' => false,
													  ));
		$this->hasColumn('streaming_id', 'integer', 4, array(
															'type' => 'integer',
															'length' => 4,
															'unsigned' => 0,
															'primary' => false,
															'notnull' => true,
															'autoincrement' => false,
													   ));
	}
}