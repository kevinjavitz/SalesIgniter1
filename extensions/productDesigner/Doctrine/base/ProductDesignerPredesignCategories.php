<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductDesignerPredesignCategories extends Doctrine_Record {
	
	public function setUp(){
		$this->hasMany('ProductDesignerPredesignCategoriesDescription', array(
			'local'      => 'categories_id',
			'foreign'    => 'categories_id',
			'cascade'    => array('delete')
		));
		
		$this->hasMany('ProductDesignerPredesignsToPredesignCategories', array(
			'local'      => 'categories_id',
			'foreign'    => 'categories_id',
			'cascade'    => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('product_designer_predesign_categories');
		
		$this->hasColumn('categories_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('parent_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('sort_order', 'integer', 2, array(
			'type'          => 'integer',
			'length'        => 2,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
	}
}