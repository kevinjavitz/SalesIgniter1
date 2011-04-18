<?php
	$Qavail = Doctrine_Query::create()
	->from('RentalAvailability ra')
	->leftJoin('ra.RentalAvailabilityDescription rad')
	->where('rad.language_id=?', Session::get('languages_id'));


	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qavail);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_AVAIL_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$Result = &$tableGrid->getResults();
	if ($Result){
		foreach($Result as $aInfo){
			$aInfoId = $aInfo['rental_availability_id'];

			if ((!isset($_GET['arID']) || (isset($_GET['arID']) && ($_GET['arID'] == $aInfoId))) && !isset($cInfo)){
				$cInfo = new objectInfo($aInfo);
				$cInfo->name =  $aInfo['RentalAvailabilityDescription'][Session::get('languages_id')]['name']; 
			}

			$arrowIcon = htmlBase::newElement('icon')
						->setHref(itw_app_link(tep_get_all_get_params(array('action', 'arID')) . 'arID=' . $aInfoId));

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'arID')) . 'arID=' . $aInfoId);
			if (isset($cInfo) && $aInfoId == $cInfo->rental_availability_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link(tep_get_all_get_params(array('action', 'arID')) . 'action=edit&arID=' . $aInfoId);
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}

			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $aInfo['RentalAvailabilityDescription'][Session::get('languages_id')]['name']),
					array('text' => $arrowIcon->draw(), 
					      'align' => 'right'
					)
				)
			));
		}
	}

	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setButtonBarLocation('top');

	$RentalAvailability = Doctrine_Core::getTable('RentalAvailability');

	if (isset($_GET['arID'])){
		$RentalAvailability = Doctrine_Query::create()
							->from('RentalAvailability r')
							->leftJoin('r.RentalAvailabilityDescription rad')
							->where('r.rental_availability_id = ?',(int)$_GET['arID'])
							->execute()
							->toArray(true);
	}else{
		$RentalAvailability = new RentalAvailability();
	}	
    //$Description = $RentalAvailability->RentalAvailabilityDescription;
	switch ($action){
		case 'new':
			$infoBox->setHeader('<b>New Rental Availability</b>');
			$infoBox->setForm(array(
				'name'   => 'avail',
				'action' => itw_app_link(tep_get_all_get_params(array('action', 'arID')) . 'action=saveAvail')
			));

			$saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
			$cancelButton = htmlBase::newElement('button')->setType('submit')->usePreset('cancel')
							->setHref(itw_app_link(tep_get_all_get_params(array('action', 'arID'))));

			$infoBox->addButton($saveButton)
					->addButton($cancelButton);
			$rental_availability_inputs_string = '';
			foreach(sysLanguage::getLanguages() as $lInfo){
				$rental_availability_inputs_string .= '<br>' . $lInfo['showName']() . '&nbsp;' . tep_draw_input_field('name[' . $lInfo['id'] . ']', '');
			}

			$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_AVAILABILITY_NAME') . $rental_availability_inputs_string .'<br><br><b>'.sysLanguage::get('TEXT_INFO_AVAILABILITY_RATIO').'</b><br>' . tep_draw_input_field('ratio') );
			break;
		case 'edit':
			$infoBox->setHeader('<b>' . $cInfo->name . '</b>');
			$infoBox->setForm(array(
				'name'   => 'avail',
				'action' => itw_app_link(tep_get_all_get_params(array('action', 'arID')) . 'action=saveAvail&arID=' . $cInfo->rental_availability_id)
			));

			$saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
			$cancelButton = htmlBase::newElement('button')->setType('submit')->usePreset('cancel')
							->setHref(itw_app_link(tep_get_all_get_params(array('action', 'arID')) . 'arID=' . $cInfo->rental_availability_id));

			$infoBox->addButton($saveButton)->addButton($cancelButton);
			
			$rental_availability_inputs_string = '';
			foreach(sysLanguage::getLanguages() as $lInfo){
				$rental_availability_inputs_string .= '<br>' . $lInfo['showName']() . '&nbsp;' . tep_draw_input_field('name[' . $lInfo['id'] . ']', (isset($RentalAvailability[0]['RentalAvailabilityDescription'][$lInfo['id']]) ? $RentalAvailability[0]['RentalAvailabilityDescription'][$lInfo['id']]['name'] : ''));
			}

			$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_AVAILABILITY_NAME') . $rental_availability_inputs_string. '<br><br><b>'.sysLanguage::get('TEXT_INFO_AVAILABILITY_RATIO').'</b><br>' . tep_draw_input_field('ratio', $cInfo->ratio) );
			break;
		default:
			if (isset($cInfo) && is_object($cInfo)) {
				$infoBox->setHeader('<b>' . $cInfo->name . '</b>');

				$deleteButton = htmlBase::newElement('button')->setType('submit')->usePreset('delete')->setHref(itw_app_link(tep_get_all_get_params(array('action', 'arID')) . 'action=deleteConfirmAvail&arID=' . $cInfo->rental_availability_id));
				$editButton = htmlBase::newElement('button')->setType('submit')->usePreset('edit')
							->setHref(itw_app_link(tep_get_all_get_params(array('action', 'arID')) . 'action=edit&arID=' . $cInfo->rental_availability_id));

				$infoBox->addButton($editButton)
						->addButton($deleteButton);

				$infoBox->addContentRow('<br>' . nl2br($cInfo->ratio));
			}
			break;
	}
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_AVAIL');?></div>
<br/>
<div style="width:75%;float:left;">
	<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
		<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
	</div>
	<div style="text-align:right;"><?php
	  echo htmlBase::newElement('button')
			->usePreset('new')
			->setText('New Rental Availability')
			->setHref(itw_app_link(tep_get_all_get_params(array('action',
		                                                        'arID'
															)
														) . 'action=new'))->draw();
	?></div>
</div>
<div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>