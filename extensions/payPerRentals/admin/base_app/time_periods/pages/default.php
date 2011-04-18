<?php
	$Qperiods = Doctrine_Query::create()
	->from('PayPerRentalPeriods');

	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qperiods);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_PERIODS')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$periods = &$tableGrid->getResults();
	if ($periods){
		foreach($periods as $period){
			$periodId = $period['period_id'];

			if ((!isset($_GET['pID']) || (isset($_GET['pID']) && ($_GET['pID'] == $periodId))) && !isset($cInfo)){
				$cInfo = new objectInfo($period);
			}

			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'pID')) . 'pID=' . $periodId));

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'epID')) . 'pID=' . $periodId);
			if (isset($cInfo) && $periodId == $cInfo->period_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link('pID=' . $periodId, null, 'new');
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}

			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $period['period_name']),
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
				$infoBox->setHeader('<b>' . $cInfo->period_name . '</b>');

				$deleteButton = htmlBase::newElement('button')->setType('submit')->usePreset('delete')->setHref(itw_app_link(tep_get_all_get_params(array('action', 'pID')) . 'action=deleteConfirm&pID=' . $cInfo->period_id));
				$editButton = htmlBase::newElement('button')->setType('submit')->usePreset('edit')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'pID')) . 'pID=' . $cInfo->period_id,null,'new'));

				$infoBox->addButton($editButton)->addButton($deleteButton);

				$infoBox->addContentRow('<br>' . date(sysLanguage::getDateFormat(),strtotime($cInfo->period_start_date)));
				$infoBox->addContentRow('<br>' . date(sysLanguage::getDateFormat(),strtotime($cInfo->period_end_date)));
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
  	echo htmlBase::newElement('button')->usePreset('new')->setText('New Period')
  	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'pID')), null, 'new', 'SSL'))
  	->draw();
  ?></div>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>