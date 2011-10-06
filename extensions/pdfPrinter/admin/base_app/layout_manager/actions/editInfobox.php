<?php
require(sysConfig::getDirFsCatalog() . 'includes/classes/fileSystemBrowser.php');
$Qwidget = Doctrine_Query::create()
	->from('PDFTemplatesInfoboxes')
	->where('box_code = ?', $_GET['widgetCode'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$WidgetCode = $Qwidget[0]['box_code'];
$WidgetPath = sysConfig::getDirFsCatalog() . $Qwidget[0]['box_path'];
if (file_exists($WidgetPath . 'pdfinfobox.php')){
	$WidgetSettings = json_decode(urldecode($_POST['widgetSettings']));

	$Dir = new fileSystemBrowser(sysConfig::getDirFsCatalog() . 'extensions/pdfPrinter/widgetTemplates/');
	$WidgetTemplates = array();
	foreach($Dir->getFiles() as $fileInfo){
		$WidgetTemplates[$fileInfo['fileName_noExt']] = $fileInfo;
	}

	ksort($WidgetTemplates);

	$TemplateFileSelect = htmlBase::newElement('selectbox')
		->setName('template_file')
		->selectOptionByValue((isset($WidgetSettings->template_file) ? $WidgetSettings->template_file : 'noFormatingBox.tpl'));
	foreach($WidgetTemplates as $fileInfo){
		$TemplateFileSelect->addOption($fileInfo['fileName'], $fileInfo['fileName']);
	}

	$WidgetSettingsTable = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0);

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('valign' => 'top', 'css' => array('width' => '100px'), 'text' => 'Template File: '),
			array('valign' => 'top', 'text' => $TemplateFileSelect->draw())
		)
	));

	$idInput = htmlBase::newElement('input')
		->setName('id')
		->css('width', '300px');
	if (isset($WidgetSettings->id)){
		$idInput->val($WidgetSettings->id);
	}

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('valign' => 'top', 'css' => array('width' => '100px'), 'text' => 'Widget Css Id: '),
			array('valign' => 'top', 'text' => $idInput->draw())
		)
	));

	$TitleInputTable = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0);
	$CurLang = sysLanguage::getLanguage(Session::get('languages_id'));
	foreach(sysLanguage::getLanguages() as $lInfo){
		$TitleInput = htmlBase::newElement('input')
			->css('width', '300px')
			->setName('widget_title[' . $lInfo['id'] . ']');

		if (isset($WidgetSettings->widget_title) && isset($WidgetSettings->widget_title->{$lInfo['id']})){
			$TitleInput->val($WidgetSettings->widget_title->{$lInfo['id']});
		}

		$TitleInputTable->addBodyRow(array(
			'columns' => array(
				array('valign' => 'top', 'text' => $lInfo['showName']('&nbsp;')),
				array('valign' => 'top', 'text' => $TitleInput->draw() . '&nbsp;<span class="ui-icon ui-icon-transferthick-e-w translateText" data-from="' . $CurLang['id'] . '" data-to="' . $lInfo['id'] . '" tooltip="Translate From ' . $CurLang['name'] . ' To ' . $lInfo['name'] . '"></span>')
			)
		));
	}

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('valign' => 'top', 'css' => array('width' => '100px'), 'text' => 'Widget Title: '),
			array('valign' => 'top', 'text' => $TitleInputTable->draw())
		)
	));

	if (file_exists($WidgetPath . 'windows/editInfobox.php')){
		require($WidgetPath . 'windows/editInfobox.php');
	}

	$saveButton = htmlBase::newElement('button')
		->attr('data-widget_code', $WidgetCode)
		->addClass('saveButton')
		->usePreset('save');

	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBoxEdit = htmlBase::newElement('infobox')
		->setHeader('<b>Edit Infobox</b>')
		->setButtonBarLocation('top')
		->addButton($saveButton)
		->addButton($cancelButton)
		->addContentRow($WidgetSettingsTable);

	$Response = $infoBoxEdit->draw();
}else{
	$Response = 'Unknown Widget';
}


EventManager::attachActionResponse($Response, 'html');
?>