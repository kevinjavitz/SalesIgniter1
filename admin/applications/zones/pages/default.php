<?php
	$Qzones = Doctrine_Query::create()
	->from('GoogleZones')
	->orderBy('google_zones_name asc');
	
	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qzones);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_GOOGLE_ZONE_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));
	
	$zones = &$tableGrid->getResults();
	if ($zones){
		foreach($zones as $zone){
			$zoneId = $zone['google_zones_id'];
		
			if ((!isset($_GET['zID']) || (isset($_GET['zID']) && ($_GET['zID'] == $zoneId))) && !isset($cInfo)){
				$cInfo = new objectInfo($zone);
			}
		
			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'zID')) . 'zID=' . $zoneId));

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'zID')) . 'zID=' . $zoneId);
			if (isset($cInfo) && $zoneId == $cInfo->google_zones_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link(tep_get_all_get_params(array('action', 'zID')) . 'action=edit&zID=' . $zoneId);
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}
		
			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $zone['google_zones_name']),
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
				$infoBox->setHeader('<b>' . $cInfo->google_zones_name . '</b>');
				
				$deleteButton = htmlBase::newElement('button')->setType('submit')->usePreset('delete')->setHref(itw_app_link(tep_get_all_get_params(array('action', 'zID')) . 'action=deleteConfirm&zID=' . $cInfo->google_zones_id));
				$editButton = htmlBase::newElement('button')->setType('submit')->usePreset('edit')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'zID')) . 'zID=' . $cInfo->google_zones_id, 'zones', 'new'));
				
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
  	echo htmlBase::newElement('button')->usePreset('new')->setText('New Google Zone')
  	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'zID')), null, 'new', 'SSL'))
  	->draw();
  ?></div>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>