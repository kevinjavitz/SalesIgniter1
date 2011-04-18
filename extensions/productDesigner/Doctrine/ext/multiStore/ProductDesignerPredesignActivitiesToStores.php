<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductDesignerPredesignActivitiesToStores extends Doctrine_Record {

	public function setUp(){
		$PredesignActivities = Doctrine_Core::getTable('ProductDesignerPredesignActivities')->getRecordInstance();
		$Stores = Doctrine_Core::getTable('Stores')->getRecordInstance();

		$PredesignActivities->hasMany('ProductDesignerPredesignActivitiesToStores', array(
			'local'      => 'activity_id',
			'foreign'    => 'activity_id',
			'cascade'    => array('delete')
		));
		
		$Stores->hasMany('ProductDesignerPredesignActivitiesToStores', array(
			'local'      => 'stores_id',
			'foreign'    => 'stores_id',
			'cascade'    => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('product_designer_predesign_activities_to_stores');
		
		$this->hasColumn('activity_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('stores_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => false,
		));
	}
}