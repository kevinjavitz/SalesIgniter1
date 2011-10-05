<?php
class PDFTemplatesInfoboxes extends Doctrine_Record {
	
	public function setUp(){
	}
	
	public function setTableDefinition(){
		$this->setTableName('pdf_templates_infoboxes');
		
		$this->hasColumn('box_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('box_code', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'default'       => '',
			'notnull'       => true
		));
		
		$this->hasColumn('box_path', 'string', 255, array(
			'type'          => 'string',
			'length'        => 255,
			'default'       => '',
			'notnull'       => true
		));
		
		$this->hasColumn('ext_name', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'notnull'       => false
		));
	}
}
?>