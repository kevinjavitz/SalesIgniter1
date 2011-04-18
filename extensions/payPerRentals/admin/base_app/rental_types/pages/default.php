<?php
	$QPayPerRentalTypes = Doctrine_Query::create()
	->from('PayPerRentalTypes');
	
	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($QPayPerRentalTypes);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_PPR_TYPE_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));
	
	$rentalType = &$tableGrid->getResults();
	if ($rentalType){
		foreach($rentalType as $rInfo){
			$rentalTypeId = $rInfo['pay_per_rental_types_id'];
		
			if ((!isset($_GET['rID']) || (isset($_GET['rID']) && ($_GET['rID'] == $rentalTypeId))) && !isset($rObject)){
				$rObject = new objectInfo($rInfo);
			}
		
			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'rID=' . $rentalTypeId));

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'rID=' . $rentalTypeId);
			if (isset($rObject) && $rentalTypeId == $rObject->pay_per_rental_types_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'action=edit&rID=' . $rentalTypeId);
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}
		
			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $rInfo['pay_per_rental_types_name']),
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
								'name'      => 'edit_rental_type'
							)
			);

			/*$available = htmlBase::newElement('selectbox')
						->setLabel('Rental Status Available: ')
						->setLabelPosition('before')
						->setName('rental_status_available');

			$available->addOption('0', 'Not Available');
			$available->addOption('1', 'Available');*/

		 	 if (isset($_GET['rID'])) {
		            $rentalType = Doctrine_Core::getTable('PayPerRentalTypes')->findOneByPayPerRentalTypesId($_GET['rID']);
				    $typename = $rentalType->pay_per_rental_types_name;
					$minutes = $rentalType->minutes;
					//$available->selectOptionByValue($rentalStatus->rental_status_available);
				    $infoBox->setHeader('<b>Edit Rental Type</b>');
			 }else{
			  	    $typename = "";
				    $minutes = "";
				    //$available->selectOptionByValue('0');
				    $infoBox->setHeader('<b>New Rental type</b>');
			 }

			 $htmlTypeName = htmlBase::newElement('input')
			            ->setLabel(sysLanguage::get('TEXT_RENTAL_TYPE_NAME'))
						->setLabelPosition('before')
					    ->setName('pay_per_rental_types_name')
					    ->setValue($typename);

  			 $htmlMinutes = htmlBase::newElement('input')
						->setType('text')
			            ->setLabel(sysLanguage::get('TEXT_RENTAL_TYPE_MINUTES'))
						->setLabelPosition('before')
			            ->setLabelSeparator('<br />')
					    ->setName('minutes')
					    ->setValue($minutes);

 			 $saveButton = htmlBase::newElement('button')
					        ->setType('submit')
					        ->usePreset('save');
			 $cancelButton = htmlBase::newElement('button')
					        ->usePreset('cancel')
			                ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));



			 $infoBox->addContentRow($htmlTypeName->draw());
			 $infoBox->addContentRow($htmlMinutes->draw());
			 //$infoBox->addContentRow($available->draw());
			 $infoBox->addButton($saveButton)->addButton($cancelButton);

			 break;
		default:
			if (isset($rObject) && is_object($rObject)) {
				$infoBox->setHeader('<b>' . $rObject->pay_per_rental_types_name . '</b>');
				
				$deleteButton = htmlBase::newElement('button')
								->setType('submit')
								->usePreset('delete')
								->setHref(itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'action=deleteConfirm&rID=' . $rObject->pay_per_rental_types_id));
				$editButton = htmlBase::newElement('button')
								->setType('submit')
								->usePreset('edit')
								->setHref(itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'action=edit' . '&rID=' . $rObject->pay_per_rental_types_id, 'rental_types', 'default'));
				
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
		    ->setText('New Rental Type')
  	        ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'action=edit', null, 'default', 'SSL'))
  	        ->draw();
  ?></div>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>