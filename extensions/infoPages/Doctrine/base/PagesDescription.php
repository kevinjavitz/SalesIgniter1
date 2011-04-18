<?php
/*
	$Id: PagesDescription.php

	Info Pages Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PagesDescription extends Doctrine_Record {
	
	public function setUp(){
		$this->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'language_id');
		
		$this->hasOne('Pages', array(
			'local' => 'pages_id',
			'foreign' => 'pages_id'
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('pages_description');
		
		$this->hasColumn('pages_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('pages_title', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('pages_html_text', 'string', null, array(
			'type'          => 'string',
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('intorext', 'string', 1, array(
			'type'          => 'string',
			'length'        => 1,
			'fixed'         => true,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('externallink', 'string', 255, array(
			'type'          => 'string',
			'length'        => 255,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('link_target', 'string', 1, array(
			'type'          => 'string',
			'length'        => 1,
			'fixed'         => true,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('language_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->index('pages_id', array(
			'fields' => array('pages_id')
		));
	}
	
	public function newLanguageProcess($fromLangId, $toLangId){
		$Qdescription = Doctrine_Query::create()
		->from('PagesDescription')
		->where('language_id = ?', (int) $fromLangId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($Qdescription as $Record){
			$toTranslate = array(
				'title'       => $Record['pages_title'],
				'description' => $Record['pages_html_text']
			);
			
			EventManager::notify('PagesDescriptionNewLanguageProcessBeforeTranslate', $toTranslate);
			
			$translated = sysLanguage::translateText($toTranslate, (int) $toLangId, (int) $fromLangId);
			
			$newDesc = new PagesDescription();
			$newDesc->pages_id = $Record['pages_id'];
			$newDesc->language_id = (int) $toLangId;
			$newDesc->pages_title = $translated['title'];
			$newDesc->pages_html_text = $translated['description'];
			$newDesc->intorext = $Record['intorext'];
			$newDesc->externallink = $Record['externallink'];
			$newDesc->link_target = $Record['link_target'];
			
			EventManager::notify('PagesDescriptionNewLanguageProcessBeforeSave', $newDesc);
			
			$newDesc->save();
		}
	}

	public function cleanLanguageProcess($existsId){
		Doctrine_Query::create()
		->delete('PagesDescription')
		->whereNotIn('language_id', $existsId)
		->execute();
	}

	public function deleteLanguageProcess($langId){
		Doctrine_Query::create()
		->delete('PagesDescription')
		->where('language_id = ?', (int) $langId)
		->execute();
	}
	
}
?>