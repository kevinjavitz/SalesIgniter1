<?php
/*

	Blog Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class BlogPostsDescription extends Doctrine_Record {
	
	public function setUp(){
		$this->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'language_id');
		
		$this->hasOne('BlogPosts', array(
			'local' => 'blog_post_id',
			'foreign' => 'post_id'
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('blog_posts_description');
		
		$this->hasColumn('blog_post_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'autoincrement' => false
		));

        $this->hasColumn('language_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '1',
			'autoincrement' => false
		));
		
		$this->hasColumn('blog_post_title', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));

          $this->hasColumn('blog_post_seo_url', 'string', 200, array(
			'type'          => 'string',
			'length'        => 200,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));

        $this->hasColumn('blog_post_head_title', 'string', 150, array(
			'type'          => 'string',
			'length'        => 150,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));

        $this->hasColumn('blog_post_head_desc', 'string', 255, array(
			'type'          => 'string',
			'length'        => 255,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));

        $this->hasColumn('blog_post_head_keywords', 'string', 100, array(
			'type'          => 'string',
			'length'        => 100,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));

		$this->hasColumn('blog_post_text', 'string', null, array(
			'type'          => 'string',
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));

	}
	public function newLanguageProcess($fromLangId, $toLangId){
		$Qdescription = Doctrine_Query::create()
		->from('BlogPostsDescription')
		->where('language_id = ?', (int) $fromLangId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($Qdescription as $Record){
			$toTranslate = array(
				'blog_post_title' => $Record['blog_post_title'],
				'blog_post_head_title' => $Record['blog_post_head_title'],
				'blog_post_head_desc' => $Record['blog_post_head_desc'],
				'blog_post_head_keywords' => $Record['blog_post_head_keywords'],
				'blog_post_text' => $Record['blog_post_text']
			);

			EventManager::notify('BlogPostsDescriptionNewLanguageProcessBeforeTranslate', $toTranslate);

			$translated = sysLanguage::translateText($toTranslate, (int) $toLangId, (int) $fromLangId);

			$newDesc = new BlogPostsDescription();
			$newDesc->blog_post_id = $Record['blog_post_id'];
			$newDesc->blog_post_title = $Record['blog_post_title'];
			$newDesc->language_id = (int) $toLangId;
			$newDesc->blog_post_head_title = $translated['blog_post_head_title'];
			$newDesc->blog_post_head_desc = $translated['blog_post_head_desc'];
			$newDesc->blog_post_head_keywords = $translated['blog_post_head_keywords'];
			$newDesc->blog_post_text = $translated['blog_post_text'];

			EventManager::notify('BlogPostsDescriptionNewLanguageProcessBeforeSave', $newDesc);

			$newDesc->save();
		}
	}

	public function cleanLanguageProcess($existsId){
		Doctrine_Query::create()
		->delete('BlogPostsDescription')
		->whereNotIn('language_id', $existsId)
		->execute();
	}

	public function deleteLanguageProcess($langId){
		Doctrine_Query::create()
		->delete('BlogPostsDescription')
		->where('language_id = ?', (int) $langId)
		->execute();
	}
}
?>