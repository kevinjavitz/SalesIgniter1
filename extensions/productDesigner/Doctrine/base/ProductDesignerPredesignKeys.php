<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductDesignerPredesignKeys extends Doctrine_Record {

	public function setTableDefinition(){
		$this->setTableName('product_designer_predesign_keys');
		
		$this->hasColumn('key_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('key_text', 'string', 64, array(
			'type' => 'string',
			'length' => 64,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('set_from', 'string', 12, array(
			'type' => 'string',
			'length' => 12,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
			'default' => 'admin'
		));
		
		$this->hasColumn('key_type', 'string', 12, array(
			'type' => 'string',
			'length' => 12,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
			'default' => 'text'
		));
	}
}