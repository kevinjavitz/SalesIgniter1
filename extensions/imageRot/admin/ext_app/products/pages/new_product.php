<?php
/*
	Banner Manager Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class imageRot_admin_products_new_product extends Extension_imageRot {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;
		EventManager::attachEvents(array(
			'NewProductTabHeader',
			'NewProductTabBody'
		), null, $this);
	}
	

	public function NewProductTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . sysLanguage::get('TAB_BANNER_MANAGER') . '</span></a></li>';
	}
	
	public function NewProductTabBody(&$Product){
		$checkedCats = array();

		if ($Product['products_id'] > 0){
			$QcurGroups = Doctrine_Query::create()
			->select('banner_group_id')
			->from('BannerManagerProductsToGroups')
			->where('products_id = ?', $Product['products_id'])
			->execute();
			if ($QcurGroups->count() > 0){
				foreach($QcurGroups->toArray() as $group){
					$checkedCats[] = $group['banner_group_id'];
				}
				unset($group);
			}
			$QcurGroups->free();
			unset($QcurGroups);
		}

		$contents = tep_get_group_tree_list($checkedCats);
		unset($checkedCats);

		return '<div id="tab_' . $this->getExtensionKey() . '">' . $contents . '</div>';
	}
}
?>