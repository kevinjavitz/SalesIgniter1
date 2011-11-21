<?php
/*
Pay Per Rental Products Extension Version 1

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class ProductsPayPerPeriods extends Doctrine_Record {
	
	public function setUp(){
		$this->setUpParent();
	}
	
	public function setUpParent(){

	}
	
	public function setTableDefinition(){
		$this->setTableName('products_pay_per_periods');
		
		$this->hasColumn('pay_per_periods_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('products_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));

		$this->hasColumn('period_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('price', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => 4,
		));

	}
}