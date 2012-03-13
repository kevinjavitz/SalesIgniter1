<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class infoBoxCategoriesMenu extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('categoriesMenu');
	}
	
	public function getCategories($parentId)
	{
	    
		$Qcategories = Doctrine_Query::create()
		    ->select('c.categories_id, cd.categories_name, c.parent_id, cd.categories_seo_url')
		    ->from('Categories c')
		    ->leftJoin('c.CategoriesDescription cd')
		    ->where('c.parent_id = ?', $parentId)
		    ->andWhere('(c.categories_menu = "infobox" or c.categories_menu = "both")')
		    ->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
		    ->orderBy('c.sort_order, cd.categories_name');

		EventManager::notify('CategoryQueryBeforeExecute', $Qcategories);
		
		return $Qcategories->execute(array(), Doctrine::HYDRATE_ARRAY);
	}
	
	public function getChildCategories($parentCategoryId, $currentPath=''){
		global $current_category_id;
	    if ($parentCategoryId==='') {
		return null;
	    }
	    

	    
	    //get subcategories
	    $subcategories = $this->getCategories($parentCategoryId);
	    
	    $subcats_ul = htmlBase::newElement('list');
		$hasItems = false;
	    foreach ($subcategories as $current_subcategory) {
		$hasItems = true;
		
		$subcategory_name = $current_subcategory['CategoriesDescription'][0]['categories_name'];
		$selected = '';

		$li_element = htmlBase::newElement('li');
		if ($current_category_id == $current_subcategory['categories_id']){
			$selected = 'selected';
			$li_element->addClass('selectedli');
		}
		$li_element->html(			
			'<a class="'.$selected.'" href="'.itw_app_link(null, 'index', $current_subcategory['CategoriesDescription'][0]['categories_seo_url']).'">'.
			    $subcategory_name.
			'</a>'
		);
		
		$subsubcategories_element = $this->getChildCategories($current_subcategory['categories_id']);
		if ($subsubcategories_element!==null) {
		    $li_element->append($subsubcategories_element);
		}
		
		$subcats_ul->addItemObj($li_element);
	    }
	    if($hasItems){
	        return $subcats_ul;
	    }else{
		    return '';
	    }
	}

	public function show()
	{
		$boxWidgetProperties = $this->getWidgetProperties();

		$ulElement = $this->getChildCategories(((int)$boxWidgetProperties->selected_category > 0) ? $boxWidgetProperties->selected_category : 0);

	    $this->setBoxContent('<div id="'.(isset($boxWidgetProperties->widgetId)?$boxWidgetProperties->widgetId:'').'">'.$ulElement->draw().'</div>');

	    return $this->draw();
	}	
}