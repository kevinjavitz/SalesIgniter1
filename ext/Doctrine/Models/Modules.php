<?php
class Modules extends Doctrine_Record {
	public function setUp(){
		$this->hasMany('ModulesConfiguration', array(
			'local'   => 'modules_id',
			'foreign' => 'modules_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('modules');
		
		$this->hasColumn('modules_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('modules_code', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('modules_status', 'integer', 2, array(
			'type'          => 'integer',
			'length'        => 2,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('modules_type', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
	}
}