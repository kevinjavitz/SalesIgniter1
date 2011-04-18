<?php
	function addChildren_Custom($child, $currentPath, &$ulElement) {
		global $current_category_id;
		$currentPath .= '_' . $child['categories_id'];

		$childLinkEl = htmlBase::newElement('a')
		->addClass('ui-corner-all')
		->html('<img src="' . DIR_WS_TEMPLATES . 'images/header_menu_bullet.png" valign="middle" hspace="2" vspace="2" />' . $child['CategoriesDescription'][Session::get('languages_id')]['categories_name'])
		->setHref(itw_app_link('cPath=' . $currentPath, 'index', 'default'));

		if ($child['categories_id'] == $current_category_id){
			$childLinkEl->addClass('selected');
		}

		$Qchildren = Doctrine_Query::create()
		->select('c.categories_id, cd.categories_name, c.parent_id')
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('c.parent_id = ?', $child['categories_id'])
		->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
		->orderBy('c.sort_order, cd.categories_name');

		EventManager::notify('CategoryQueryBeforeExecute', $Qchildren);
		$currentParentChildren = $Qchildren->execute();

		$children = false;
		if ($currentParentChildren->count() > 0){
			$childLinkEl->addClass('ui-menu-indicator')
			->html('<span><img src="' . DIR_WS_TEMPLATES . 'images/header_menu_bullet.png" valign="middle" hspace="2" vspace="2" />' . $child['CategoriesDescription'][Session::get('languages_id')]['categories_name'] . '</span><span style="float:right;" class="ui-icon ui-icon-triangle-1-e"></span>');

			$children = htmlBase::newElement('list')->addClass('ui-widget ui-corner-all ui-menu-flyout');
			foreach($currentParentChildren->toArray(true) as $childInfo){
				addChildren_Custom($childInfo, $currentPath, &$children);
			}
		}

		$liElement = htmlBase::newElement('li')
		->append($childLinkEl);
		if ($children){
			$liElement->append($children);
		}
		if ($ulElement->hasListItems()){
			$liElement->css(array(
				'border-top' => '1px solid #313332'
			));
		}
		$ulElement->addItemObj($liElement);
	}

	$categoriesString = '';
	$tree = array();

	$Qcategories = Doctrine_Query::create()
	->select('c.categories_id, cd.categories_name, c.parent_id')
	->from('Categories c')
	->leftJoin('c.CategoriesDescription cd')
	->where('c.parent_id = ?', '0')
	->andWhere('(c.categories_menu = "top" or c.categories_menu = "both")')
	->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
	->orderBy('c.sort_order, cd.categories_name');

	EventManager::notify('CategoryQueryBeforeExecute', $Qcategories);

	$Result = $Qcategories->execute();

	$menuContainer = htmlBase::newElement('div')
	->attr('id', 'headerMenu')
	->addClass('ui-widget ui-widget-header ui-corner-all');

	$linkEl = htmlBase::newElement('a')
	->addClass('ui-corner-all headerMenuHeading')
	->html(sysLanguage:.get('HEADER_LINK_HOME'))
	->setHref(itw_app_link(null, 'index', 'default'));

	$homeBlock = htmlBase::newElement('div')
	->addClass('headerMenuHeadingBlock')
	->append($linkEl);

	$menuContainer->append($homeBlock);

	if ($Result){
		foreach($Result->toArray(true) as $cInfo){
			$categoryId = $cInfo['categories_id'];
			$parentId = $cInfo['parent_id'];
			$categoryName = $cInfo['CategoriesDescription'][Session::get('languages_id')]['categories_name'];

			$linkEl = htmlBase::newElement('a')
			->addClass('ui-corner-all headerMenuHeading')
			->html($categoryName)
			->setHref(itw_app_link('cPath=' . $categoryId, 'index', 'default'));

			$menuBlock = htmlBase::newElement('div')
			->addClass('headerMenuHeadingBlock')
			->append($linkEl);

			$Qchildren = Doctrine_Query::create()
			->select('c.categories_id, cd.categories_name, c.parent_id')
			->from('Categories c')
			->leftJoin('c.CategoriesDescription cd')
			->where('c.parent_id = ?', $categoryId)
			->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
			->orderBy('c.sort_order, cd.categories_name');

			EventManager::notify('CategoryQueryBeforeExecute', $Qchildren);
			$currentChildren = $Qchildren->execute();

			if ($currentChildren->count() > 0){
				$flyoutContainer = htmlBase::newElement('div')
				->addClass('ui-menu-container ui-widget ui-widget-content ui-corner-bottom ui-menu-flyout');

				$menuList = htmlBase::newElement('list')
				->addClass('ui-menu ui-corner-all')
				->css(array(
					'width' => '150px'
				));

				foreach($currentChildren->toArray(true) as $child){
					addChildren_Custom($child, $categoryId, &$menuList);
				}

				$flyoutContainer->append($menuList);
				$menuBlock->append($flyoutContainer);
			}

			$separator = htmlBase::newElement('div')->addClass('headerMenuHeadingSeparator')->html('/');

			$menuContainer->append($separator)->append($menuBlock);
		}
	}
?>