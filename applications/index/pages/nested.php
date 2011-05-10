<?php
if ($category_depth == 'nested') {
	ob_start();
	$new_products_category_id = $current_category_id; //For new products module

	$Qcategory = Doctrine_Query::create()
	->select('cd.categories_name, c.categories_image, cd.categories_htc_title_tag, cd.categories_description')
	->from('Categories c')
	->leftJoin('c.CategoriesDescription cd')
	->where('c.categories_id = ?', (int)$current_category_id)
	->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
	->orderBy('cd.categories_name')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$pageTitle = $Qcategory[0]['CategoriesDescription'][0]['categories_htc_title_tag'];
	if (tep_not_null($Qcategory[0]['CategoriesDescription'][0]['categories_description'])){
		echo '<div class="pageHeadingSub">' . $Qcategory[0]['CategoriesDescription'][0]['categories_description'] . '</div><br />';
	}

	EventManager::notify('IndexNestedListingBeforeListing');

	$Qchildren = Doctrine_Query::create()
	->select('c.categories_id, cd.categories_name, c.categories_image')
	->from('Categories c')
	->leftJoin('c.CategoriesDescription cd')
	->where('c.parent_id = ?', (int)$current_category_id)
	->andWhere('cd.language_id=?', Session::get('languages_id'))
	->orderBy('cd.categories_name')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qchildren){
		$categoryTable = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0)
		->css(array(
			'width' => '100%'
		));

		$col = 0;
		$tableColumns = array();
		foreach($Qchildren as $cInfo){
			$categoryId = $cInfo['categories_id'];
			$categoryImage = $cInfo['categories_image'];
			$categoryName = $cInfo['CategoriesDescription'][0]['categories_name'];

			$cPath_new = tep_get_path($categoryId);

			$tableColumns[] = array(
				'addCls' => 'main',
				'align' => 'center',
				'text' => '<a href="' .  itw_app_link($cPath_new, 'index', 'default') . '">' .
						  '<img src="imagick_thumb.php?path=rel&imgSrc=' . 'images/'. $categoryImage . '&width=100&height=100" alt="' . $categoryName . '" />' .
						'<br />' . $categoryName . '</a>'
			);

			$col++;
			if ($col > sysConfig::get('MAX_DISPLAY_CATEGORIES_PER_ROW')){
				$categoryTable->addBodyRow(array(
					'columns' => $tableColumns
				));
				$tableColumns = array();
				$col = 0;
			}
		}
		if (sizeof($tableColumns) > 0){
			$categoryTable->addBodyRow(array(
				'columns' => $tableColumns
			));
		}
		echo $categoryTable->draw() . '<br />';
	}
	EventManager::notify('IndexNestedListingAfterListing');

	echo '<div>';
	if (sysConfig::get('NEW_PRODUCTS_ON_NESTED_CATEGORIES') == 'true'){
		include(DIR_WS_MODULES . FILENAME_NEW_PRODUCTS);
	}
	echo '</div>';
	
	$pageContents = ob_get_contents();
	ob_end_clean();
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
}
