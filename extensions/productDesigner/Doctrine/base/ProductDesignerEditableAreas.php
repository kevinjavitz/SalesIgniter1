<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductDesignerEditableAreas extends Doctrine_Record {
	
	public function setUp(){
		$Products = Doctrine_Core::getTable('Products');
		
		$Products->hasMany('ProductDesignerEditableAreas', array(
			'local'      => 'products_id',
			'foreign'    => 'products_id',
			'cascade'    => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('product_designer_editable_areas');
		
		$this->hasColumn('area_id', 'integer', 4, array(
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
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('area_x1', 'decimal', 15, array(
			'type'          => 'decimal',
			'length'        => 15,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0.0000',
			'notnull'       => true,
			'autoincrement' => false,
			'scale'         => 4
		));
		
		$this->hasColumn('area_x2', 'decimal', 15, array(
			'type'          => 'decimal',
			'length'        => 15,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0.0000',
			'notnull'       => true,
			'autoincrement' => false,
			'scale'         => 4
		));
		
		$this->hasColumn('area_y1', 'decimal', 15, array(
			'type'          => 'decimal',
			'length'        => 15,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0.0000',
			'notnull'       => true,
			'autoincrement' => false,
			'scale'         => 4
		));
		
		$this->hasColumn('area_y2', 'decimal', 15, array(
			'type'          => 'decimal',
			'length'        => 15,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0.0000',
			'notnull'       => true,
			'autoincrement' => false,
			'scale'         => 4
		));
		
		$this->hasColumn('area_width', 'decimal', 15, array(
			'type'          => 'decimal',
			'length'        => 15,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0.0000',
			'notnull'       => true,
			'autoincrement' => false,
			'scale'         => 4
		));
		
		$this->hasColumn('area_height', 'decimal', 15, array(
			'type'          => 'decimal',
			'length'        => 15,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0.0000',
			'notnull'       => true,
			'autoincrement' => false,
			'scale'         => 4
		));
		
		$this->hasColumn('area_height_inches', 'decimal', 15, array(
			'type'          => 'decimal',
			'length'        => 15,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0.0000',
			'notnull'       => true,
			'autoincrement' => false,
			'scale'         => 4
		));
		
		$this->hasColumn('area_width_inches', 'decimal', 15, array(
			'type'          => 'decimal',
			'length'        => 15,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0.0000',
			'notnull'       => true,
			'autoincrement' => false,
			'scale'         => 4
		));
		
		$this->hasColumn('area_location', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'primary'       => false,
			'notnull'       => false
		));
	}
}