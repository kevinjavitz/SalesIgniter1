<?php
/*
$Id: ProductsOptionsValuesDescription.php

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class ProductsOptionsValuesDescription extends Doctrine_Record {
	
	public function setUp(){
		$ProductsOptionsValues = Doctrine_Core::getTable('ProductsOptionsValues')->getRecordInstance();
		
		$ProductsOptionsValues->hasMany('ProductsOptionsValuesDescription', array(
			'local' => 'products_options_values_id',
			'foreign' => 'products_options_values_id',
			'cascade' => array('delete')
		));
		
		$this->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'language_id');
	}
	
	public function setTableDefinition(){
		$this->setTableName('products_options_values_description');

		$this->hasColumn('products_options_values_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => false
		));

		$this->hasColumn('language_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'default'       => '1',
			'autoincrement' => false,
		));
		
		$this->hasColumn('products_options_values_name', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false,
		));
	}
	
	public function newLanguageProcess($fromLangId, $toLangId){
		$Qdescription = Doctrine_Query::create()
		->from('ProductsOptionsValuesDescription')
		->where('language_id = ?', (int) $fromLangId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($Qdescription as $Record){
			$toTranslate = array(
				'name' => $Record['products_options_values_name']
			);
			
			EventManager::notify('ProductsOptionsValuesDescriptionNewLanguageProcessBeforeTranslate', $toTranslate);
			
			$translated = sysLanguage::translateText($toTranslate, (int) $toLangId, (int) $fromLangId);
			
			$newDesc = new ProductsOptionsValuesDescription();
			$newDesc->products_options_values_id = $Record['products_options_values_id'];
			$newDesc->language_id = (int) $toLangId;
			$newDesc->products_options_values_name = $translated['name'];
			
			EventManager::notify('ProductsOptionsValuesDescriptionNewLanguageProcessBeforeSave', $newDesc);
			
			$newDesc->save();
		}
	}

	public function cleanLanguageProcess($existsId){
		Doctrine_Query::create()
		->delete('ProductsOptionsValuesDescription')
		->whereNotIn('language_id', $existsId)
		->execute();
	}

	public function deleteLanguageProcess($langId){
		Doctrine_Query::create()
		->delete('ProductsOptionsValuesDescription')
		->where('language_id = ?', (int) $langId)
		->execute();
	}
}
?>