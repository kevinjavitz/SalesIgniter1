<?php
require(sysConfig::getDirFsCatalog() . 'includes/classes/fileSystemBrowser.php');
$Widget = $TemplateManager->getWidget($_GET['widgetCode']);
if ($Widget !== false){
	$WidgetSettings = json_decode(urldecode($_POST['widgetSettings']));

	$WidgetTemplates = array();
	foreach($TemplateManager->getWidgetTemplatePaths() as $WidgetTemplateCode => $WidgetTemplatePath){
		$WidgetTemplates[$WidgetTemplateCode] = $WidgetTemplatePath;
	}

	ksort($WidgetTemplates);

	$TemplateFileSelect = htmlBase::newElement('selectbox')
		->setName('template_file')
		->selectOptionByValue((isset($WidgetSettings->template_file) ? $WidgetSettings->template_file : 'noFormatingBox.tpl'));
	foreach($WidgetTemplates as $WidgetTemplateCode => $WidgetTemplatePath){
		$TemplateFileSelect->addOption($WidgetTemplateCode . '.tpl', $WidgetTemplateCode);
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

	if (file_exists($Widget->getPath() . 'windows/editInfobox.php')){
		require($Widget->getPath() . 'windows/editInfobox.php');
	}

	$saveButton = htmlBase::newElement('button')
		->attr('data-widget_code', $Widget->getBoxCode())
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