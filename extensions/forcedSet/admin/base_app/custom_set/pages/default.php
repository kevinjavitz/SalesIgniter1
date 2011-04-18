<?php
	$QforcedRelation = Doctrine_Query::create()
	->from('ForcedSetRelations');
	
	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($QforcedRelation);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_FORCED_RELATION_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));
	
	$forcedRelation = &$tableGrid->getResults();
	if ($forcedRelation){
		foreach($forcedRelation as $fInfo){
			$forcedRelationId = $fInfo['forced_set_id'];
		
			if ((!isset($_GET['fID']) || (isset($_GET['fID']) && ($_GET['fID'] == $forcedRelationId))) && !isset($fObject)){
				$fObject = new objectInfo($fInfo);
			}
		
			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'fID=' . $forcedRelationId));

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'fID=' . $forcedRelationId);
			if (isset($fObject) && $forcedRelationId == $fObject->forced_set_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'action=edit&fID=' . $forcedRelationId);
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}
		
			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $fInfo['forced_set_custom_field_one'] . ' - ' . $fInfo['forced_set_custom_field_two']),
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
								'name'      => 'edit_forced_set'
							)
			);

		 	 if (isset($_GET['fID'])) {
		            $forcedRelation = Doctrine_Core::getTable('ForcedSetRelations')->find($_GET['fID']);
				    $customField1 = $forcedRelation->forced_set_custom_field_one;
					$customField2 = $forcedRelation->forced_set_custom_field_two;
				    $infoBox->setHeader('<b>Edit Relation</b>');
			 }else{
			  	    $text = "";
				    $color = "";
				    $infoBox->setHeader('<b>New Relation</b>');
			 }

			 $htmlCustomField1 = htmlBase::newElement('input')
			            ->setLabel(sysLanguage::get('TEXT_CUSTOM_FIELD1'))
						->setLabelPosition('before')
					    ->setName('forced_set_custom_field_one')
					    ->setValue($customField1);

  			 $htmlCustomField2 = htmlBase::newElement('input')
			            ->setLabel(sysLanguage::get('TEXT_CUSTOM_FIELD2'))
						->setLabelPosition('before')
			            ->setLabelSeparator('<br />')
					    ->setName('forced_set_custom_field_two')
					    ->setValue($customField2);

 			 $saveButton = htmlBase::newElement('button')
					        ->setType('submit')
					        ->usePreset('save');
			 $cancelButton = htmlBase::newElement('button')
					        ->usePreset('cancel')
			                ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));



			 $infoBox->addContentRow($htmlCustomField1->draw());
			 $infoBox->addContentRow($htmlCustomField2->draw());
			 $infoBox->addButton($saveButton)->addButton($cancelButton);

			 break;
		default:
			if (isset($fObject) && is_object($fObject)) {
				$infoBox->setHeader('<b>' . $fObject->forced_set_custom_field_one. ' - '. $fObject->forced_set_custom_field_two . '</b>');
				
				$deleteButton = htmlBase::newElement('button')
								->setType('submit')
								->usePreset('delete')
								->setHref(itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'action=deleteConfirm&fID=' . $fObject->forced_set_id));
				$editButton = htmlBase::newElement('button')
								->setType('submit')
								->usePreset('edit')
								->setHref(itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'action=edit' . '&fID=' . $fObject->forced_set_id, 'custom_set', 'default'));
				
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
		    ->setText('New Relation')
  	        ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'action=edit', null, 'default', 'SSL'))
  	        ->draw();
  ?></div>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>