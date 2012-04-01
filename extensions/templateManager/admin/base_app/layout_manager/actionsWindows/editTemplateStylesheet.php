<?php
$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->find((int) $_GET['tID']);

$infoBox = htmlBase::newElement('infobox');
$infoBox->setHeader('<b>Edit Stylesheet</b>');
$infoBox->setButtonBarLocation('top');

$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

$infoBox->addButton($saveButton)->addButton($cancelButton);

$SettingsTable = htmlBase::newElement('table')
	->setCellPadding(5)
	->setCellSpacing(0)
	->css('width', '100%');

$TemplateDir = $Template->Configuration['DIRECTORY']->configuration_value;

$StylesheetFile = sysConfig::getDirFsCatalog() . 'templates/' . $TemplateDir . '/stylesheet.css';
$commentOpen = false;
$Stylesheet = file_get_contents($StylesheetFile);
$fileParsed = array();
$Stylesheet = str_replace(array("\r", "\n", "\t"), '', $Stylesheet);
preg_match_all('/(.*[^\{])[ ]?\{[ ]?(.*[^\}])?\}/imsU', $Stylesheet, &$fileParsed);

$SettingsTable->addHeaderRow(array(
	'columns' => array(
		array('css' => array('width' => '15%'), 'text' => 'Selector'),
		array('css' => array('width' => '42%'), 'text' => 'Definition'),
		array('css' => array('width' => '42%'), 'text' => 'Custom')
	)
));
foreach($fileParsed[1] as $k => $selector){
	$CurrentRules = $fileParsed[2][$k];
	//Pull out any php functions
	$phpFunctions = array();
	preg_match_all('/<\?php[ ](.*[^\?]+);[ ]?\?>/', $CurrentRules, &$phpFunctions);
	$customText = '';
	if (!empty($phpFunctions[0])){
		foreach($phpFunctions[1] as $k => $func){
			$CurrentRules = str_replace('<?php ' . $phpFunctions[1][$k] . ';?>', '', $CurrentRules);
			$customText .= '<?php ' . $phpFunctions[1][$k] . ';?>';
		}
	}

	$inputs = '';
	$RulesArr = explode(';', $CurrentRules);
	foreach($RulesArr as $Rule){
		$Rule = trim($Rule);
		if (!empty($Rule)){
			$info = explode(':', $Rule);
			if(isset($info[0]) && isset($info[1])){
				$inputs .= '<input type="text" value="' . trim($info[0]) . '" style="width:15%;"> : ';
				$inputs .= '<input type="text" value="' . trim($info[1]) . '" style="width:80%;"><br>';
			}
		}
	}
	$SettingsTable->addBodyRow(array(
		'columns' => array(
			array('valign' => 'top', 'text' => '<input style="width:100%" type="text" value="' . $selector . '">'),
			array('valign' => 'top', 'text' => $inputs),
			array('valign' => 'top', 'text' => '<textarea style="width:100%;height:100px;">' . $customText . '</textarea>')
		)
	));
}

$infoBox->addContentRow($SettingsTable->draw());

EventManager::attachActionResponse($infoBox->draw(), 'html');
