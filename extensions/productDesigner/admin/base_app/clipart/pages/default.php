<?php

/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$categories_count = 0;
	$rows = 0;
	$lID = (int)Session::get('languages_id');

	$Qcategories = Doctrine_Query::create()
	->select('c.*, cd.categories_name')
	->from('ProductDesignerClipartCategories c')
	->leftJoin('c.ProductDesignerClipartCategoriesDescription cd')
	->where('cd.language_id = ?', $lID)
	->orderBy('c.sort_order, cd.categories_name');
	if (isset($_GET['search'])) {
		$search = tep_db_prepare_input($_GET['search']);
		$Qcategories->andWhere('cd.categories_name LIKE ?', '%' . $search . '%');
	} else {
		$Qcategories->andWhere('c.parent_id = ?', (int)$current_clipart_category_id);
	}

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 0))
	->setQuery($Qcategories);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_CATEGORIES')),			
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$categories = &$tableGrid->getResults();
	if ($categories){
		foreach($categories as $category){
			$categoryId = $category['categories_id'];
			$categories_count++;
			$rows++;

			// Get parent_id for subcategories if search
			if (isset($_GET['search'])) $clipart_cPath = $category['parent_id'];

			if ((!isset($_GET['cID']) || $_GET['cID'] == $categoryId) && !isset($cInfo) && (substr($action, 0, 3) != 'new')){
				$category_childs = array('childs_count' => tep_childs_in_clipart_category_count($categoryId));
				//$category_images = array('products_count' => tep_images_in_category_count($categoryId));

				$cInfo_array = array_merge($category, $category_childs);
				$cInfo = new objectInfo($cInfo_array);
			}

			$folderIcon = htmlBase::newElement('icon')->setType('folderClosed')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'clipart_cPath', 'cID')) .tep_get_clipart_path($categoryId), null, null, 'SSL'));

			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $categoryId, null, null, 'SSL'));

			if (isset($cInfo)&& $categoryId == $cInfo->categories_id) {
				
				$addCls = 'ui-state-default';
				$onclickLink = itw_app_link(tep_get_all_get_params(array('action', 'clipart_cPath', 'cID')) . tep_get_clipart_path($categoryId), null, null, 'SSL');
				$arrowIcon->setType('circleTriangleEast');
			}else{
				$addCls = '';
				$onclickLink = itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $categoryId, null, null, 'SSL');
				$arrowIcon->setType('info');
			}

			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'document.location=\'' . $onclickLink . '\'',
				'columns' => array(
					array('text' => $folderIcon->draw() . $category['ProductDesignerClipartCategoriesDescription'][Session::get('languages_id')]['categories_name']),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));
		}
	}

	$clipart_cPath_back = '';
	if (isset($clipart_cPath_array)){
		if (sizeof($clipart_cPath_array) > 0){
			for ($i=0, $n=sizeof($clipart_cPath_array)-1; $i<$n; $i++){
				if (empty($clipart_cPath_back)){
					$clipart_cPath_back .= $clipart_cPath_array[$i];
				}else{
					$clipart_cPath_back .= '_' . $clipart_cPath_array[$i];
				}
			}
		}
	}

	$clipart_cPath_back = (tep_not_null($clipart_cPath_back)) ? 'clipart_cPath=' . $clipart_cPath_back . '&' : '';

	$infoBox = htmlBase::newElement('infobox');

	$editButton = htmlBase::newElement('button')->usePreset('edit');
	$deleteButton = htmlBase::newElement('button')->usePreset('delete');	
	if (!empty($action)){
		$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
		->setHref(itw_app_link(tep_get_all_get_params(array('action')), null, null, 'SSL'));
	}

	switch ($action) {
		case 'delete_category':
			$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_DELETE_CATEGORY') . '</b>');
			$infoBox->setForm(array(
				'name' => 'categories',
				'action' => itw_app_link(tep_get_all_get_params(array('action')) . 'action=deleteCategoryConfirm', null, null, 'SSL')
			));

			$deleteButton->setType('submit');

			$infoBox->addButton($deleteButton)->addButton($cancelButton);

			$infoBox->addContentRow(sysLanguage::get('TEXT_DELETE_CATEGORY_INTRO') . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
			$infoBox->addContentRow('<b>' . $cInfo->ProductDesignerClipartCategoriesDescription[Session::get('languages_id')]['categories_name'] . '</b>');

			if ($cInfo->childs_count > 0){
				$infoBox->addContentRow(sprintf(sysLanguage::get('TEXT_DELETE_WARNING_CHILDS'), $cInfo->childs_count));
			}

			/*if ($cInfo->images_count > 0){
				$infoBox->addContentRow(sprintf(sysLanguage::get('TEXT_DELETE_WARNING_PRODUCTS'), $cInfo->images_count));
			}*/
			break;
		
		default:
			$infoBox->setButtonBarLocation('top');
			if (isset($cInfo) && is_object($cInfo)) { // category info box contents
				$categoryName = $cInfo->ProductDesignerClipartCategoriesDescription[Session::get('languages_id')]['categories_name'];
				$categoryId = $cInfo->categories_id;

				$infoBox->setHeader('<b>' . $categoryName . '</b>');

				$allGetParams = tep_get_all_get_params(array('action'));
				$editButton->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $cInfo->categories_id, null, 'new_category', 'SSL'));
				$deleteButton->setHref(itw_app_link($allGetParams . 'cID=' . $cInfo->categories_id . '&action=delete_category', null, null, 'SSL'));

				$infoBox->addButton($editButton)->addButton($deleteButton);
				$infoBox->addContentRow(tep_info_image($cInfo->categories_image, $categoryName, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT) . '<br>' . $cInfo->categories_image);
				$infoBox->addContentRow(sysLanguage::get('TEXT_SUBCATEGORIES') . ' ' . $cInfo->childs_count);
			}else { // create category/product info
				$infoBox->setHeader('<b>' . sysLanguage::get('EMPTY_CATEGORY') . '</b>');
				$infoBox->addContentRow(sysLanguage::get('TEXT_NO_CHILD_CATEGORIES'));
			}
			break;
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
<table border="0" width="100%" cellspacing="0" cellpadding="3">
  <tr>
   <td class="smallText" align="right"><?php
   echo '<form name="goto" action="' . itw_app_link(tep_get_all_get_params(array('action', 'clipart_cPath', 'cID')), null, null, 'SSL') . '" method="get">';
   echo sysLanguage::get('HEADING_TITLE_GOTO') . ' ' . tep_draw_pull_down_menu('clipart_cPath', tep_get_admin_clipart_category_tree(), $current_clipart_category_id, 'onChange="this.form.submit();"');
   echo '</form>';
   ?></td>
  </tr>
 </table>
 <table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
  </tr>
 </table>

 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   </div>
  </div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td align="right" class="smallText"><?php
	if(isset($clipart_cPath_array))
    if (sizeof($clipart_cPath_array) > 0){
    	$backButton = htmlBase::newElement('button')->usePreset('back')
    	->setHref(itw_app_link(tep_get_all_get_params(array('clipart_cPath', 'cID')) . $clipart_cPath_back . 'cID=' . $current_clipart_category_id, null, null, 'SSL'));

    	echo $backButton->draw();
    }

    if (!isset($_GET['search'])){
    	$newCatButton = htmlBase::newElement('button')->usePreset('install')->setText(sysLanguage::get('TEXT_BUTTON_NEW_CATEGORY'))
    	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')), null, 'new_category', 'SSL'));

    	echo $newCatButton->draw();
    }
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>