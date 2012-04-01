<?php
	function buildDefaultInfobox($settings){
		global $allGetParams;
		if (!empty($settings['categoryImage']) && file_exists(sysConfig::getDirFsCatalog() . 'images/' . $settings['categoryImage'])){
			$imageHtml = htmlBase::newElement('image')
			->setSource(sysConfig::getDirWsCatalog() . 'images/' . $settings['categoryImage'])
			->setWidth(sysConfig::get('SMALL_IMAGE_WIDTH'))
			->setHeight(sysConfig::get('SMALL_IMAGE_HEIGHT'))
			->thumbnailImage(true);
		}else{
			$imageHtml = htmlBase::newElement('span')
			->addClass('main')
			->html('Image Does Not Exist');
		}
		
		$infoBox = htmlBase::newElement('infobox');
		$infoBox->setHeader('<b>' . $settings['categoryName'] . '</b>');

		$infoBox->addContentRow($imageHtml->draw() . '<br>' . $settings['categoryImage']);
		$infoBox->addContentRow(sysLanguage::get('TEXT_SUBCATEGORIES') . ' ' . $settings['categoryChildren']);
		
		return $infoBox->draw();
	}
	
	function addCategoryTreeToGrid($parentId, &$tableGrid, &$infoBoxes, $namePrefix = ''){
		global $lID, $allGetParams, $cInfo;
		$Qcategories = Doctrine_Query::create()
		->from('BlogCategories c')
		->leftJoin('c.BlogCategoriesDescription cd')
		->where('cd.language_id = ?', $lID)
		->andWhere('c.parent_id = ?', $parentId)
		->orderBy('c.sort_order, cd.blog_categories_title');

		if(isset($_GET['key'])){
			$Qcategories->andWhere('c.extra_key = ?', $_GET['key']);
		}

		EventManager::notify('BlogCategoryListingQueryBeforeExecute', &$Qcategories);
		
		$Result = $Qcategories->execute();
		if ($Result->count() > 0){
			foreach($Result->toArray(true) as $Category){
				if ($Category['parent_id'] > 0){
					//$namePrefix .= '&nbsp;';
				}
				
				$infoBoxSettings = array(
					'categoryId'           => $Category['blog_categories_id'],
					'categoryImage'        => $Category['categories_image'],
					'categoryName'         => $Category['BlogCategoriesDescription'][Session::get('languages_id')]['blog_categories_title'],
					'categoryChildren'     => tep_childs_in_blog_category_count($Category['blog_categories_id'])
				);

				if ((isset($_GET['cID']) && $_GET['cID'] == $Category['blog_categories_id'])){
					$cInfo = $infoBoxSettings;
				}
				
				// Get parent_id for subcategories if search
				if (isset($_GET['search'])) $cPath = $Category['parent_id'];

				$category_childs = array('childs_count' => $infoBoxSettings['categoryChildren']);

				$folderIcon = htmlBase::newElement('icon')->setType('folderClosed');

				$insertIcon = htmlBase::newElement('icon')->setType('insert')->addClass('insertIcon')->attr('tooltip', 'Add Child');
				$editIcon = htmlBase::newElement('icon')->setType('edit')->addClass('editIcon')->attr('tooltip', 'Edit');
				$deleteIcon = htmlBase::newElement('icon')->setType('delete')->addClass('deleteIcon')->attr('tooltip', 'Delete');
				
				$tableGrid->addBodyRow(array(
					'addCls' => ($parentId > 0 ? 'child-of-node-' . $parentId : ''),
					'rowAttr' => array(
						'id' => 'node-' . $infoBoxSettings['categoryId'],
						'infobox_id' => $infoBoxSettings['categoryId'],
						'data-category_id' => $Category['blog_categories_id']
					),
					'columns' => array(
						array('text' => $namePrefix . $folderIcon->draw() . '<span class="categoryListing-name">' . $infoBoxSettings['categoryName'] . '</span>'),
						array('align' => 'right', 'text' => $insertIcon->draw() . $editIcon->draw() . $deleteIcon->draw())
					)
				));
			
				$infoBoxes[$infoBoxSettings['categoryId']] = buildDefaultInfobox($infoBoxSettings);
				
				addCategoryTreeToGrid($Category['blog_categories_id'], &$tableGrid, &$infoBoxes, '&nbsp;&nbsp;&nbsp;' . $namePrefix);
			}
		}
	}
	
	$categories_count = 0;
	$rows = 0;
	$lID = (int)Session::get('languages_id');

	$tableGrid = htmlBase::newElement('newGrid');

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_CATEGORIES')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));
	
	$allGetParams = tep_get_all_get_params(array('cID', 'action'));

	$infoBoxes = array();
	addCategoryTreeToGrid(0, $tableGrid, $infoBoxes, '');
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   </div>
  </div>
  <div style="text-align:right;"><?php
   	echo htmlBase::newElement('button')
   	->usePreset('install')
   	->setText('New Root Category')
   	->css(array(
   		'margin' => '.5em'
   	))
   	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')), null, 'new_category', 'SSL'))
   	->draw();
  ?></div>
 </div>
 <div style="width:25%;float:right;"><?php
 	if (sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $infoBoxId => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $infoBoxId . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>