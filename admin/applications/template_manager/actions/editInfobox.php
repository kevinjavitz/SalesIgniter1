<?php
	$template = $_GET['template'];
	$data = explode('_', $_GET['box']);
	$column = ($data[0] == 'rightColumnBox' ? 'right' : 'left');
	$box = $data[1];

	$header = '<b>' . 'Edit Infobox' . '</b>';

	$Infobox = Doctrine_Query::create()
	->from('TemplatesInfoboxes i')
	->leftJoin('i.TemplatesInfoboxesToTemplates i2t')
	->where('i2t.template_name = ?', $template)
	->andWhere('i2t.template_column = ?', $column)
	->andWhere('i.box_code = ?', $box)
	->fetchOne();

	$templates = new fileSystemBrowser(sysConfig::getDirFsCatalog() . 'templates/' . $template . '/boxes/');
	$files = $templates->getFiles();
	$filesArray = array();
	foreach($files as $fileInfo){
		$filesArray[$fileInfo['fileName_noExt']] = $fileInfo;
	}

	ksort($filesArray);

	if (empty($Infobox['TemplatesInfoboxesToTemplates'][$template]['template_file'])){
		$selected = 'box.tpl';
	}else{
		$selected = $Infobox['TemplatesInfoboxesToTemplates'][$template]['template_file'];
	}
	$templateFile = htmlBase::newElement('selectbox')
	->setName('template_file')
	->selectOptionByValue($selected);
	foreach($filesArray as $fileInfo){
		$templateFile->addOption($fileInfo['fileName'], $fileInfo['fileName']);
	}

	$updateAllCheckbox = htmlBase::newElement('checkbox')
	->setName('updateAllBoxes')
	->setLabel('<b>' . 'Update All Templates' . '</b>')
	->setLabelPosition('after')
	->setValue('1')
	->setChecked(false);

	$finalTable = htmlBase::newElement('table')->setCellPadding('3')->setCellSpacing('0');

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . 'Template File' . '</b>')
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $templateFile)
	)));
	
	$boxDir = $Infobox['box_path'];
	
	if (file_exists(sysConfig::getDirFsCatalog() . $boxDir . 'windows/' . $action . '.php')){
		ob_start();
		if (file_exists(sysConfig::getDirFsCatalog() . $boxDir . 'javascript/' . $action . '.js')){
			echo '<script type="text/javascript" src="' . sysConfig::getDirWsCatalog() . $boxDir . 'javascript/' . $action . '.js"></script>';
		}
		require(sysConfig::getDirFsCatalog() . $boxDir . 'windows/' . $action . '.php');
		$contents = ob_get_contents();
		ob_end_clean();
		
		$finalTable->addBodyRow(array('columns' => array(
			array('addCls' => 'main', 'text' => $contents)
		)));
	}
	
	/*$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $updateAllCheckbox)
	)));*/

	$infoBoxEdit = htmlBase::newElement('infobox');
	$infoBoxEdit->setHeader('<b>Edit Infobox</b>');
	$infoBoxEdit->setButtonBarLocation('top');

	$saveButton = htmlBase::newElement('button')
	->attr('data-infobox_id', $Infobox['box_id'])
	->attr('data-box', $_GET['box'])
	->addClass('saveButton')
	->usePreset('save');
	
	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBoxEdit->addButton($saveButton)->addButton($cancelButton);

	$infoBoxEdit->addContentRow($finalTable);
		
	EventManager::attachActionResponse($infoBoxEdit->draw(), 'html');
?>