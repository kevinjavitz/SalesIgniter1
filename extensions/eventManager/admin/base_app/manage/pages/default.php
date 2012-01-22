<?php
	$Qevents = Doctrine_Query::create()
	->from('EventManagerEvents eve')
	->leftJoin('eve.EventManagerEventsDescription eved')
	->where('eved.language_id = ?', Session::get('languages_id'));

	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qevents);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_EVENTS')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$events = &$tableGrid->getResults();
	if ($events){
		foreach($events as $event){
			$eventsId = $event['events_id'];

			if ((!isset($_GET['eID']) || (isset($_GET['eID']) && ($_GET['eID'] == $eventsId))) && !isset($cInfo)){
				$cInfo = new objectInfo($event);
			}

			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'eID')) . 'eID=' . $eventsId));

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'eID')) . 'eID=' . $eventsId);
			if (isset($cInfo) && $eventsId == $cInfo->events_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link($allGetParams . 'eID=' . $eventsId, null, 'new');
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}

			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $event['EventManagerEventsDescription'][Session::get('languages_id')]['events_title']),
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
				$infoBox->setHeader('<b>' . $cInfo->EventManagerEventsDescription[Session::get('languages_id')]['events_title'] . '</b>');

				$deleteButton = htmlBase::newElement('button')->usePreset('delete')->setHref(itw_app_link(tep_get_all_get_params(array('action', 'eID')) . 'action=deleteConfirm&eID=' . $cInfo->events_id));
				$editButton = htmlBase::newElement('button')->usePreset('edit')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'eID')) . 'eID=' . $cInfo->events_id,null,'new'));

				$infoBox->addButton($editButton)->addButton($deleteButton);

				$infoBox->addContentRow('<br>' . strftime(sysLanguage::getDateFormat('long'),strtotime($cInfo->events_start_date)));
			}
			break;
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_EVENTS');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
  </div>
  <div style="text-align:right;"><?php
  	echo htmlBase::newElement('button')->usePreset('new')->setText('New Event')
  	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'eID')), null, 'new', 'SSL'))
  	->draw();
  ?></div>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>