<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductDesignerPredesignCategoriesDescription extends Doctrine_Record {

	public function setUp(){
		$this->hasOne('ProductDesignerPredesignCategories', array(
			'local'      => 'categories_id',
			'foreign'    => 'categories_id'
		));
		
		$this->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'language_id');
	}
	
	public function setTableDefinition(){
		$this->setTableName('product_designer_predesign_categories_description');
		
		$this->hasColumn('categories_id', 'integer', 4, array(
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
		
		$this->hasColumn('categories_name', 'string', 32, array(
			'type'          => 'string',
			'length'        => 32,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));
	}
	public function newLanguageProcess($fromLangId, $toLangId){
		$Qdescription = Doctrine_Query::create()
		->from('ProductDesignerPredesignCategoriesDescription')
		->where('language_id = ?', (int) $fromLangId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($Qdescription as $Record){
			$toTranslate = array(
				'categories_name' => $Record['categories_name']
			);

			EventManager::notify('ProductDesignerPredesignCategoriesDescriptionNewLanguageProcessBeforeTranslate', $toTranslate);

			$translated = sysLanguage::translateText($toTranslate, (int) $toLangId, (int) $fromLangId);

			$newDesc = new ProductDesignerPredesignCategoriesDescription();
			$newDesc->categories_id = $Record['categories_id'];
			$newDesc->language_id = (int) $toLangId;
			$newDesc->categories_name = $translated['categories_name'];

			EventManager::notify('ProductDesignerPredesignCategoriesDescriptionNewLanguageProcessBeforeSave', $newDesc);

			$newDesc->save();
		}
	}

	public function cleanLanguageProcess($existsId){
		Doctrine_Query::create()
		->delete('ProductDesignerPredesignCategoriesDescription')
		->whereNotIn('language_id', $existsId)
		->execute();
	}

	public function deleteLanguageProcess($langId){
		Doctrine_Query::create()
		->delete('ProductDesignerPredesignCategoriesDescription')
		->where('language_id = ?', (int) $langId)
		->execute();
	}
}