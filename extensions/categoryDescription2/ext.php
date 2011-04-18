<?php
/*
	Categories Description 2 Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_categoryDescription2 extends ExtensionBase {

	public function __construct(){
		parent::__construct('categoryDescription2');
	}
	
	public function init(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvent('PageLayoutFooterBeforeDraw', null, $this);
	}
	
	public function PageLayoutFooterBeforeDraw(){
		global $appExtension, $current_category_id;
		
		$Qcheck = Doctrine_Query::create()
		->select('categories_description2')
		->from('CategoriesDescription')
		->where('categories_id = ?', $current_category_id)
		->andWhere('language_id = ?', Session::get('languages_id'))
		->execute();
		if ($Qcheck->count() > 0){
			$description = $Qcheck->toArray();
			if (!empty($description[Session::get('languages_id')]['categories_description2'])){
				return '<div class="ui-widget ui-widget-content ui-corner-all categoryDescription2">' . 
					$description[Session::get('languages_id')]['categories_description2'] . 
				'</div>';
			}
		}else{
			$infoPages = $appExtension->getExtension('infoPages');
			$pageInfo = $infoPages->getInfoPage(31);
			return '<div class="ui-widget ui-widget-content ui-corner-all categoryDescription2">' . 
				stripslashes($pageInfo['PagesDescription'][Session::get('languages_id')]['pages_html_text']) . 
			'</div>';
		}
		return;
	}
}
?>