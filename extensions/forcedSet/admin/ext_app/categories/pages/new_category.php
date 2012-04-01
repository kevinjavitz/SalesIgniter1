<?php
/*
	Related Products Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class forcedSet_admin_categories_new_category extends Extension_forcedSet {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'NewCategoryTabHeader',
			'NewCategoryTabBody'
		), null, $this);
	}
	
	public function NewCategoryTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . 'ForcedSet' . '</span></a></li>';
	}

	function get_category_tree_list($parent_id = '0', $checked = false, $include_itself = true) {
		$catList = '';
		if (tep_childs_in_category_count($parent_id) > 0){
			if (!is_array($checked)){
				$checked = array();
			}
			$catList = '<ul class="catListingUL">';

			if ($parent_id == '0'){
				$category_query = tep_db_query("select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.language_id = '" . (int)Session::get('languages_id') . "' and cd.categories_id = '" . (int)$parent_id . "'");
				if (tep_db_num_rows($category_query)){
					$category = tep_db_fetch_array($category_query);

					$catList .= '<li>' . tep_draw_radio_field('categories[]', $parent_id, (in_array($parent_id, $checked)), 'id="catCheckbox_' . $parent_id . '"') . '<label for="catCheckbox_' . $parent_id . '">' . $category['categories_name'] . '</label></li>';
				}
			}

			$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)Session::get('languages_id') . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
			while ($categories = tep_db_fetch_array($categories_query)) {
				$catList .= '<li>' . tep_draw_radio_field('categories[]', $categories['categories_id'], (in_array($categories['categories_id'], $checked)), 'id="catCheckbox_' . $categories['categories_id'] . '"') . '<label for="catCheckbox_' . $categories['categories_id'] . '">' . $categories['categories_name'] . '</label></li>';
				if (tep_childs_in_category_count($categories['categories_id']) > 0){
					$catList .= '<li class="subCatContainer">' . $this->get_category_tree_list($categories['categories_id'], $checked, false) . '</li>';
				}
			}
			$catList .= '</ul>';
		}

		return $catList;
	}
	
	public function NewCategoryTabBody(&$cInfo){
		$checkedCats = array();


		$Qrelation = Doctrine_Query::create()
					->from('ForcedSetCategories')
					->where('forced_set_category_one_id = ?', $_GET['cID'])
					->orWhere('forced_set_category_two_id = ?', $_GET['cID'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		if (count($Qrelation) > 0){
			if ($Qrelation[0]['forced_set_category_one_id'] == $_GET['cID']){
				$checkedCats[] = $Qrelation[0]['forced_set_category_two_id'];
			}else{
				$checkedCats[] = $Qrelation[0]['forced_set_category_one_id'];
			}
		}

	
		return '<div id="tab_' . $this->getExtensionKey() . '">' . $this->get_category_tree_list('0', $checkedCats) . '</div>';
	}
}
?>