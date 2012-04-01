<?php
	$QGate = Doctrine_Query::create()
	->from('PayPerRentalGates');
	
	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)

	->setQuery($QGate);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_GATE_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));
	
	$gates = &$tableGrid->getResults();
	if ($gates){
		foreach($gates as $gInfo){
			$gatesId = $gInfo['gates_id'];
		
			if ((!isset($_GET['gID']) || (isset($_GET['gID']) && ($_GET['gID'] == $gatesId))) && !isset($gObject)){
				$gObject = new objectInfo($gInfo);
			}
		
			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID=' . $gatesId));

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID=' . $gatesId);
			if (isset($gObject) && $gatesId == $gObject->gates_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'action=edit&gID=' . $gatesId);
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}
		
			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $gInfo['gate_name']),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));
		}
	}
	
	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setButtonBarLocation('top');

	switch ($action){
		case 'edit':
			$infoBox->setForm(array(
								'action'    => itw_app_link(tep_get_all_get_params(array('action')) . 'action=save'),
								'method'    =>  'post',
								'name'      => 'edit_gates'
							)
			);

		 	 if (isset($_GET['gID'])) {
		            $gates = Doctrine_Core::getTable('PayperRentalGates')->find($_GET['gID']);
				    $gName = $gates->gate_name;
				    $infoBox->setHeader('<b>Edit Gate</b>');
			 }else{
			  	    $gName = "";
				    $infoBox->setHeader('<b>New Gate</b>');
			 }

			 $htmlGate = htmlBase::newElement('input')
			 ->setLabel(sysLanguage::get('TEXT_GATE'))
			 ->setLabelPosition('before')
			 ->setName('gate_name')
			 ->setValue($gName);

 			 $saveButton = htmlBase::newElement('button')
			 ->setType('submit')
			 ->usePreset('save');
			 $cancelButton = htmlBase::newElement('button')
			 ->usePreset('cancel')
			 ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));


			 $infoBox->addContentRow($htmlGate->draw());
			 $infoBox->addButton($saveButton)->addButton($cancelButton);

			 break;
		default:
			if (isset($gObject) && is_object($gObject)) {
				$infoBox->setHeader('<b>' . $gObject->gate_name . '</b>');
				
				$deleteButton = htmlBase::newElement('button')
				->setType('submit')
				->usePreset('delete')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'action=deleteConfirm&gID=' . $gObject->gates_id));
				$editButton = htmlBase::newElement('button')
				->setType('submit')
				->usePreset('edit')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'action=edit' . '&gID=' . $gObject->gates_id, 'gates', 'default'));
				
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
  	echo htmlBase::newElement('button')
		    ->usePreset('new')
		    ->setText('New Gate')
  	        ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'action=edit', null, 'default', 'SSL'))
  	        ->draw();
  ?></div>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>