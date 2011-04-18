<?php
class CmcicReference extends Doctrine_Record {

	public function setUp(){

	}

	public function setTableDefinition(){
		$this->setTableName('cmcic_reference');
		
		$this->hasColumn('ref_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('ref_number', 'string', 16, array(
			'type'          => 'string',
			'length'        => 16,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));

		
		$this->hasColumn('order_id', 'integer', 11, array(
			'type'          => 'integer',
			'length'        => 11,
			'autoincrement' => false
		));
	}
}