<?php
$ProductDescriptionLanguageTabs = htmlBase::newElement('tabs')
	->setId('productDescriptionLanguageTabs')
	->setSelected('productDescriptionLangTab_' . sysLanguage::getId());
	
foreach(sysLanguage::getLanguages() as $lInfo){
	$lID = $lInfo['id'];

	$ProductsName = htmlBase::newElement('input')
		->setName('products_name[' . $lID . ']');

	if (sysConfig::get('ENABLE_HTML_EDITOR') == 'false') {
		$ProductsDescription = htmlBase::newElement('textarea')
			->attr('rows', '8')
			->attr('cols', '23');
	}else{
		$ProductsDescription = htmlBase::newElement('ck_editor');
	}
	$ProductsDescription->setName('products_description[' . $lID . ']');

	$ProductsSeoUrl = htmlBase::newElement('input')
		->setName('products_seo_url[' . $lID . ']');

	$ProductsName->setValue(stripslashes($Product->ProductsDescription[$lID]['products_name']));
	$ProductsDescription->html(stripslashes($Product->ProductsDescription[$lID]['products_description']));

	$ProductsSeoUrl->setValue($Product->ProductsDescription[$lID]['products_seo_url']);

	$inputTable = htmlBase::newElement('table')
		->setCellPadding(0)
		->setCellSpacing(0);

	$inputTable->addBodyRow(array(
			'columns' => array(
				array('text' => sysLanguage::get('TEXT_PRODUCTS_NAME')),
				array('text' => $ProductsName->draw())
			)
		));

	$inputTable->addBodyRow(array(
			'columns' => array(
				array('text' => sysLanguage::get('TEXT_PRODUCTS_DESCRIPTION')),
				array('text' => $ProductsDescription->draw())
			)
		));

	$inputTable->addBodyRow(array(
			'columns' => array(
				array('colspan' => 2, 'text' => '<hr>' . sysLanguage::get('TEXT_PRODUCT_METTA_INFO'))
			)
		));

	$inputTable->addBodyRow(array(
			'columns' => array(
				array('text' => sysLanguage::get('TEXT_PRODUCTS_SEO_URL')),
				array('text' => $ProductsSeoUrl->draw())
			)
		));

	/**
	 * this event expects an array having two elements: label and content | i.e. (array(label=>'', content=>''))
	 */
	$contents_middle = array();
	EventManager::notify('ProductsFormMiddle', $lID, &$contents_middle, $Product);

	if (is_array($contents_middle)){
		foreach($contents_middle as $element){
			if (is_array($element)){
				if (!isset($element['label'])) {
					$element['label'] = 'no_defined';
				}
				if (!isset($element['content'])) {
					$element['content'] = 'no_defined';
				}

				$inputTable->addBodyRow(array(
						'columns' => array(
							array('text' => $element['label']),
							array('text' => $element['content'])
						)
					));
			}
			else {
				$inputTable->addBodyRow(array(
						'columns' => array(
							array('colspan' => 2, 'text' => $element)
						)
					));
			}
		}
	}

	$ProductDescriptionLanguageTabs->addTabHeader('productDescriptionLangTab_' . $lID, array('text' => $lInfo['showName']()))
		->addTabPage('productDescriptionLangTab_' . $lID, array('text' => $inputTable));
}
echo $ProductDescriptionLanguageTabs->draw();
