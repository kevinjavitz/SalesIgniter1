<?php
/*
Categories Description 2 Extension Version 1

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class categoryDescription2_admin_categories_new_category extends Extension_categoryDescription2 {

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
		return '<li class="ui-tabs-nav-item"><a href="#tab_categoryDescription2"><span>' . 'Description 2' . '</span></a></li>';
	}

	public function NewCategoryTabBody(&$Category){
		$contents = '<ul>';
		$languages = tep_get_languages();
		for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
			$langImage = tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']);
			$lID = $languages[$i]['id'];
			$contents .= '<li class="ui-tabs-nav-item"><a href="#description2LangTab_' . $lID . '"><span>' . $langImage . '&nbsp;' . $languages[$i]['name'] . '</span></a></li>';
		}
		$contents .= '</ul>';

		for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
			$langImage = tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']);
			$lID = $languages[$i]['id'];
			$description = $Category->CategoriesDescription[$lID]->categories_description2;
			
			$contents .= '<div id="description2LangTab_' . $lID . '">' . 
			 tep_draw_textarea_field('categories_description2[' . $lID . ']', 'hard', 30, 5, $description, 'class="makeFCK"') . 
			'</div>';
		}
		return '<div id="tab_categoryDescription2">' . $contents . '</div>';
	}
}
?>