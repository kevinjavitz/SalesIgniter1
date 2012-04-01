<?php

class TemplateManagerContainerLinks extends Doctrine_Record {

	public function setUp(){
		parent::setUp();
		$this->hasOne('TemplateManagerLayoutsContainers as Container', array(
			'local' => 'container_id',
			'foreign' => 'container_id'
		));
	}

	public function setTableDefinition(){
		$this->setTableName('template_manager_container_links');

		$this->hasColumn('link_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));

		$this->hasColumn('container_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'autoincrement' => false,
		));

		$this->hasColumn('link_name', 'string', 255, array(
			'type' => 'string',
			'length' => 255
		));
	}
}

