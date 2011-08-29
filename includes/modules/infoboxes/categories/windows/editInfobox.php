<?php
$lID = (int)Session::get('languages_id');
function getCategoryTree($parentId, $namePrefix = '', &$categoriesTree){
	global $lID, $allGetParams, $cInfo;
	$Qcategories = Doctrine_Query::create()
			->select('c.*, cd.categories_name')
			->from('Categories c')
			->leftJoin('c.CategoriesDescription cd')
			->where('cd.language_id = ?', $lID)
			->andWhere('c.parent_id = ?', $parentId)
			->orderBy('c.sort_order, cd.categories_name');

	EventManager::notify('CategoryListingQueryBeforeExecute', &$Qcategories);

	$Result = $Qcategories->execute();
	if ($Result->count() > 0){
		foreach($Result->toArray(true) as $Category){
			if ($Category['parent_id'] > 0){
				//$namePrefix .= '&nbsp;';
			}

			$categoriesTree[] = array(
				'categoryId'           => $Category['categories_id'],
				'categoryName'         => $namePrefix . $Category['CategoriesDescription'][Session::get('languages_id')]['categories_name'],
			);

			getCategoryTree($Category['categories_id'], '&nbsp;&nbsp;&nbsp;' . $namePrefix, &$categoriesTree);
		}
	}
}

$categoryTreeList = false;
getCategoryTree(0,'',&$categoryTreeList);
$selectedCategory = isset($WidgetSettings->selected_category)?$WidgetSettings->selected_category:'';

$categoryTree = htmlBase::newElement('selectbox')
		->setName('selected_category')
		->setId('selectedCategory')
		->setLabel(sysLanguage::get('TEXT_SELECT_PARENT_CATEGORY'))
		->setLabelPosition('before');
foreach($categoryTreeList as $category){
	$categoryTree->addOption($category['categoryId'], $category['categoryName']);
}
if(isset($selectedCategory)){
	$categoryTree->selectOptionByValue($selectedCategory);
}

$WidgetSettingsTable->addBodyRow(array(
                                      'columns' => array(
	                                      array('colspan' => 2, 'text' => $categoryTree->draw())
                                      )
                                 ));

?>