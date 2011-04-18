<?php
/*
	Orders Custom Fields Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class OrdersCustomFieldsOptionsDescription extends Doctrine_Record {
	
	public function setUp(){
		$this->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'language_id');
	}
	
	public function setTableDefinition(){
		$this->setTableName('orders_custom_fields_options_description');
		
		$this->hasColumn('option_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('option_name', 'string', 64, array(
			'type' => 'string',
			'length' => 64,
			'fixed' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('language_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
	}
	
	public function newLanguageProcess($fromLangId, $toLangId){
		$Qdescription = Doctrine_Query::create()
		->from('OrdersCustomFieldsOptionsDescription')
		->where('language_id = ?', (int) $fromLangId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($Qdescription as $Record){
			$toTranslate = array(
				'name' => $Record['option_name']
			);
			
			EventManager::notify('OrdersCustomFieldsOptionsDescriptionNewLanguageProcessBeforeTranslate', $toTranslate);
			
			$translated = sysLanguage::translateText($toTranslate, (int) $toLangId, (int) $fromLangId);
			
			$newDesc = new OrdersCustomFieldsOptionsDescription();
			$newDesc->option_id = $Record['option_id'];
			$newDesc->language_id = (int) $toLangId;
			$newDesc->option_name = $translated['name'];
			
			EventManager::notify('OrdersCustomFieldsOptionsDescriptionNewLanguageProcessBeforeSave', $newDesc);
			
			$newDesc->save();
		}
	}

	public function cleanLanguageProcess($existsId){
		Doctrine_Query::create()
		->delete('OrdersCustomFieldsOptionsDescription')
		->whereNotIn('language_id', $existsId)
		->execute();
	}

	public function deleteLanguageProcess($langId){
		Doctrine_Query::create()
		->delete('OrdersCustomFieldsOptionsDescription')
		->where('language_id = ?', (int) $langId)
		->execute();
	}
}