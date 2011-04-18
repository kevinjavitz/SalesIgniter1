<?php
	$QcategoriesPages = Doctrine_Query::create()
	->from('CategoriesPages');
	
	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($QcategoriesPages);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_PAGE_KEY_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));
	
	$categoriesPages = &$tableGrid->getResults();
	if ($categoriesPages){
		foreach($categoriesPages as $cInfo){
			$categoriesPagesId = $cInfo['categories_pages_id'];
		
			if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $categoriesPagesId))) && !isset($cObject)){
				$cObject = new objectInfo($cInfo);
			}
		
			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $categoriesPagesId));

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $categoriesPagesId);
			if (isset($cObject) && $categoriesPagesId == $cObject->categories_pages_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'action=edit&cID=' . $categoriesPagesId);
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}
		
			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $cInfo['page_title']),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));
		}
	}
	
	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setButtonBarLocation('top');

	switch ($action){
		case 'edit':
			$infoBox->setForm(array(
								'action'    => itw_app_link(tep_get_all_get_params(array('action')) . 'action=save'),
								'method'    =>  'post',
								'name'      => 'edit_categories_pages'
							)
			);

		 	 if (isset($_GET['cID'])) {
		            $categoriesPages = Doctrine_Core::getTable('CategoriesPages')->find($_GET['cID']);
				    $pageKey = $categoriesPages->page_key;
		            $pageTitle = $categoriesPages->page_title;

		            $categories = explode(',', $categoriesPages->categories);
				    $infoBox->setHeader('<b>Edit Categories Page</b>');
			 }else{
			  	    $pageKey = "";
				    $infoBox->setHeader('<b>New Categories Pages</b>');
			 }

			$htmlPageKey = htmlBase::newElement('input')
			            ->setLabel('Page key: ')
						->setLabelPosition('before')
					    ->setName('page_key')
					    ->setValue($pageKey);
			$htmlPageTitle = htmlBase::newElement('input')
			            ->setLabel('Page Title: ')
						->setLabelPosition('before')
					    ->setName('page_title')
					    ->setValue($pageTitle);



 			 $saveButton = htmlBase::newElement('button')
					        ->setType('submit')
					        ->usePreset('save');
			 $cancelButton = htmlBase::newElement('button')
					        ->usePreset('cancel')
			                ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));

			 $infoBox->addContentRow($htmlPageKey->draw());
			 $infoBox->addContentRow($htmlPageTitle->draw());
			 $infoBox->addContentRow(tep_get_category_tree_list('0', $categories));
			 $infoBox->addButton($saveButton)->addButton($cancelButton);

			 break;
		default:
			if (isset($cObject) && is_object($cObject)) {
				$infoBox->setHeader('<b>' . $cObject->page_key. '</b>');
				
				$deleteButton = htmlBase::newElement('button')
								->setType('submit')
								->usePreset('delete')
								->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'action=deleteConfirm&cID=' . $cObject->categories_pages_id));
				$editButton = htmlBase::newElement('button')
								->setType('submit')
								->usePreset('edit')
								->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'action=edit' . '&cID=' . $cObject->categories_pages_id, 'manage', 'default'));
				
				$infoBox->addButton($editButton)->addButton($deleteButton);

			}
			break;
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
  </div>
  <div style="text-align:right;"><?php
  	echo htmlBase::newElement('button')
		    ->usePreset('new')
		    ->setText('New Categories Pages')
  	        ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'action=edit', null, 'default', 'SSL'))
  	        ->draw();
  ?></div>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>