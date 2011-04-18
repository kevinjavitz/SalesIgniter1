<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Qactivities = Doctrine_Query::create()
	->from('ProductDesignerPredesignActivities')
	//->where('cd.language_id = ?', $lID)
	->orderBy('activity_name');

	EventManager::notify('ProductDesignerPredesignActivitiesListingQueryBeforeExecute', &$Qactivities);

	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 0))
	->setQuery($Qactivities);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_ACTIVITIES')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$activities = &$tableGrid->getResults();
	$infoBoxes = array();
	if (!empty($action)){
		$saveButton = htmlBase::newElement('button')->usePreset('save')->setType('submit');
		$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
		->setHref(itw_app_link(tep_get_all_get_params(array('action')), null, null, 'SSL'));
	}
	
	if ($activities){
		$allGetParams = tep_get_all_get_params(array('action', 'aID'));
		$deleteButton = htmlBase::newElement('button')->usePreset('delete');
		$editButton = htmlBase::newElement('button')->usePreset('edit');
		foreach($activities as $activity){
			$activityId = $activity['activity_id'];
			if ((!isset($_GET['aID']) || $_GET['aID'] == $activityId) && !isset($aInfo) && (substr($action, 0, 3) != 'new')){
				$aInfo = new objectInfo($activity);
			}

			$arrowIcon = htmlBase::newElement('icon')->setType('info')
			->setHref(itw_app_link($allGetParams . 'aID=' . $activityId, null, null, 'SSL'));

			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'infobox_id' => $activityId
				),
				'columns' => array(
					array('text' => $activity['activity_name']),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));
			
			$editButton->setHref(itw_app_link($allGetParams . 'action=edit&aID=' . $activityId, null, null, 'SSL'));
			$deleteButton->setHref(itw_app_link($allGetParams . 'action=delete_activity&aID=' . $activityId, null, null, 'SSL'));
			$infoBox = htmlBase::newElement('infobox');
			$infoBox->setButtonBarLocation('top');
			$infoBox->setHeader('<b>' . $activity['activity_name'] . '</b>');
			$infoBox->addButton($editButton)->addButton($deleteButton);
			
			$infoBoxes[$activityId] = $infoBox->draw();
			unset($infoBox);
		}
	}

	if (!empty($action)){
		$infoBox = htmlBase::newElement('infobox');
		switch ($action) {
			case 'delete_activity':
				$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_DELETE_ACTIVITY') . '</b>');
				$infoBox->setForm(array(
					'name' => 'activities',
					'action' => itw_app_link(tep_get_all_get_params(array('action')) . 'action=deleteActivityConfirm', null, null, 'SSL')
				));

				$deleteSubmitButton = htmlBase::newElement('button')->setType('submit')->usePreset('delete');

				$infoBox->addButton($deleteSubmitButton)->addButton($cancelButton);

				$infoBox->addContentRow(sysLanguage::get('TEXT_DELETE_ACTIVITY_INTRO') . tep_draw_hidden_field('activity_id', $aInfo->activity_id));
				$infoBox->addContentRow('<b>' . $aInfo->activity_name . '</b>');
				
				$infoBoxes[$aInfo->activity_id] = $infoBox->draw();
				break;
			case 'new':
			case 'edit':
				if ($action == 'edit'){
					$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_EDIT_ACTIVITY') . '</b>');
				}else{
					$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_NEW_ACTIVITY') . '</b>');
				}
				$infoBox->setForm(array(
					'name' => 'activity',
					'action' => itw_app_link(tep_get_all_get_params(array('action')) . 'action=saveActivity', null, null, 'SSL')
				));

				$infoBox->addButton($saveButton)->addButton($cancelButton);
			
				$activityName = htmlBase::newElement('input')->setName('activity_name');
				if (isset($aInfo)){
					$activityName->val($aInfo->activity_name);
				}
			
				$infoBox->addContentRow(sysLanguage::get('TEXT_EDIT_ACTIVITY_NAME') . $activityName->draw());

				$MultiStore = $appExtension->getExtension('multiStore');
				if ($MultiStore !== false){
					$stores = $MultiStore->getStoresArray();
					$inputs = array();
					$checked = array();
					foreach($stores as $sInfo){
						$inputs[] = array(
							'value' => $sInfo['stores_id'],
							'label' => $sInfo['stores_name'],
							'labelPosition' => 'after'
						);
						
						if ($action == 'new'){
							$checked[] = $sInfo['stores_id'];
						}
					}
					
					if (isset($aInfo)){
						$QtoStores = Doctrine_Query::create()
						->select('stores_id')
						->from('ProductDesignerPredesignActivitiesToStores')
						->where('activity_id = ?', $aInfo->activity_id)
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						foreach($QtoStores as $store){
							$checked[] = $store['stores_id'];
						}
					}
					$checkboxes = htmlBase::newElement('checkbox')
					->addGroup(array(
						'separator' => '<br />',
						'checked' => $checked,
						'name' => 'stores_id[]',
						'data' => $inputs
					));
					
					$infoBox->addContentRow('Stores: <br />' . $checkboxes->draw());
				}

				if (isset($aInfo)){
					$infoBoxId = $aInfo->activity_id;
				}else{
					$infoBoxId = 'new';
				}
				$infoBoxes[$infoBoxId] = $infoBox->draw();
				break;
		}
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />

 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   </div>
  </div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td align="right" class="smallText"><?php
   	$newActivityButton = htmlBase::newElement('button')->usePreset('install')->setText(sysLanguage::get('TEXT_BUTTON_NEW_ACTIVITY'))
   	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'aID')) . 'action=new', null, null, 'SSL'));

   	echo $newActivityButton->draw();
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;"><?php
 	if (sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $aID => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $aID . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>
