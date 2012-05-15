<?php
	$QFees = Doctrine_Query::create()
	->from('PayPerRentalExtraFees');
	
	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($QFees);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_TIME_FEES_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));
	
	$timefees = &$tableGrid->getResults();
	if ($timefees){
		foreach($timefees as $tfInfo){
			$timefeesId = $tfInfo['timefees_id'];
		
			if ((!isset($_GET['tfID']) || (isset($_GET['tfID']) && ($_GET['tfID'] == $timefeesId))) && !isset($tfObject)){
				$tfObject = new objectInfo($tfInfo);
			}
		
			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'tfID')) . 'tfID=' . $timefeesId));

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'tfID')) . 'tfID=' . $timefeesId);
			if (isset($tfObject) && $timefeesId == $tfObject->timefees_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link(tep_get_all_get_params(array('action', 'tfID')) . 'action=edit&tfID=' . $timefeesId);
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}
		
			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $tfInfo['timefees_name']),
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
								'name'      => 'edit_timefees'
							)
			);

		 	 if (isset($_GET['tfID'])) {
		            $timefees = Doctrine_Core::getTable('PayperRentalExtraFees')->find($_GET['tfID']);
				    $tfName = $timefees->timefees_name;
				    $tfDesc = $timefees->timefees_description;
				    $tfFee = $timefees->timefees_fee;
				    $tfMandatory = ($timefees->timefees_mandatory == 0)?false:true;
				    $tfHours = $timefees->timefees_hours;
				    $infoBox->setHeader('<b>Edit Time Fee</b>');
			 }else{
			  	    $tfName = '';
				    $tfDesc = '';
				    $tfFee = '0';
				    $tfMandatory = false;
				    $tfHours = '0';
				    $infoBox->setHeader('<b>New Time Fee</b>');
			 }

			 $htmlTimeFeeName = htmlBase::newElement('input')
			 ->setLabel(sysLanguage::get('TEXT_TIMEFEES'))
			 ->setLabelPosition('before')
			 ->setName('timefees_name')
			 ->setValue($tfName);

			 $htmlTimeFeeDescription = htmlBase::newElement('textarea')
				 //->setLabel(sysLanguage::get('TEXT_TIMEFEES_DESCRIPTION'))
				 //->setLabelPosition('before')
			 ->addClass('makeFCK')
			 ->setName('timefees_description')
			 ->html($tfDesc);



			 $htmlTimeFeeFee = htmlBase::newElement('input')
			 ->setLabel(sysLanguage::get('TEXT_TIMEFEES_FEE'))
			 ->setLabelPosition('before')
			 ->setName('timefees_fee')
			 ->setValue($tfFee);

			 $htmlTimeFeeHours = htmlBase::newElement('input')
			 ->setLabel(sysLanguage::get('TEXT_TIMEFEES_HOURS'))
			 ->setLabelPosition('before')
			 ->setName('timefees_hours')
			 ->setValue($tfHours);

			 $htmlMandatory = htmlBase::newElement('checkbox')
				 ->setName('timefees_mandatory')
				 ->setLabel(sysLanguage::get('TEXT_TIMEFEES_MANDATORY'))
				 ->setLabelPosition('before')
				 ->setChecked($tfMandatory);


 			 $saveButton = htmlBase::newElement('button')
			 ->setType('submit')
			 ->usePreset('save');
			 $cancelButton = htmlBase::newElement('button')
			 ->usePreset('cancel')
			 ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));

			  $timefeesTabsObj = htmlBase::newElement('tabs')
				 ->setId('timefeesTabs');

			 $inputTable = htmlBase::newElement('table')
				 ->setCellPadding(2)
				 ->setCellSpacing(0);

			 $inputTable->addBodyRow(array(
					 'columns' => array(
						 array('colspan' => 2,'text' => $htmlTimeFeeName->draw())
					 )
				 ));
			 $inputTable->addBodyRow(array(
					 'columns' => array(
						 array('colspan' => 2,'text' => $htmlTimeFeeDescription->draw())
					 )
				 ));
			 $inputTable->addBodyRow(array(
					 'columns' => array(
						 array('colspan' => 2,'text' => $htmlTimeFeeFee->draw())
					 )
				 ));

			 $inputTable->addBodyRow(array(
					 'columns' => array(
						 array('colspan' => 2,'text' => $htmlTimeFeeHours->draw())
					 )
				 ));
			 $inputTable->addBodyRow(array(
					 'columns' => array(
						 array('colspan' => 2,'text' => $htmlMandatory->draw())
					 )
				 ));

			 $timefeesTabsObj->addTabHeader('timefeesTab1', array('text' => 'Fees'))
				 ->addTabPage('timefeesTab1', array('text' => $inputTable));

			 $multiStore = $appExtension->getExtension('multiStore');
			 if ($multiStore !== false && $multiStore->isEnabled() === true){
				 if (isset($multiStore->pagePlugin)){
					 $multiStore->pagePlugin->loadTabs($timefeesTabsObj);
				 }
			 }

			 $infoBox->addContentRow($timefeesTabsObj->draw());
			 $infoBox->addButton($saveButton)->addButton($cancelButton);

			 break;
		default:
			if (isset($tfObject) && is_object($tfObject)) {
				$infoBox->setHeader('<b>' . $tfObject->timefees_name . '</b>');
				
				$deleteButton = htmlBase::newElement('button')
				->usePreset('delete')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'tfID')) . 'action=deleteConfirm&tfID=' . $tfObject->timefees_id));
				$editButton = htmlBase::newElement('button')
				->usePreset('edit')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'tfID')) . 'action=edit' . '&tfID=' . $tfObject->timefees_id, 'extrafees', 'default'));
				
				$infoBox->addButton($editButton)->addButton($deleteButton);

			}
			break;
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div style="width:50%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
  </div>
  <div style="text-align:right;"><?php
  	echo htmlBase::newElement('button')
		    ->usePreset('new')
		    ->setText('New Extra Fee')
  	        ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'tfID')) . 'action=edit', null, 'default', 'SSL'))
  	        ->draw();
  ?></div>
 </div>
 <div style="width:50%;float:right;"><?php echo $infoBox->draw();?></div>