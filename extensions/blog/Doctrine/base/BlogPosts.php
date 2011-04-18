<?php
/*
	$Id: Pages.php

	Blog Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class BlogPosts extends Doctrine_Record {

	public function setUp(){
		$this->hasMany('BlogPostsDescription', array(
			'local'   => 'post_id',
			'foreign' => 'blog_post_id',
			'cascade' => array('delete')
		));

        $this->hasMany('BlogPostToCategories', array(
			'local' => 'post_id',
			'foreign' => 'blog_post_id',
			'cascade' => array('delete')
		));

          $this->hasMany('BlogCommentToPost', array(
			'local' => 'post_id',
			'foreign' => 'blog_post_id',
			'cascade' => array('delete')
		));
	}

	public function preInsert($event){
		//$this->post_date = date('Y-m-d H:i:s');
	}

	public function setTableDefinition(){
		$this->setTableName('blog_posts');
		
		$this->hasColumn('post_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('post_status', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));

        $this->hasColumn('post_date', 'timestamp', null, array(
			'type'          => 'timestamp',
			'primary'       => false,
			'default'       => '0000-00-00 00:00:00',
			'notnull'       => true,
			'autoincrement' => false
		));
	}
}
?>