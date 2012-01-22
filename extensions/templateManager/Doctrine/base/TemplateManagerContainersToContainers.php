<?php

class TemplateManagerContainersToContainers extends Doctrine_Record {
	
	public function setUp(){
		parent::setUp();
		$this->setUpParent();
		$this->hasOne('TemplateManagerLayoutsContainers', array(
			'local' => 'container_id',
			'foreign' => 'container_id'
		));
	}
	public function setUpParent(){
		$TemplateManagerLayoutsContainers = Doctrine::getTable('TemplateManagerLayoutsContainers')->getRecordInstance();

		$TemplateManagerLayoutsContainers->hasMany('TemplateManagerContainersToContainers', array(
				'local' => 'container_id',
				'foreign' => 'container_id',
				'cascade' => array('delete')
			));
	}

	public function setTableDefinition(){
		$this->setTableName('template_manager_containers_to_containers');

		$this->hasColumn('container_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => true,
				'autoincrement' => false,
			));

		$this->hasColumn('children_container_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => true,
				'autoincrement' => false,
			));

	}
}

