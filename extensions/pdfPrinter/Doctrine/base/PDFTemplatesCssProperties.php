<?php

/**
 * TemplatesInfoboxesToTemplates
 */
class PDFTemplatesCssProperties extends Doctrine_Record {
	
	public function setUp(){
		//$this->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'layout_id');
	}
	
	public function setTableDefinition(){
		$this->setTableName('pdf_templates_css_properties');
		
		$this->hasColumn('id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));

		$this->hasColumn('css_id', 'string', 250, array(
			'type' => 'string',
			'length' => 250,
			'autoincrement' => false,
		));

		$this->hasColumn('layout_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'notnull'       => false
		));

		$this->hasColumn('css_properties', 'string', null, array(
			'type'          => 'string',
			'length'        => null,
			'notnull'       => false
		));

	}
}