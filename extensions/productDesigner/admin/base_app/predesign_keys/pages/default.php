<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Qkeys = Doctrine_Query::create()
	->from('ProductDesignerPredesignKeys')
	//->where('cd.language_id = ?', $lID)
	->orderBy('key_text');

	EventManager::notify('ProductDesignerPredesignKeyListingQueryBeforeExecute', &$Qkeys);

	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 0))
	->setQuery($Qkeys);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_KEYS')),
			array('text' => sysLanguage::get('TABLE_HEADING_KEY_TYPE')),
			array('text' => sysLanguage::get('TABLE_HEADING_SET_FROM')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$keys = &$tableGrid->getResults();
	$infoBoxes = array();
	if (!empty($action)){
		$saveButton = htmlBase::newElement('button')->usePreset('save')->setType('submit');
		$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
		->setHref(itw_app_link(tep_get_all_get_params(array('action')), null, null, 'SSL'));
	}
	
	$allGetParams = tep_get_all_get_params(array('action', 'kID'));
	if ($keys){
		$deleteButton = htmlBase::newElement('button')->usePreset('delete');
		$editButton = htmlBase::newElement('button')->usePreset('edit');
		foreach($keys as $key){
			$keyId = $key['key_id'];

			if ((!isset($_GET['kID']) || $_GET['kID'] == $keyId) && !isset($kInfo) && (substr($action, 0, 3) != 'new')){
				$kInfo = new objectInfo($key);
			}

			$arrowIcon = htmlBase::newElement('icon')->setType('info')
			->setHref(itw_app_link($allGetParams . 'kID=' . $keyId, null, null, 'SSL'));

			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'infobox_id' => $keyId
				),
				'columns' => array(
					array('text' => $key['key_text']),
					array('text' => ucfirst($key['key_type'])),
					array('text' => ucfirst($key['set_from'])),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));
			
			$editButton->setHref(itw_app_link($allGetParams . 'action=edit&kID=' . $keyId, null, null, 'SSL'));
			$deleteButton->setHref(itw_app_link($allGetParams . 'action=delete_key&kID=' . $keyId, null, null, 'SSL'));
			$infoBox = htmlBase::newElement('infobox');
			$infoBox->setButtonBarLocation('top');
			$infoBox->setHeader('<b>' . $key['key_text'] . '</b>');
			$infoBox->addButton($editButton)->addButton($deleteButton);
			
			$infoBoxes[$keyId] = $infoBox->draw();
			unset($infoBox);
		}
	}

	if (!empty($action)){
		$infoBox = htmlBase::newElement('infobox');
		switch ($action) {
			case 'delete_key':
				$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_DELETE_KEY') . '</b>');
				$infoBox->setForm(array(
					'name' => 'keys',
					'action' => itw_app_link(tep_get_all_get_params(array('action')) . 'action=deleteKeyConfirm', null, null, 'SSL')
				));

				$deleteButton->setType('submit');

				$infoBox->addButton($deleteButton)->addButton($cancelButton);

				$infoBox->addContentRow(sysLanguage::get('TEXT_DELETE_KEY_INTRO') . tep_draw_hidden_field('key_id', $kInfo->key_id));
				$infoBox->addContentRow('<b>' . $kInfo->key_text . '</b>');
				
				$infoBoxes[$kInfo->key_id] = $infoBox->draw();
				break;
			case 'new':
			case 'edit':
				if ($action == 'edit'){
					$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_EDIT_KEY') . '</b>');
				}else{
					$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_NEW_KEY') . '</b>');
				}
				$infoBox->setForm(array(
					'name' => 'keys',
					'action' => itw_app_link(tep_get_all_get_params(array('action')) . 'action=saveKey', null, null, 'SSL')
				));

				$infoBox->addButton($saveButton)->addButton($cancelButton);
			
				$keyName = htmlBase::newElement('input')->setName('key_text');
				$keyType = htmlBase::newElement('selectbox')->setName('key_type')->addOption('text', 'Text')->addOption('clipart', 'Clipart');
				$setFrom = htmlBase::newElement('selectbox')->setName('set_from')->addOption('admin', 'Admin')->addOption('catalog', 'Catalog');
				if (isset($kInfo)){
					$keyName->val($kInfo->key_text);
					$keyType->selectOptionByValue($kInfo->key_type);
					$setFrom->selectOptionByValue($kInfo->set_from);
				}
			
				$infoBox->addContentRow('Key Name: ' . $keyName->draw());
				$infoBox->addContentRow('Key Type: ' . $keyType->draw());
				$infoBox->addContentRow('Set From: ' . $setFrom->draw() . '<br /><small>*<b>Admin</b> means from the multi-store setup page</small><br /><small>*<b>Catalog</b> means the customer sets the value</small>');
				
				if ($action == 'edit'){
					$infoBoxes[$kInfo->key_id] = $infoBox->draw();
				}else{
					$infoBoxes['new'] = $infoBox->draw();
				}
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
   	$newKeyButton = htmlBase::newElement('button')->usePreset('install')->setText(sysLanguage::get('TEXT_BUTTON_NEW_KEY'))
   	->setHref(itw_app_link($allGetParams . 'action=new', null, null, 'SSL'));

   	echo $newKeyButton->draw();
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;"><?php
 	if (sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $kID => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $kID . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>