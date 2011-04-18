<?php
	$Qmembership = Doctrine_Query::create()
	->from('Membership m')
	->leftJoin('m.MembershipPlanDescription md')
	->where('md.language_id = ?', Session::get('languages_id'))
	->orderBy('sort_order');
	
	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qmembership);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_MEMBERSHIP')),
			array('text' => 'Default Checked'),
			array('text' => 'Sort Order')/*,
			array('text' => 'info')*/
		)
	));
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('New')->addClass('newButton'),
		htmlBase::newElement('button')->setText('Edit')->addClass('editButton')->disable(),
		htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable()
	));

   	$Result = &$tableGrid->getResults();
	if ($Result){
		foreach($Result as $mInfo){
			$planId = $mInfo['plan_id'];
			$planName = $mInfo['MembershipPlanDescription'][Session::get('languages_id')]['name'];
			$sortOrder = $mInfo['sort_order'];
			
			//$arrowIcon = htmlBase::newElement('icon')->setType('info');

			$statusIcon = htmlBase::newElement('icon');
			if ($mInfo['default_plan'] == '1') {
				$statusIcon->setType('circleCheck');
			} else {
				$statusIcon->setType('circleClose')->setTooltip('Click to make default')
				->setHref(itw_app_link('action=setDefault&pID=' . $planId));
			}

			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-plan_id' => $planId
				),
				'columns' => array(
					array('text' => $planName),
					array('text' => $statusIcon->draw(), 'align' => 'center'),
					array('text' => $sortOrder, 'align' => 'center')/*,
					array('text' => $arrowIcon->draw(), 'align' => 'right')*/
				)
			));
		}
	}
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_PACKAGES');
?></div>
<br />
<div class="pageHeading" style="color:red;font-size:.8em;"><?php
	echo sysLanguage::get('TEXT_INFO_EDIT_DELETE');
?></div>
<br />
<div class="gridContainer">
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
		</div>
	</div>
</div>