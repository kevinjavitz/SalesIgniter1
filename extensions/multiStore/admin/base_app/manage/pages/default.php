<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$currentPage = (isset($_GET['page']) ? (int)$_GET['page'] : 0);
	$tableGrid = htmlBase::newElement('newGrid');

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_STORES_ID')),
			array('text' => sysLanguage::get('TABLE_HEADING_STORES')),
			array('text' => sysLanguage::get('TABLE_HEADING_STORES_DOMAIN')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$Qstores = Doctrine_Query::create()
	->from('Stores')
	->orderBy('stores_name');
	
	$allGetParams = tep_get_all_get_params(array('action', 'sID'));
	$Result = $Qstores->execute(array(), Doctrine::HYDRATE_ARRAY);
	if ($Result){
		$saveButton = htmlBase::newElement('button')->usePreset('save');
		$editButton = htmlBase::newElement('button')->usePreset('edit');
		$deleteButton = htmlBase::newElement('button')->usePreset('delete');
		if (!empty($action)){
			$cancelButton = htmlBase::newElement('button')->usePreset('cancel');
		}
		$infoBoxes = array();
		
		foreach($Result as $storeInfo){
			$storeId = $storeInfo['stores_id'];
			$storeName = $storeInfo['stores_name'];
			$storeDomain = $storeInfo['stores_domain'];
			
			if ((!isset($_GET['sID']) || (isset($_GET['sID']) && ($_GET['sID'] == $storeId))) && !isset($sInfo) && (substr($action, 0, 3) != 'new')) {
				$sInfo = new objectInfo($storeInfo);
			}
		
			$arrowIcon = htmlBase::newElement('icon')->setType('info')
			->setHref(itw_app_link('appExt=multiStore&sID=' . $storeId));

			$tableGrid->addBodyRow(array(
				'rowAttr' => array('infobox_id' => $storeId),
				'columns' => array(
					array('text' => $storeId),
					array('text' => $storeName),
					array('text' => '<a href="http://' . $storeDomain . '">' . $storeDomain . '</a>'),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));
			
			$infoBox = htmlBase::newElement('infobox');
			$infoBox->setHeader('<b>' . $storeName . '</b>');

			$editButton->setHref(itw_app_link($allGetParams . 'sID=' . $storeId, null, 'new_store'));
			$deleteButton->addClass('deleteButton')->attr('data-store_id', $storeId);

			$infoBox->addButton($editButton);
				
			if ($storeId > 1){
				$infoBox->addButton($deleteButton);
			}
				
			$infoBox->addContentRow(sysLanguage::get('TEXT_STORES_DOMAIN') . $storeDomain);
			$infoBox->addContentRow(sysLanguage::get('TEXT_STORES_SSL_DOMAIN') . $storeInfo['stores_ssl_domain']);
			$infoBox->addContentRow(sysLanguage::get('TEXT_STORES_TEMPLATE') . $storeInfo['stores_template']);
			
			$infoBoxes[$storeId] = $infoBox->draw();
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
   	$newStoreButton = htmlBase::newElement('button')->usePreset('install')->setText(sysLanguage::get('TEXT_BUTTON_NEW_STORE'))
   	->setHref(itw_app_link($allGetParams, null, 'new_store'));

   	echo $newStoreButton->draw();
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;"><?php
 	if (sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $storeID => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $storeID . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>