<?php
/*

	Blog Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class BlogComments extends Doctrine_Record {

	public function setUp(){
		
	}

    public function preInsert($event){
		$this->comment_date = date('Y-m-d H:i:s');
	}

	public function setTableDefinition(){
		$this->setTableName('blog_comments');
		
		$this->hasColumn('comment_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('comment_status', 'integer', 1, array(
			'type'          => 'integer',
			'length'        => 1,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));

         $this->hasColumn('comment_date', 'timestamp', null, array(
			'type'          => 'timestamp',
			'primary'       => false,
			'default'       => '0000-00-00 00:00:00',
			'notnull'       => true,
			'autoincrement' => false
		));

       $this->hasColumn('comment_text', 'string', null, array(
			'type'          => 'string',
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));

       $this->hasColumn('comment_author', 'string', 150, array(
			'type'          => 'string',
			'length'        => 150,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));

        $this->hasColumn('comment_email', 'string', 100, array(
			'type'          => 'string',
			'length'        => 100,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));
	}
}
?>