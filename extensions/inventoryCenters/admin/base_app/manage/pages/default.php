<?php
	$Qcenters = Doctrine_Query::create()
	->from('ProductsInventoryCenters')
	->orderBy('inventory_center_name asc');
	EventManager::notify('AdminInventoryCentersListingQueryBeforeExecute', &$Qcenters);

	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qcenters);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_CENTER_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));
	
	$centers = &$tableGrid->getResults();
	if ($centers){
		foreach($centers as $center){
			$centerId = $center['inventory_center_id'];
		
			if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $centerId))) && !isset($cInfo)){
				$cInfo = new objectInfo($center);
			}
		
			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $centerId));

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $centerId);
			if (isset($cInfo) && $centerId == $cInfo->inventory_center_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'action=edit&cID=' . $centerId);
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}
		
			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $center['inventory_center_name']),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));
		}
	}
	
	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setButtonBarLocation('top');

	switch ($action){
		default:
			if (isset($cInfo) && is_object($cInfo)) {
				$infoBox->setHeader('<b>' . $cInfo->inventory_center_name . '</b>');
				
				$deleteButton = htmlBase::newElement('button')->usePreset('delete')->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'action=deleteConfirm&cID=' . $cInfo->inventory_center_id));
				$editButton = htmlBase::newElement('button')->usePreset('edit')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $cInfo->inventory_center_id, 'manage', 'new'));
				
				$infoBox->addButton($editButton)->addButton($deleteButton);
				
				$infoBox->addContentRow('<br>' . nl2br($cInfo->inventory_center_address));
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
  	echo htmlBase::newElement('button')->usePreset('new')->setText('New Center')
  	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')), null, 'new', 'SSL'))
  	->draw();
  ?></div>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>