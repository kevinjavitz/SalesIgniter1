<?php
$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_EDIT') . '</b>');
	$infoBox->setButtonBarLocation('top');
	
	$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBox->addButton($saveButton)->addButton($cancelButton);

$Config = new ModuleConfigReader(
	$_GET['module'],
	$_GET['moduleType'],
	(isset($_GET['modulePath']) ? $_GET['modulePath'] : false)
);
if ($_GET['moduleType'] == 'purchaseType'){
	$tabs = array();
	$tabsPages = array();
	$tabId = 1;
	foreach($Config->getConfig() as $cfg){
		if (!isset($tabs[$cfg->getTab()])){
			$tabs[$cfg->getTab()] = array(
				'panelId' => 'page-' . $tabId,
				'panelHeader' => $cfg->getTab(),
				'panelContent' => ''
			);
			$tabId++;
		}

		$key = $cfg->getKey();
		$value = $cfg->getValue();
		if ($cfg->hasSetFunction() && $cfg->getSetFunction() != 'isArea') {
			eval('$inputField = ' . $cfg->getSetFunction() . "'" . $value . "', '" . $key . "');");
		} else if ($cfg->hasSetFunction() && $cfg->getSetFunction() == 'isArea') {
			$inputField = '<br>' . tep_draw_textarea_field('configuration[' . $key . ']', 'hard', 30, 5, $value, 'class="makeModFCK"');
		}else {
			$inputField = '<br>' . tep_draw_input_field('configuration[' . $key . ']', $value);
		}

		$tabs[$cfg->getTab()]['panelContent'] .= '<br><b>' . $cfg->getTitle() . '</b><br>' . $cfg->getDescription() . $inputField . '<br>';
	}

	$tabPanel = htmlBase::newElement('tabs')
		->setId('module_tabs');
	foreach($tabs as $pInfo){
		$tabPanel->addTabHeader($pInfo['panelId'], array('text' => $pInfo['panelHeader']))
			->addTabPage($pInfo['panelId'], array('text' => $pInfo['panelContent']));
	}
	$infoBox->addContentRow($tabPanel->draw());

}else{
	foreach($Config->getConfig() as $cfg){
		$key = $cfg->getKey();
		$value = $cfg->getValue();
		if ($cfg->hasSetFunction() && $cfg->getSetFunction() != 'isArea') {
			eval('$inputField = ' . $cfg->getSetFunction() . "'" . $value . "', '" . $key . "');");
		} else if ($cfg->hasSetFunction() && $cfg->getSetFunction() == 'isArea') {
			$inputField = tep_draw_textarea_field('configuration[' . $key . ']', 'hard', 30, 5, $value, 'class="makeModFCK"');
		}else {
			$inputField = tep_draw_input_field('configuration[' . $key . ']', $value);
		}

		$infoBox->addContentRow('<b>' . $cfg->getTitle() . '</b><br>' . $cfg->getDescription() . '<br>' . $inputField);
	}
}

/*	$Qconfig = Doctrine_Query::create()
	->from('Modules m')
	->leftJoin('m.ModulesConfiguration c')
	->where('modules_code = ?', $_GET['module'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qconfig){
		foreach($Qconfig as $mInfo){
			if (isset($mInfo['ModulesConfiguration']) && !empty($mInfo['ModulesConfiguration'])){
				foreach($mInfo['ModulesConfiguration'] as $cInfo){
					$key = $cInfo['configuration_key'];
					$value = $cInfo['configuration_value'];
					if (isset($cInfo['set_function']) && !empty($cInfo['set_function']) && $cInfo['set_function'] != 'isArea') {
						eval('$inputField = ' . $cInfo['set_function'] . "'" . $value . "', '" . $key . "');");
					} else if (isset($value['set_function']) && $value['set_function'] == 'isArea') {
						$inputField = tep_draw_textarea_field('configuration[' . $key . ']', 'hard', 30, 5, $value, 'class="makeModFCK"');
					}else {
						$inputField = tep_draw_input_field('configuration[' . $key . ']', $value);
					}
					
					$infoBox->addContentRow('<b>' . $cInfo['configuration_title'] . '</b><br>' . $cInfo['configuration_description'] . '<br>' . $inputField);
				}
			}
		}
	}
	*/
	EventManager::attachActionResponse($infoBox->draw(), 'html');
?>