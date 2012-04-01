<?php
/*
	Blog Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class multiStore_admin_blog_blog_categories_new_category extends Extension_multiStore {

	public function __construct(){
		parent::__construct('multiStore');
	}
	
	public function load(){
		global $appExtension;
		if ($this->isEnabled() === false) return;
		$appExtension->registerAsResource(__CLASS__, $this);
		
		EventManager::attachEvents(array(
			'NewBlogCategoryTabHeader',
			'NewBlogCategoryTabBody'
		), null, $this);
	}
	
	public function NewBlogCategoryTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . 'Stores' . '</span></a></li>';
	}
	
	public function NewBlogCategoryTabBody(&$category){
		$contents = '';
		$Qstores = Doctrine_Query::create()
		->from('Stores')
		->orderBy('stores_name');

		if (isset($category['blog_categories_id']) && $category['blog_categories_id'] > 0){
			$Qblogc = Doctrine_Query::create()
			->from('BlogCategoriesToStores')
			->where('blog_categories_id = ?', $category['blog_categories_id'])
			->execute(array(), Doctrine::HYDRATE_ARRAY);

			$curStores = array();
			foreach($Qblogc as $psInfo){
				$curStores[] = $psInfo['stores_id'];
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

		return '<div id="tab_' . $this->getExtensionKey() . '">' . $contents . '</div>';

	}
}
?>