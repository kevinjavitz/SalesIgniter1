<?php
	$windowAction = $_GET['windowAction'];
	if ($windowAction == 'edit'){
		$Qvalue = Doctrine_Query::create()
		->select('v.products_options_values_id, vd.products_options_values_name')
		->from('ProductsOptionsValues v')
		->leftJoin('v.ProductsOptionsValuesDescription vd')
		->where('v.products_options_values_id = ?', $_GET['value_id'])
		->orderBy('vd.products_options_values_name')
		->fetchOne()->toArray();
	}

	$languages = tep_get_languages();
	$valueNames = htmlBase::newElement('table')->setCellPadding('3')->setCellSpacing('0');
	for ($i=0, $n=sizeof($languages); $i<$n; $i++){
		$langID = $languages[$i]['id'];
				
		$langImage = htmlBase::newElement('image')
		->setSource(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'])
		->setTitle($languages[$i]['name']);
				
		$valueNameInput = htmlBase::newElement('input')->setName('value_name[' . $langID . ']');
		if (isset($Qvalue)){
			$valueNameInput->setValue($Qvalue['ProductsOptionsValuesDescription'][$langID]['products_options_values_name']);
		}
		$valueNames->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => $langImage),
				array('addCls' => 'main', 'text' => $valueNameInput)
			)
		));
	}

	$finalTable = htmlBase::newElement('table')->setCellPadding('3')->setCellSpacing('0');
	
	if (isset($_GET['value_id'])){
		$finalTable->attr('value_id', (int)$_GET['value_id']);
	}
			
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_VALUE_NAME') . '</b>')
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $valueNames)
	)));

	EventManager::attachActionResponse($finalTable->draw(), 'html');
?>