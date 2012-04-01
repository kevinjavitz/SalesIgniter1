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

	public function buildStylesheet(){
		$boxWidgetProperties = $this->getWidgetProperties();
		$idW = (isset($boxWidgetProperties->widgetId)?$boxWidgetProperties->widgetId:'menuCategories');
		$css =

			'#'.$idW.' .ui-icon { ' .
			'margin-right: .3em;float:right;text-indent:0px;' .
			' }' . "\n" .

			'#'.$idW.' .ui-icon-triangle-1-n{'.
			'display:inline;' .
			' }' . "\n" .
			'#'.$idW.' .ui-icon-triangle-1-s{'.
				'display:inline;' .
			' }' . "\n"
			.'' . "\n";
		ob_start();
		?>
	<?php
		$cssSource = ob_get_contents();
		ob_end_clean();
		$css .= $cssSource;

		return $css;
	}

	public function buildJavascript(){
		$boxWidgetProperties = $this->getWidgetProperties();
		ob_start();
		?>
	$('.isCollapsible').hide();
	$('.isCollapsible').prev().click(function(){
		if($(this).hasClass('isOpen')){
			$(this).find('.ui-icon').addClass('ui-icon-circle-triangle-s');
			$(this).find('.ui-icon').removeClass('ui-icon-circle-triangle-n');
			$(this).removeClass('isOpen');
			$(this).next().hide();
		}else{
			$(this).find('.ui-icon').removeClass('ui-icon-circle-triangle-s');
			$(this).find('.ui-icon').addClass('ui-icon-circle-triangle-n');
			$(this).addClass('isOpen');
			$(this).next().show();
		}
		return false;
	});

	$('a.selected').closest('.isCollapsible').prev().trigger('click');

	<?php
 		$javascript = ob_get_contents();
		ob_end_clean();

		return $javascript;
	}


	public function getChildCategories($parentCategoryId, $currentPath=''){
		global $current_category_id;
	    if ($parentCategoryId==='') {
		return null;
	    }
	    $boxWidgetProperties = $this->getWidgetProperties();

	    $excludedCategories = isset($boxWidgetProperties->excludedCategories)?explode(';',$boxWidgetProperties->excludedCategories):array();

	    
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
		}
		$extraIcon = '';
		if(in_array($current_subcategory['categories_id'], $excludedCategories)){
		    $extraIcon = '<span class="ui-icon ui-icon-circle-triangle-s"></span>';
		}

		$li_element->html(			
			'<a class="'.$selected.'" href="'.itw_app_link(null, 'index', $current_subcategory['CategoriesDescription'][0]['categories_seo_url']).'">'.
			    $subcategory_name. $extraIcon.
			'</a>'
		);
		
		$subsubcategories_element = $this->getChildCategories($current_subcategory['categories_id']);

		if ($subsubcategories_element!==null) {
			if(in_array($current_subcategory['categories_id'], $excludedCategories)){
				$subsubcategories_element->addClass('isCollapsible');

				$li_element1 = htmlBase::newElement('li');
				$li_element1->html(
					'<a href="'.itw_app_link(null, 'index', $current_subcategory['CategoriesDescription'][0]['categories_seo_url']).'">'.
						'View All'.
						'</a>'
				);
				if(is_object($subsubcategories_element)){
					$subsubcategories_element->addItemObj($li_element1);
				}
			}
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

		$excludedCategories = isset($boxWidgetProperties->excludedCategories)?explode(';',$boxWidgetProperties->excludedCategories):array();
		/*$catArrExcl = array();
		foreach($excludedCategories as $catExcl){
			$catArr = tep_get_categories('', $catExcl);
			$catArrExcl[] = $catExcl;
			foreach($catArr as $catA){
				$catArrExcl[] = $catA['id'];
			}
		} */
		$ulElement = $this->getChildCategories((isset($boxWidgetProperties->selected_category) && (int)$boxWidgetProperties->selected_category > 0) ? $boxWidgetProperties->selected_category : 0);

	    $this->setBoxContent('<div id="'.(isset($boxWidgetProperties->widgetId)?$boxWidgetProperties->widgetId:'').'">'.$ulElement->draw().'</div>');

	    return $this->draw();
	}	
}