<?php
	$Qpredesigns = Doctrine_Query::create()
	->from('ProductDesignerPredesigns')
	->orderBy('predesign_name');

	EventManager::notify('PredesignListingQueryBeforeExecute', &$Qpredesigns);

	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 0))
	->setQuery($Qpredesigns);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_PREDESIGN_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$allGetParams = tep_get_all_get_params(array('action', 'dID'));
	$predesigns = &$tableGrid->getResults();
	if ($predesigns){
		$infoBoxes = array();
		$editButton = htmlBase::newElement('button')->usePreset('edit');
		$deleteButton = htmlBase::newElement('button')->usePreset('delete');
		foreach($predesigns as $predesign){
			$predesignId = $predesign['predesign_id'];

			if ((!isset($_GET['dID']) || $_GET['dID'] == $predesignId) && !isset($dInfo)){
				$dInfo = new objectInfo($predesign);
			}

			$arrowIcon = htmlBase::newElement('icon')->setType('info')
			->setHref(itw_app_link($allGetParams . 'dID=' . $predesignId, null, null, 'SSL'));

			$tableGrid->addBodyRow(array(
				'rowAttr'  => array('infobox_id' => $predesignId),
				'columns' => array(
					array('text' => $predesign['predesign_name']),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));
			
			$infoBox = htmlBase::newElement('infobox');
			$infoBox->setButtonBarLocation('top');
			$infoBox->setHeader('<b>' . $predesign['predesign_name'] . '</b>');
			
			$editButton->setHref(itw_app_link($allGetParams . 'dID=' . $predesignId, null, 'new', 'SSL'));
			$deleteButton->setHref(itw_app_link($allGetParams . 'dID=' . $predesignId . '&action=delete', null, null, 'SSL'));

			$infoBox->addButton($editButton)->addButton($deleteButton);
			
			$infoBoxes[$predesignId] = $infoBox->draw();
			unset($infoBox);
		}
	}

	switch ($action) {
		case 'delete':
			$infoBox = htmlBase::newElement('infobox');
			$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_DELETE_PREDESIGN') . '</b>');
			$infoBox->setForm(array(
				'name' => 'delete_predesign',
				'action' => itw_app_link(tep_get_all_get_params(array('action')) . 'action=deleteConfirm', null, null, 'SSL')
			));

			$confirmButton = htmlBase::newElement('button')->usePreset('delete')->setType('submit');
			$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
			->setHref(itw_app_link(tep_get_all_get_params(array('action')), null, null, 'SSL'));

			$infoBox->addButton($confirmButton)->addButton($cancelButton);

			$infoBox->addContentRow(sysLanguage::get('TEXT_DELETE_PREDESIGN_INTRO') . tep_draw_hidden_field('predesign_id', $dInfo->predesign_id));
			$infoBox->addContentRow('<b>' . $dInfo->predesign_name . '</b>');
			$infoBoxes[$dInfo->predesign_id] = $infoBox->draw();
			break;
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
    $newPredesignButton = htmlBase::newElement('button')->usePreset('install')->setText(sysLanguage::get('TEXT_BUTTON_NEW_PREDESIGN'))
    ->setHref(itw_app_link($allGetParams, null, 'new', 'SSL'));

    echo $newPredesignButton->draw();
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;" id="infobox"><?php
 	if (isset($infoBoxes) && sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $predesignId => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $predesignId . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>