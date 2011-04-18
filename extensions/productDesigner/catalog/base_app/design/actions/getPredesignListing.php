<?php
	if (isset($_GET['products_id'])){
		$QpredesignClasses = Doctrine_Query::create()
		->select('product_designer_predesign_classes')
		->from('Products')
		->where('products_id = ?', $_GET['products_id'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($QpredesignClasses){
			$productsPredesignClasses = explode(',', $QpredesignClasses[0]['product_designer_predesign_classes']);
		}
	}
	
	$listingTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
	$Qpredesigns = Doctrine_Query::create()
	->select('p.*')
	->from('ProductDesignerPredesigns p')
	->where('predesign_location = ?', $_GET['location']);
	
	if (isset($_GET['category']) && $_GET['category'] != 'all'){
		$Qpredesigns->leftJoin('p.ProductDesignerPredesignsToPredesignCategories p2c')
		->andWhere('p2c.categories_id = ?', $_GET['category']);
	}
	
	if (isset($_GET['activity'])){
		$Qpredesigns->andWhere('FIND_IN_SET("' . $_GET['activity'] . '", p.predesign_activities)');
	}
	
	if (isset($productsPredesignClasses) && !empty($productsPredesignClasses)){
		$classSql = array();
		foreach($productsPredesignClasses as $classId){
			$classSql[] = 'FIND_IN_SET("' . $classId . '", p.predesign_classes)';
		}
		
		if (!empty($classSql)){
			$Qpredesigns->andWhere('(' . implode(' or ', $classSql) . ')');
		}
	}
	
	$Result = $Qpredesigns->execute();
	if ($Result->count() > 0 || $_GET['location'] == 'back'){
		$row1 = array();
		$row2 = array();
		$i=0;
		if ($_GET['location'] == 'back'){
			$image = htmlBase::newElement('img')
			->addClass('predesign ui-state-active')
			->attr('predesign_id', 'none')
			->attr('src', itw_app_link('appExt=productDesigner&width=80&height=80&predesign_id=none', 'predesign_thumb', 'process'));
			
			$row1[] = array('css' => array('width' => '80px', 'height' => '80px'), 'align' => 'center', 'text' => $image->draw() . '<input type="radio" name="predesign_' . $_GET['location'] . '" value="none" style="display:none;" />');
			$i++;
		}
		
		if ($Result->count() > 0){
			foreach($Result->toArray() as $design){
				if (!empty($design['predesign_settings'])){
					$settings = unserialize($design['predesign_settings']);

					$image = htmlBase::newElement('img')
					->addClass('predesign')
					->attr('predesign_id', $design['predesign_id'])
					->attr('src', itw_app_link('appExt=productDesigner&width=80&height=80&predesign_id=' . $design['predesign_id'], 'predesign_thumb', 'process'));

					if (isset($settings['text'])){
						foreach($settings['text'] as $tInfo){
							if (array_key_exists('textVariable', $tInfo)){
								switch ($tInfo['textVariable']){
									case 'PLAYER_NUMBER':
										$image->addClass('hasNumber');
										break;
									case 'PLAYER_NAME':
										$image->addClass('hasName');
										break;
									case 'SCHOOL_YEAR':
										$image->addClass('hasYear');
										break;
									case 'ACTIVITY_NAME':
										$image->addClass('hasActivity');
										break;
								}
							}
						}
					}
				
					$costStr = '';
					if ($design['predesign_cost'] > 0){
						$costStr = '<div class="smallText">+' . $currencies->format($design['predesign_cost']) . '</div>';
					}

					if ($i%2){
						$row1[] = array('css' => array('width' => '80px', 'height' => '80px'), 'align' => 'center', 'text' => $image->draw() . $costStr . '<input type="radio" name="predesign_' . $_GET['location'] . '" value="' . $design['predesign_id'] . '" style="display:none;" />');
					}else{
						$row2[] = array('css' => array('width' => '80px', 'height' => '80px'), 'align' => 'center', 'text' => $image->draw() . $costStr . '<input type="radio" name="predesign_' . $_GET['location'] . '" value="' . $design['predesign_id'] . '" style="display:none;" />');
					}
					$i++;
				}
			}
		}
		$listingTable->addBodyRow(array(
			'columns' => $row1
		));
		$listingTable->addBodyRow(array(
			'columns' => $row2
		));
	}
	
	EventManager::attachActionResponse($listingTable->draw(), 'html');
?>