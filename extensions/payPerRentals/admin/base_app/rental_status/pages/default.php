<?php
	$QrentalStatus = Doctrine_Query::create()
	->from('RentalStatus');	
	
	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($QrentalStatus);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_RENTAL_STATUS_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));
	
	$rentalStatus = &$tableGrid->getResults();
	if ($rentalStatus){
		foreach($rentalStatus as $rInfo){
			$rentalStatusId = $rInfo['rental_status_id'];
		
			if ((!isset($_GET['rID']) || (isset($_GET['rID']) && ($_GET['rID'] == $rentalStatusId))) && !isset($rObject)){
				$rObject = new objectInfo($rInfo);
			}
		
			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'rID=' . $rentalStatusId));

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'rID=' . $rentalStatusId);
			if (isset($rObject) && $rentalStatusId == $rObject->rental_status_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'action=edit&rID=' . $rentalStatusId);
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}
		
			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $rInfo['rental_status_text']),
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
								'name'      => 'edit_rental_status'
							)
			);

			/*$available = htmlBase::newElement('selectbox')
						->setLabel('Rental Status Available: ')
						->setLabelPosition('before')
						->setName('rental_status_available');

			$available->addOption('0', 'Not Available');
			$available->addOption('1', 'Available');*/

		 	 if (isset($_GET['rID'])) {
		            $rentalStatus = Doctrine_Core::getTable('RentalStatus')->findOneByRentalStatusId($_GET['rID']);
				    $text = $rentalStatus->rental_status_text;
					$color = $rentalStatus->rental_status_color;
					//$available->selectOptionByValue($rentalStatus->rental_status_available);
				    $infoBox->setHeader('<b>Edit Rental Status</b>');
			 }else{
			  	    $text = "";
				    $color = "";
				    //$available->selectOptionByValue('0');
				    $infoBox->setHeader('<b>New Rental Status</b>');
			 }

			 $htmlText = htmlBase::newElement('input')
			            ->setLabel('Rental Status Text: ')
						->setLabelPosition('before')
					    ->setName('rental_status_text')
					    ->setValue($text);

  			 $htmlColor = htmlBase::newElement('input')
						->setType('text')
			            ->setLabel('Rental Status Color: ')
						->setLabelPosition('before')
			            ->setLabelSeparator('<br />')
						->addClass('iColorPicker')
					    ->setName('rental_status_color')
			            ->setId('color_picker_status')
					    ->setValue($color);

 			 $saveButton = htmlBase::newElement('button')
					        ->setType('submit')
					        ->usePreset('save');
			 $cancelButton = htmlBase::newElement('button')
					        ->usePreset('cancel')
			                ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));



			 $infoBox->addContentRow($htmlText->draw());
			 $infoBox->addContentRow($htmlColor->draw());
			 //$infoBox->addContentRow($available->draw());
			 $infoBox->addButton($saveButton)->addButton($cancelButton);

			 break;
		default:
			if (isset($rObject) && is_object($rObject)) {
				$infoBox->setHeader('<b>' . $rObject->rental_status_text . '</b>');
				
				$deleteButton = htmlBase::newElement('button')
								->setType('submit')
								->usePreset('delete')
								->setHref(itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'action=deleteConfirm&rID=' . $rObject->rental_status_id));
				$editButton = htmlBase::newElement('button')
								->setType('submit')
								->usePreset('edit')
								->setHref(itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'action=edit' . '&rID=' . $rObject->rental_status_id, 'rental_status', 'default'));
				
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
		    ->setText('New Rental Status')
  	        ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'action=edit', null, 'default', 'SSL'))
  	        ->draw();
  ?></div>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>