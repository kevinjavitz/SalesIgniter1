<?php
	$Qmanufacturers = Doctrine_Query::create()
	->from('Manufacturers m')
	->leftJoin('m.ManufacturersInfo mi')
	//->where('mi.languages_id = ?', Session::get('languages_id'))
	->orderBy('m.manufacturers_name');

	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qmanufacturers);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_MANUFACTURERS')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$infoBoxes = array();

	$Result = &$tableGrid->getResults();
	if ($Result){
		$allGetParams = tep_get_all_get_params(array('action', 'mID'));
		foreach($Result as $manufacturer){
			$id = $manufacturer['manufacturers_id'];
			$name = $manufacturer['manufacturers_name'];
			$image = $manufacturer['manufacturers_image'];
			$dateAdded = $manufacturer['date_added'];
			$dateModified = $manufacturer['last_modified'];

			$Qproducts = Doctrine_Query::create()
			->select('count(*) as total')
			->from('Products')
			->where('manufacturers_id = ?', $id)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if ((!isset($_GET['mID']) || $_GET['mID'] == $id) && !isset($mInfo) && !strstr($action, 'new')) {
				$mInfo = new objectInfo($manufacturer);
			}

			$arrowIcon = htmlBase::newElement('icon')->setType('info')
			->setHref(itw_app_link($allGetParams . 'mID=' . $id));

			$tableGrid->addBodyRow(array(
				'rowAttr' => array('infobox_id' => $id),
				'columns' => array(
					array('text' => $name),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));

			$infoBox = htmlBase::newElement('infobox');
			$infoBox->setHeader('<b>' . $name . '</b>');
			$infoBox->setButtonBarLocation('top');

			$editButton = htmlBase::newElement('button')->usePreset('edit')
			->setHref(itw_app_link($allGetParams . 'mID=' . $id . '&action=edit'));

			$deleteButton = htmlBase::newElement('button')
			->usePreset('delete')
			->addClass('deleteButton')
			->attr('data-id', $id)
			->attr('data-name', $name)
			->attr('data-products_count', $Qproducts[0]['total']);

			$infoBox->addButton($editButton)->addButton($deleteButton);

			$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_DATE_ADDED') . ' ' . tep_date_short($dateAdded));

			if (tep_not_null($dateModified)){
				$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_LAST_MODIFIED') . ' ' . tep_date_short($dateModified));
			}

			//$infoBox->addContentRow(tep_info_image($image, $name));
			$infoBox->addContentRow(sysLanguage::get('TEXT_PRODUCTS') . ' ' . $Qproducts[0]['total']);

        	$infoBoxes[$id] = $infoBox->draw();
		}
	}
    $infoBox = htmlBase::newElement('infobox');
	switch($action){
		case 'new':
		case 'edit':

			$infoBox->setHeader('<b>' . constant('TEXT_HEADING_' . strtoupper($action) . '_MANUFACTURER') . '</b>');
			$infoBox->setForm(array(
			'name'   => 'manufacturers',
			'action' => itw_app_link('action=saveManufacturer' . ($action == 'edit' ? '&mID=' . $mInfo->manufacturers_id : '')),
			'attr'   => array('enctype' => 'multipart/form-data')
			));

			$saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
			$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
			->setHref(itw_app_link(tep_get_all_get_params(array('action'))));

			if ($action == 'edit'){
				$saveButton->setText(sysLanguage::get('IMAGE_UPDATE'));
			}
			$infoBox->addButton($saveButton)->addButton($cancelButton);

			$urlString = '';
			$htcTitleString = '';
			$htcDescString = '';
			$htcKeywordsString = '';
			$htcDescriptionString = '';
			//$languages = tep_get_languages();
			foreach(sysLanguage::getLanguages() as $lInfo){
				$langId = $lInfo['id'];
				//$langImage = tep_image(sysConfig::getDirWsCatalog() . 'includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']);

				$urlInput = htmlBase::newElement('input')->setName('manufacturers_url[' . $langId . ']');
				$htcTitleInput = htmlBase::newElement('input')->setName('manufacturers_htc_title_tag[' . $langId . ']');
				$htcDescInput = htmlBase::newElement('input')->setName('manufacturers_htc_desc_tag[' . $langId . ']');
				$htcKeywordsInput = htmlBase::newElement('input')->setName('manufacturers_htc_keywords_tag[' . $langId . ']');
				$htcDescriptionInput = htmlBase::newElement('textarea')->setName('manufacturers_htc_description[' . $langId . ']');
				if ($action == 'edit'){
					$info = $mInfo->ManufacturersInfo[$langId];

					$urlInput->val($info['manufacturers_url']);
					$htcTitleInput->val($info['manufacturers_htc_title_tag']);
					$htcDescInput->val($info['manufacturers_htc_desc_tag']);
					$htcKeywordsInput->val($info['manufacturers_htc_keywords_tag']);
					$htcDescriptionInput->val($info['manufacturers_htc_description']);
				}

				$urlString .= '<br>' . $lInfo['showName']() . '&nbsp;' . $urlInput->draw();

				//BOC HTC
				$htcTitleString .= '<br>' . $lInfo['showName']() . '&nbsp;' . $htcTitleInput->draw();
				$htcDescString .= '<br>' . $lInfo['showName']() . '&nbsp;' . $htcDescInput->draw();
				$htcKeywordsString .= '<br>' . $lInfo['showName']() . '&nbsp;' . $htcKeywordsInput->draw();
				$htcDescriptionString .= '<br>' . $lInfo['showName']() . '&nbsp;' . $htcDescriptionInput->draw();
				// EOC HTC
			}

			$nameInput = htmlBase::newElement('input')->setName('manufacturers_name');
			$imageInput = htmlBase::newElement('input')->setType('file')->setName('manufacturers_image');

			if ($action == 'edit'){
				$nameInput->val($mInfo->manufacturers_name);
			}

			$infoBox->addContentRow(constant('TEXT_INFO_' . strtoupper($action) . '_MANUFACTURER_INTRO'));
			$infoBox->addContentRow('<b>' . sysLanguage::get('TEXT_MANUFACTURERS_NAME') . '</b><br>' . $nameInput->draw());
			$infoBox->addContentRow('<b>' . sysLanguage::get('TEXT_MANUFACTURERS_IMAGE') . '</b><br>' . $imageInput->draw());
			$infoBox->addContentRow('<b>' . sysLanguage::get('TEXT_MANUFACTURERS_URL') . '</b>' . $urlString);
			// HTC BOC
			$infoBox->addContentRow('<b>' . sysLanguage::get('TEXT_HTC_MANUFACTURER_TITLE') . '</b>' . $htcTitleString);
			$infoBox->addContentRow('<b>' . sysLanguage::get('TEXT_HTC_MANUFACTURER_DESC') . '</b>' . $htcDescString);
			$infoBox->addContentRow('<b>' . sysLanguage::get('TEXT_HTC_MANUFACTURER_KEYWORDS') . '</b>' . $htcKeywordsString);
			$infoBox->addContentRow('<b>' . sysLanguage::get('TEXT_HTC_MANUFACTURER_DESC') . '</b>' . $htcDescriptionString);
			// HTC EOC

			if ($action == 'edit'){
				$infoBoxes[$mInfo->manufacturers_id] = $infoBox->draw();
			}else{
				$infoBoxes['new'] = $infoBox->draw();
			}
			break;
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_MANUFACTURERS');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
  </div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td align="right" class="smallText"><?php
    	$newButton = htmlBase::newElement('button')->usePreset('install')->setText(sysLanguage::get('TEXT_BUTTON_NEW_MANUFACTURER'))
    	->setHref(itw_app_link('action=new', 'products', 'manufacturers'));

    	echo $newButton->draw();
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;"><?php
 	if (sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $infoBoxId => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $infoBoxId . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>