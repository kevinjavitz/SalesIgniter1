<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class StoresPagesDescription extends Doctrine_Record {

	public function setUp(){
		$this->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'language_id');
		
		$this->hasOne('StoresPages', array(
			'local' => 'stores_pages_id',
			'foreign' => 'stores_pages_id'
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('stores_pages_description');
		
		$this->hasColumn('stores_pages_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
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
		$this->hasColumn('pages_head_title_tag', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));
		$this->hasColumn('pages_head_desc_tag', 'string', null, array(
			'type'          => 'string',
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));
		$this->hasColumn('pages_head_keywords_tag', 'string', null, array(
			'type'          => 'string',
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
		
		$this->hasColumn('language_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
	}
	public function newLanguageProcess($fromLangId, $toLangId){
		$Qdescription = Doctrine_Query::create()
		->from('StoresPagesDescription')
		->where('language_id = ?', (int) $fromLangId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($Qdescription as $Record){
			$toTranslate = array(
				'pages_title' => $Record['pages_title'],
				'pages_html_text' => $Record['pages_html_text']
			);

			EventManager::notify('StoresPagesDescriptionNewLanguageProcessBeforeTranslate', $toTranslate);

			$translated = sysLanguage::translateText($toTranslate, (int) $toLangId, (int) $fromLangId);

			$newDesc = new StoresPagesDescription();
			$newDesc->stores_pages_id = $Record['stores_pages_id'];
			$newDesc->language_id = (int) $toLangId;
			$newDesc->pages_title = $translated['pages_title'];
			$newDesc->pages_html_text = $translated['pages_html_text'];

			EventManager::notify('StoresPagesDescriptionNewLanguageProcessBeforeSave', $newDesc);

			$newDesc->save();
		}
	}
	public function deleteLanguageProcess($langId){
		Doctrine_Query::create()
		->delete('StoresPagesDescription')
		->where('language_id = ?', (int) $langId)
		->execute();
	}
}