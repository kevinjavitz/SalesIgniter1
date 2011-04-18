<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class infoBoxCategories extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('categories');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_CATEGORIES'));
		$this->buildStylesheetMultiple = false;
		$this->buildJavascriptMultiple = false;
	}

	public function show(){
		global $cPath, $cPath_array, $tree, $categoriesString, $current_category_id;

		$Qcategories = Doctrine_Query::create()
		->select('c.categories_id, cd.categories_name, c.parent_id')
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('c.parent_id = ?', '0')
		->andWhere('(c.categories_menu = "infobox" or c.categories_menu = "both")')
		->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
		->orderBy('c.sort_order, cd.categories_name');

		EventManager::notify('CategoryQueryBeforeExecute', $Qcategories);

		$Result = $Qcategories->execute(array(), Doctrine::HYDRATE_ARRAY);

		$menuContainer = htmlBase::newElement('div')
		->attr('id', 'categoriesBoxMenu');

		if ($Result){
			foreach($Result as $idx => $cInfo){
				$categoryId = $cInfo['categories_id'];
				$parentId = $cInfo['parent_id'];
				$categoryName = $cInfo['CategoriesDescription'][0]['categories_name'];

				$headerEl = htmlBase::newElement('h3');
				if (isset($cPath_array) && $cPath_array[0] == $categoryId){
					$headerEl->addClass('currentCategory');
				}
				$headerEl->html($categoryName);

				$Qchildren = Doctrine_Query::create()
				->select('c.categories_id, cd.categories_name, c.parent_id')
				->from('Categories c')
				->leftJoin('c.CategoriesDescription cd')
				->where('c.parent_id = ?', $categoryId)
				->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
				->orderBy('c.sort_order, cd.categories_name');

				EventManager::notify('CategoryQueryBeforeExecute', &$Qchildren);
				$currentChildren = $Qchildren->execute();

				$flyoutContainer = htmlBase::newElement('div');
				$ulElement = htmlBase::newElement('list');
				if ($currentChildren->count() > 0){
					foreach($currentChildren->toArray() as $child){
						addChildren($child, $categoryId, &$ulElement);
					}
				}else{
					$childLinkEl = htmlBase::newElement('a')
					->addClass('ui-widget ui-widget-content ui-corner-all')
					->css('border-color', 'transparent')
					->html('<span class="ui-icon ui-icon-triangle-1-e ui-icon-categories-bullet" style="vertical-align:middle;"></span><span class="ui-categories-text" style="vertical-align:middle;">'.sysLanguage::get('INFOBOX_CATEGORIES_VIEW_PRODUCTS').'</span>')
					->setHref(itw_app_link('cPath=' . $categoryId, 'index', 'default'));

					$liElement = htmlBase::newElement('li')
					->append($childLinkEl);
					$ulElement->addItemObj($liElement);
				}
				$flyoutContainer->append($ulElement);

				$menuContainer->append($headerEl)->append($flyoutContainer);
			}
		}

		$this->setBoxContent($menuContainer->draw() . '<div style="text-align:center;font-size:.8em;font-weight:bold;margin:.5em;"><a href="' . itw_app_link(null, 'products', 'all', 'NONSSL') . '">' . sysLanguage::get('INFOBOX_CATEGORIES_ALL_PRODUCTS') . '</a></div>');

		return $this->draw();
	}
	
	public function buildStylesheet(){
		$css = '' . "\n" . 
		'#categoriesBoxMenu .ui-accordion-header { ' .
			'color:#ffffff;' . 
			'font-weight:bold;' . 
			'margin:0;' . 
			'padding: .5em;' . 
		' }' . "\n" . 
		'#categoriesBoxMenu .ui-accordion-header.ui-state-hover { ' .
			'background-color: #f6a864;' . 
		' }' . "\n" . 
		'#categoriesBoxMenu .ui-accordion-header.ui-state-active { ' .
			'border-color: transparent;' . 
			'background-color: #f6a864;' . 
		' }' . "\n" . 
		'#categoriesBoxMenu .ui-accordion-header .ui-icon { ' .
			'position:relative;' . 
			'float:right;' . 
			'margin-top:0em;' . 
			'margin-right: .5em;' . 
			'top: 0;' . 
			'background-image: url(/ext/jQuery/themes/icons/ui-icons_ffffff_256x240.png);' . 
		' }' . "\n" . 
		'#categoriesBoxMenu .ui-accordion-header.ui-corner-all { ' .
			buildBorderRadius('0px', '0px', '0px', '0px') . 
			'border-top: none;' . 
			'border-left: none;' . 
			'border-right: none;' . 
			'border-color: #ffffff;' . 
		' }' . "\n" . 
		'#categoriesBoxMenu .ui-accordion-content { ' .
			'padding: 0;' . 
			'margin: 0;' . 
			'border:none;' . 
			'background: transparent;' . 
			buildBorderRadius('0px', '0px', '0px', '0px') . 
			'overflow:visible;' . 
		' }' . "\n" . 
		'#categoriesBoxMenu .ui-accordion-content ul { ' .
			'list-style: none;' . 
			'padding: 0;' . 
			'margin: .1em;' . 
		' }' . "\n" . 
		'#categoriesBoxMenu .ui-accordion-content li { ' .
			'font-size: 1em;' . 
			'padding: .1em 0;' . 
		' }' . "\n" . 
		'#categoriesBoxMenu .ui-accordion-content li ul { ' .
			'width: 150px;' . 
			'padding: .2em;' . 
		' }' . "\n" . 
		'#categoriesBoxMenu .ui-accordion-content li a { ' .
			'text-decoration: none;' . 
			'display:block;' . 
			'padding: .1em;' . 
			'margin-left: auto;' . 
			'margin-right: auto;' . 
		' }' . "\n" . 
		'#categoriesBoxMenu .ui-accordion-content li .ui-icon { ' .
			'margin-right: .3em;' . 
		' }' . "\n" . 
		'#categoriesBoxMenu .ui-accordion-content li a:hover, ' .
		'#categoriesBoxMenu .ui-accordion .ui-accordion-content li a.selected { ' .
			'background: #e6e6e6;' . 
		' }' . "\n" . 
		'#categoriesBoxMenu .ui-accordion-content li .ui-icon { ' .
			'text-indent:0px;' . 
		' }' . "\n" . 
		'' . "\n";
		
		return $css;
	}
}
?>