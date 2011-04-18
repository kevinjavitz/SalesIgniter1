<?php
	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_EDIT_MODULE') . '</b>');
	$infoBox->setButtonBarLocation('top');
	
	$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBox->addButton($saveButton)->addButton($cancelButton);
	
	$Qconfig = Doctrine_Query::create()
	->from('Modules m')
	->leftJoin('m.ModulesConfiguration c')
	->where('modules_code = ?', $_GET['module'])
	->orderBy('c.sort_order ASC')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qconfig){
		foreach($Qconfig as $mInfo){
			if (isset($mInfo['ModulesConfiguration']) && !empty($mInfo['ModulesConfiguration'])){
				foreach($mInfo['ModulesConfiguration'] as $cInfo){
					$key = $cInfo['configuration_key'];
					$value = $cInfo['configuration_value'];
					if (isset($cInfo['set_function']) && $cInfo['set_function'] != 'isArea') {
						eval('$inputField = ' . $cInfo['set_function'] . "'" . $value . "', '" . $key . "');");
					} else if (isset($value['set_function']) && $value['set_function'] == 'isArea') {
						$inputField = tep_draw_textarea_field('configuration[' . $key . ']', 'hard', 30, 5, $value, 'class="makeModFCK"');
					}else {
						$inputField = htmlBase::newElement('input')
						->css(array(
							'width' => '80%'
						))
						->setName('configuration[' . $key . ']')
						->val($value)
						->draw();
					}
					
					$infoBox->addContentRow('<b>' . $cInfo['configuration_title'] . '</b><br>' . $cInfo['configuration_description'] . '<br>' . $inputField);
				}
			}
		}
	}
	
	EventManager::attachActionResponse($infoBox->draw(), 'html');
?>