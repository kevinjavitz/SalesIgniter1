<?php
/*
	Products Attributes Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductsOptionsToProductsOptionsGroups extends Doctrine_Record {
	
	public function setUp(){
		$this->hasOne('ProductsOptionsGroups', array(
			'local' => 'products_options_groups_id',
			'foreign' => 'products_options_groups_id',
			'cascade' => array('delete')
		));
		
		$this->hasOne('ProductsOptions', array(
			'local' => 'products_options_id',
			'foreign' => 'products_options_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition() {
		$this->setTableName('products_options_to_products_options_groups');
		
		$this->hasColumn('products_options_to_products_options_groups_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('products_options_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('products_options_groups_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('sort_order', 'integer', 2, array(
			'type'          => 'integer',
			'length'        => 2,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
	}
}
?>