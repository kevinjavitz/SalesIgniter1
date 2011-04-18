<?php

/**
 * TemplatesInfoboxSearchGuided
 */
class TemplatesInfoboxSearchGuided extends Doctrine_Record {
	
	public function setUp(){
		$this->hasMany('TemplatesInfoboxSearchGuidedDescription', array(
			'local' => 'id',
			'foreign' => 'search_id'
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('templates_infobox_search_guided');
		
		$this->hasColumn('id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));

		$this->hasColumn('option_type', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('option_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('option_sort', 'integer', 2, array(
			'type' => 'integer',
			'length' => 2,
			'unsigned' => 0,
			'primary' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('template_name', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
	}
}