<?php
/*
	Featured Manager Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_featuredManager extends ExtensionBase {
	
	public function __construct(){
		parent::__construct('featuredManager');
	}
	
	public function init(){
		if ($this->isEnabled() === false) return;
	}


	public function getFeaturedGroup($group,$settings = null){

	    $QCategories = Doctrine_Query::create()
			            ->select('cd.categories_name, c.categories_id')
			            ->from('Categories c')
			            ->leftJoin('c.CategoriesDescription cd')
			            ->leftJoin('c.FeaturedManagerCategoriesToGroups fmc2g')
			            ->where('fmc2g.featured_group_id = ?', $group)
			            ->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
			            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	    $FeaturedGroups = Doctrine::getTable('FeaturedManagerGroups')->find($group);
		$numberProducts = $FeaturedGroups->featured_group_number_of_products;
	    
	    if (count($QCategories) > 0){
		    foreach($QCategories as $cInfo){
				$Qproducts = Doctrine_Query::create()
					->select('p.products_id')
					->from('Products p')
					->leftJoin('p.ProductsDescription pd')
					->leftJoin('p.ProductsToCategories p2c')
					->leftJoin('p.ProductsToBox p2b')
					->where('p.products_status = ?', '1')
					->andWhere('p2b.products_id is null')
					->limit($numberProducts)
					->andWhere('p2c.categories_id = ?', $cInfo['categories_id'])
					->andWhere('pd.language_id = ?', (int)Session::get('languages_id'));

				EventManager::notify('ProductListingQueryBeforeExecute', &$Qproducts);

				$productListing = new productListing_col();
				$productListing->setQuery($Qproducts);

				$featElem['BoxHeading'] = $cInfo['CategoriesDescription'][0]['categories_name'];
				$featElem['BoxLink'] = '<a href="'. itw_app_link(tep_get_path($cInfo['categories_id']), 'index', 'default') . '">'. 'More...' . '</a>';			
				$featElem['BoxContent'] = $productListing->draw();
			    $featuredCategories[] = $featElem;
		    }
	    }
	                        
		return $featuredCategories; 
	}

}

function tep_get_featured_group_tree_list($checked = false, $include_itself = true) {
	if (!is_array($checked)){
        $checked = array();
    }
    $catList = '<ul class="catListingUL">';

    $groups_query = tep_db_query("select * from featured_manager_groups");
    while ($groups = tep_db_fetch_array($groups_query)) {
        $catList .= '<li>' .
				tep_draw_checkbox_field('groups[]', $groups['featured_group_id'],
	                                    (in_array($groups['featured_group_id'], $checked)),
		                                 'id="catCheckbox_' . $groups['featured_group_id'] . '"'
				) .
		        '<label for="catCheckbox_' . $groups['featured_group_id'] . '">' . $groups['featured_group_name'] . '</label></li>';
          }
          $catList .= '</ul>';
    return $catList;
}


?>