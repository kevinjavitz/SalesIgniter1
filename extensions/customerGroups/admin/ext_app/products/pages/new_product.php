<?php
/*
	Banner Manager Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class customerGroups_admin_products_new_product extends Extension_customerGroups {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		EventManager::attachEvents(array(
			'NewProductTabHeader',
			'NewProductTabBody'
		), null, $this);
	}
	

	public function NewProductTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . sysLanguage::get('TAB_CUSTOMER_GROUP') . '</span></a></li>';
	}
	
	public function NewProductTabBody(&$Product){
		$checkedCats = array();

		if ($Product['products_id'] > 0){
			$QcurGroups = Doctrine_Query::create()
			->from('ProductsToCustomerGroups')
			->where('products_id = ?', $Product['products_id'])
			->execute();
			if ($QcurGroups->count() > 0){
				foreach($QcurGroups->toArray() as $group){
					$checkedCats[] = $group['customer_groups_id'];
				}
				unset($group);
			}
			$QcurGroups->free();
			unset($QcurGroups);
		}

		$catList = '<ul class="catListingUL">';
		$catList .= '<li>' . tep_draw_checkbox_field('groups[]', '-1', (in_array('-1', $checkedCats)), 'id="catCheckbox_' . '-1' . '"') . '<label for="catCheckbox_' . '-1' . '">' . 'Guests Customers' . '</label></li>';
		$groups_query = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc("select * from customer_groups");
		foreach ($groups_query as $groups) {
			$catList .= '<li>' . tep_draw_checkbox_field('groups[]', $groups['customer_groups_id'], (in_array($groups['customer_groups_id'], $checkedCats)), 'id="catCheckbox_' . $groups['customer_groups_id'] . '"') . '<label for="catCheckbox_' . $groups['customer_groups_id'] . '">' . $groups['customer_groups_name'] . '</label></li>';
		}
		$catList .= '</ul>';
		unset($checkedCats);

		return '<div id="tab_' . $this->getExtensionKey() . '">' . '<b>Hide from these groups:</b><br/>'. $catList . '</div>';
	}
}
?>