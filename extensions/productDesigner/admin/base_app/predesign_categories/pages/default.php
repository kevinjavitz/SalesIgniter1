<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$lID = (int)Session::get('languages_id');
	$languages = tep_get_languages();

	$Qcategories = Doctrine_Query::create()
	->select('c.*, cd.categories_name')
	->from('ProductDesignerPredesignCategories c')
	->leftJoin('c.ProductDesignerPredesignCategoriesDescription cd')
	//->where('cd.language_id = ?', $lID)
	->orderBy('c.sort_order, cd.categories_name');

	EventManager::notify('ProductDesignerPredesignCategoryListingQueryBeforeExecute', &$Qcategories);

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

			if ((!isset($_GET['cID']) || $_GET['cID'] == $categoryId) && !isset($cInfo) && (substr($action, 0, 3) != 'new')){
				$cInfo = new objectInfo($category);
			}

			$folderIcon = htmlBase::newElement('icon')->setType('folderClosed')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'predesign_cPath', 'cID')) . tep_get_predesign_path($categoryId), null, null, 'SSL'));

			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $categoryId, null, null, 'SSL'));

			if (isset($cInfo) && $categoryId == $cInfo->categories_id) {
				$addCls = 'ui-state-default';
				$onclickLink = itw_app_link(tep_get_all_get_params(array('action', 'predesign_cPath', 'cID')) . tep_get_predesign_path($categoryId), null, null, 'SSL');
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
					array('text' => $folderIcon->draw() . $category['ProductDesignerPredesignCategoriesDescription'][Session::get('languages_id')]['categories_name']),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));
		}
	}

	$predesign_cPath_back = '';
	if (sizeof($predesign_cPath_array) > 0) {
		for ($i=0, $n=sizeof($predesign_cPath_array)-1; $i<$n; $i++) {
			if (empty($predesign_cPath_back)) {
				$predesign_cPath_back .= $predesign_cPath_array[$i];
			} else {
				$predesign_cPath_back .= '_' . $predesign_cPath_array[$i];
			}
		}
	}

	$predesign_cPath_back = (tep_not_null($predesign_cPath_back)) ? 'predesign_cPath=' . $predesign_cPath_back . '&' : '';

	$infoBox = htmlBase::newElement('infobox');

	$editButton = htmlBase::newElement('button')->usePreset('edit');
	$saveButton = htmlBase::newElement('button')->usePreset('save')->setType('submit');
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
			$infoBox->addContentRow('<b>' . $cInfo->ProductDesignerPredesignCategoriesDescription[Session::get('languages_id')]['categories_name'] . '</b>');
			break;
		case 'new':
		case 'edit':
			if ($action == 'edit'){
				$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_EDIT_CATEGORY') . '</b>');
			}else{
				$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_NEW_CATEGORY') . '</b>');
			}
			$infoBox->setForm(array(
				'name' => 'categories',
				'action' => itw_app_link(tep_get_all_get_params(array('action')) . 'action=saveCategory', null, null, 'SSL')
			));

			$deleteButton->setType('submit');

			$infoBox->addButton($saveButton)->addButton($cancelButton);
			
			$Field = htmlBase::newElement('input');
			$inputs = '';
			for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
				$lID = $languages[$i]['id'];
				$langImage = tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']);
				
				if (isset($cInfo)){
					$Field->val($cInfo->ProductDesignerPredesignCategoriesDescription[$lID]['categories_name']);
				}
				
				$inputs .= $langImage . '&nbsp;' . $Field->setName('categories_name[' . $lID . ']')->draw() . '<br />';
			}

			$sortInput = htmlBase::newElement('input')->setName('sort_order')->attr('size', 3);
			if (isset($cInfo)){
				$sortInput->val($cInfo->sort_order);
			}
			
			$infoBox->addContentRow('Category Name:<br />' . $inputs);
			$infoBox->addContentRow('Sort Order: ' . $sortInput->draw());
			break;
		default:
			$infoBox->setButtonBarLocation('top');
			if (isset($cInfo) && is_object($cInfo)) { // category info box contents
				$categoryName = $cInfo->ProductDesignerPredesignCategoriesDescription[Session::get('languages_id')]['categories_name'];
				$categoryId = $cInfo->categories_id;

				$infoBox->setHeader('<b>' . $categoryName . '</b>');

				$allGetParams = tep_get_all_get_params(array('action'));
				$editButton->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'action=edit&cID=' . $cInfo->categories_id, null, null, 'SSL'));
				$deleteButton->setHref(itw_app_link($allGetParams . 'cID=' . $cInfo->categories_id . '&action=delete_category', null, null, 'SSL'));

				$infoBox->addButton($editButton)->addButton($deleteButton);
			}else { // create category/product info
				$infoBox->setHeader('<b>' . sysLanguage::get('EMPTY_CATEGORY') . '</b>');
			}
			break;
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <table border="0" width="100%" cellspacing="0" cellpadding="3">
  <tr>
   <td class="smallText" align="right"><?php
   echo '<form name="goto" action="' . itw_app_link(null, null, null, 'SSL') . '" method="get">';
   echo sysLanguage::get('HEADING_TITLE_GOTO') . ' ' . tep_draw_pull_down_menu('predesign_cPath', tep_get_predesign_category_tree(), $current_predesign_category_id, 'onChange="this.form.submit();"');
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
    if (sizeof($predesign_cPath_array) > 0){
    	$backButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_BACK'))
    	->setHref(itw_app_link(tep_get_all_get_params(array('predesign_cPath', 'cID')) . $predesign_cPath_back . 'cID=' . $current_predesign_category_id, null, null, 'SSL'));

    	echo $backButton->draw();
    }

    if (!isset($_GET['search'])){
    	$newCatButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_NEW_CATEGORY'))
    	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'action=new', null, null, 'SSL'));

    	echo $newCatButton->draw();
    }
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>