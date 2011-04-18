<?php
class ModulesConfiguration extends Doctrine_Record {
	public function setUp(){
		$this->setAttribute(Doctrine::ATTR_COLL_KEY, 'configuration_key');
	}
	
	public function preInsert($event){
		$this->date_added = date('Y-m-d H:i:s');
	}
	
	public function preUpdate($event){
		$this->last_modified = date('Y-m-d H:i:s');
	}
	
	public function setTableDefinition(){
		$this->setTableName('modules_configuration');
		
		$this->hasColumn('configuration_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('modules_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('configuration_title', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('configuration_key', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('configuration_value', 'string', null, array(
			'type'          => 'string',
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('configuration_description', 'string', 255, array(
			'type'          => 'string',
			'length'        => 255,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
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
		
		$this->hasColumn('last_modified', 'timestamp', null, array(
			'type'          => 'timestamp',
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('date_added', 'timestamp', null, array(
			'type'          => 'timestamp',
			'primary'       => false,
			'default'       => '0000-00-00 00:00:00',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('use_function', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('set_function', 'string', 999, array(
			'type'          => 'string',
			'length'        => 999,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
	}
}