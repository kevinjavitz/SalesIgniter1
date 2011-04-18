<?php

/**
 * TemplatesInfoboxesToTemplates
 */
class TemplatesInfoboxesToTemplates extends Doctrine_Record {
	
	public function setUp(){
		parent::setUp();

		$this->hasMany('TemplatesInfoboxesDescription', array(
			'local'   => 'templates_infoboxes_id',
			'foreign' => 'templates_infoboxes_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('templates_infoboxes_to_templates');
		
		$this->hasColumn('templates_infoboxes_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));

		$this->hasColumn('box_id', 'string', 250, array(
			'type' => 'string',
			'length' => 250,
			'autoincrement' => false,
		));

		$this->hasColumn('layout_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'notnull'       => false
		));
		
		$this->hasColumn('template_file', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'notnull'       => false
		));

		$this->hasColumn('widget_properties', 'string', null, array(
			'type'          => 'string',
			'length'        => null,
			'notnull'       => false
		));

	}
}