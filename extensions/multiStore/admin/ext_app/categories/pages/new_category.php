<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class multiStore_admin_categories_new_category extends Extension_multiStore {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'NewCategoryTabHeader',
			'NewCategoryTabBody'
		), null, $this);
	}
	
	public function NewCategoryTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_categoryStores"><span>' . 'Stores' . '</span></a></li>';
	}

	public function NewCategoryTabBody(&$Category){
		$contents = '';
		$Qstores = Doctrine_Query::create()
		->from('Stores')
		->orderBy('stores_name');
		
		if ($Category->categories_id > 0){
			$Qcategory = Doctrine_Query::create()
			->from('CategoriesToStores')
			->where('categories_id = ?', $Category->categories_id)
			->execute(array(), Doctrine::HYDRATE_ARRAY);
			
			$curStores = array();
			foreach($Qcategory as $cInfo){
				$curStores[] = $cInfo['stores_id'];
			}
		}
		$Result = $Qstores->execute(array(), Doctrine::HYDRATE_ARRAY);
		foreach($Result as $sInfo){
			$checkbox = htmlBase::newElement('checkbox')
			->setId('store_' . $sInfo['stores_id'])
			->setName('store[]')
			->setValue($sInfo['stores_id'])
			->setLabel($sInfo['stores_name'])
			->setLabelPosition('after')
			->setChecked((isset($curStores) && in_array($sInfo['stores_id'], $curStores)));
			
			$contents .= $checkbox->draw() . '<br />';
		}
		return '<div id="tab_categoryStores">' . $contents . '</div>';
	}
}
?>