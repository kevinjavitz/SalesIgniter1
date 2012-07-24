<?php

class ProductsGroups extends Doctrine_Record {
	
	public function setUp(){

	}
	 
	public function setTableDefinition(){
		$this->setTableName('products_groups');
		
		$this->hasColumn('product_group_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));

		$this->hasColumn('product_group_name', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

		$this->hasColumn('product_group_limit', 'integer', 4, array(
				'type'          => 'integer',
				'length'        => 4,
				'fixed'         => false,
				'primary'       => false,
				'notnull'       => true,
				'default'       => '0',
				'autoincrement' => false,
			));

		$this->hasColumn('products', 'string', null, array(
				'type'          => 'string',
				'length'        => null,
				'fixed'         => false,
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false,
			));

	}
}