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
		EventManager::notify('CategoryQueryBeforeExecute', $Qcategories);
	    
		$Qcategories = Doctrine_Query::create()
		    ->select('c.categories_id, cd.categories_name, c.parent_id')
		    ->from('Categories c')
		    ->leftJoin('c.CategoriesDescription cd')
		    ->where('c.parent_id = ?', $parentId)
		    ->andWhere('(c.categories_menu = "infobox" or c.categories_menu = "both")')
		    ->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
		    ->orderBy('c.sort_order, cd.categories_name');	    
		
		return $Qcategories->execute(array(), Doctrine::HYDRATE_ARRAY);
	}
	
	public function getChildCategories($parentCategoryId, $currentPath='')
	{	    
	    if ($parentCategoryId==='') {
		return null;
	    }
	    
	    $current_path = $currentPath;
	    if ($current_path!=='') {
		$current_path .= '_';
	    }
	    
	    //get subcategories
	    $subcategories = $this->getCategories($parentCategoryId);
	    
	    $subcats_ul = htmlBase::newElement('list');
	    foreach ($subcategories as $current_subcategory) {
		$current_subcategory_path = $current_path.$current_subcategory['categories_id'];
		
		$subcategory_name = $current_subcategory['CategoriesDescription'][0]['categories_name'];
		
		$li_element = htmlBase::newElement('li');		
		$li_element->html(			
			'<a href="'.itw_app_link('cPath=' . $current_subcategory_path, 'index', 'default').'">'.
			    $subcategory_name.
			'</a>'
		);
		
		$subsubcategories_element = $this->getChildCategories($current_subcategory['categories_id'], $current_subcategory_path);
		if ($subsubcategories_element!==null) {
		    $li_element->append($subsubcategories_element);
		}
		
		$subcats_ul->addItemObj($li_element);
	    }
	    
	    return $subcats_ul;    	    
	}

	public function show()
	{	    
	    $ulElement = $this->getChildCategories(0);
	    $this->setBoxContent($ulElement->draw());

	    return $this->draw();
	}	
}