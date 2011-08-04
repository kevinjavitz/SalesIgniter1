<?php
class pointsRewardsPurchaseTypes extends Doctrine_Record {
	public function setUp(){
		$this->setUpParent();
		parent::setUp();
		$this->hasOne('Products', array(
		                               'local' => 'purchase_type',
		                               'foreign' => 'purchase_type'
		                          ));
		$this->hasOne('OrdersProducts', array(
		                                     'local' => 'purchase_type',
		                                     'foreign' => 'purchase_type'
		                                ));
		
	}

	public function setUpParent(){
		$Products = Doctrine::getTable('Products')->getRecordInstance();

		$Products->hasMany('pointsRewardsPurchaseTypes', array(
		                                                            'local' => 'purchase_type',
		                                                            'foreign' => 'purchase_type'
		                                                       ));

		$OrdersProducts = Doctrine::getTable('OrdersProducts')->getRecordInstance();

		$OrdersProducts->hasMany('pointsRewardsPurchaseTypes', array(
		                                                                  'local' => 'purchase_type',
		                                                                  'foreign' => 'purchase_type'
		                                                             ));
	}

	public function setTableDefinition(){
		$this->setTableName('pointsRewardsPurchaseTypes');
		$this->hasColumn('purchase_type', 'string', 16, array(
															 'type' => 'string',
															 'length' => 16,
															 'default' => '',
															 'primary' => true,
															 'notnull' => true,
														));
		$this->hasColumn('percentage', 'string', 16, array(
														   'type' => 'string',
														   'length' => 16,
														   'notnull' => true
													  ));
		$this->hasColumn('threshold', 'integer', 4, array(
												 'type' => 'integer',
												 'length' => 4,
												 'unsigned' => 0,
												 'primary' => false,
												 'notnull' => true
											));
		$this->hasColumn('conversionRatio', 'decimal', 10, array(
														   'type' => 'decimal',
														   'scale' => 2,
														   'unsigned' => 0,
														   'primary' => false,
														   'notnull' => true
													  ));
		
	}
}

