<?php
/*
	Featured Manager Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class featuredManager_admin_categories_new_category extends Extension_featuredManager {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){


		EventManager::attachEvents(array(
			'NewCategoryTabHeader',
			'NewCategoryTabBody'
		), null, $this);
	}
	

	public function NewCategoryTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . sysLanguage::get('TAB_FEATURED_MANAGER') . '</span></a></li>';
	}
	
	public function NewCategoryTabBody(&$Category){
		$checkedCats = array();

		if ($Category['categories_id'] > 0){
			$QGroups = Doctrine_Query::create()
			->select('featured_group_id')
			->from('FeaturedManagerCategoriesToGroups')
			->where('categories_id = ?', $Category['categories_id'])
			->execute();
			if ($QGroups->count() > 0){
				foreach($QGroups->toArray() as $gInfo){
					$checkedCats[] = $gInfo['featured_group_id'];
				}
				unset($gInfo);
			}
			$QGroups->free();
			unset($QGroups);
		}

		$contents = tep_get_featured_group_tree_list($checkedCats);
		unset($checkedCats);

		return '<div id="tab_' . $this->getExtensionKey() . '">' . $contents . '</div>';
	}
}
?>