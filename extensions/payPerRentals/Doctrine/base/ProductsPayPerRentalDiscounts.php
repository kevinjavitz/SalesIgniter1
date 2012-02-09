<?php
/*
Pay Per Rental Products Extension Version 1

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class ProductsPayPerRentalDiscounts extends Doctrine_Record {

	public function setUp(){
		parent::setUp();
		$this->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'store_id');

		$this->hasOne('ProductsPayPerRental', array(
				'local' => 'ppr_id',
				'foreign' => 'pay_per_rental_id',
			));
	}

	public function setTableDefinition(){
		$this->setTableName('products_pay_per_rental_discounts');

		$this->hasColumn('ppr_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => false,
				'autoincrement' => false,
			));

		$this->hasColumn('store_id', 'integer', 4, array(
				'type' => 'integer',
				'default' => 0,
				'length' => 4,
				'unsigned' => 0,
				'primary' => false,
				'autoincrement' => false,
			));

		$this->hasColumn('ppr_type', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0',
				'notnull' => true,
				'autoincrement' => false,
			));

		$this->hasColumn('discount_stage', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0',
				'notnull' => true,
				'autoincrement' => false,
			));

		$this->hasColumn('discount_from', 'decimal', 15, array(
				'type' => 'decimal',
				'length' => 15,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0.0000',
				'notnull' => true,
				'autoincrement' => false,
				'scale' => false,
			));

		$this->hasColumn('discount_to', 'decimal', 15, array(
				'type' => 'decimal',
				'length' => 15,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0.0000',
				'notnull' => true,
				'autoincrement' => false,
				'scale' => false,
			));

		$this->hasColumn('discount_amount', 'decimal', 15, array(
				'type' => 'decimal',
				'length' => 15,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0.0000',
				'notnull' => true,
				'autoincrement' => false,
				'scale' => false,
			));

		$this->hasColumn('discount_type', 'string', 16);
	}
}