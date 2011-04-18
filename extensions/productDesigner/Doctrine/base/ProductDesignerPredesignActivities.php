<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductDesignerPredesignActivities extends Doctrine_Record {

	public function setTableDefinition(){
		$this->setTableName('product_designer_predesign_activities');
		
		$this->hasColumn('activity_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('activity_name', 'string', 64, array(
			'type' => 'string',
			'length' => 64,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
	}
}