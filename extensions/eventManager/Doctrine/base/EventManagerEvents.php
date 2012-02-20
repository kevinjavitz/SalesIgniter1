<?php

/**
 * PhotoGalleryCategories
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class EventManagerEvents extends Doctrine_Record {
	
	public function setUp(){
		parent::setUp();
		$this->hasMany('EventManagerEventsDescription', array(
			'local'   => 'events_id',
			'foreign' => 'events_id',
			'cascade' => array('delete')
		));

	}
	
	public function setTableDefinition(){
		$this->setTableName('event_manager_events');
		
		$this->hasColumn('events_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));

		$this->hasColumn('events_start_date', 'datetime', null, array(
				'type'          => 'datetime',
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false
		));
		$this->hasColumn('events_end_date', 'datetime', null, array(
				'type'          => 'datetime',
				'primary'       => false,
				'notnull'       => true,
				'autoincrement' => false
		));

	}
}