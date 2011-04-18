<?php
	$Membership = Doctrine_Core::getTable('Membership');
	if (isset($_GET['pID'])){
		$Package = $Membership->find((int) $_GET['pID']);
		$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_EDIT_PACKAGE');
		$boxIntro = sysLanguage::get('TEXT_INFO_EDIT_INTRO');
	}else{
		$Package = $Membership->getRecord();
		$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_NEW_PACKAGE');
		$boxIntro = sysLanguage::get('TEXT_INFO_INSERT_INTRO');
	}
	
	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . $boxHeading . '</b>');
	$infoBox->setButtonBarLocation('top');

	$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBox->addButton($saveButton)->addButton($cancelButton);

	$infoBox->addContentRow($boxIntro);
	
	$nameInputs = '';
	foreach(sysLanguage::getLanguages() as $lInfo){
		$htmlInput = htmlBase::newElement('input')
		->setName('name[' . $lInfo['id'] . ']');
		if (!empty($Package->MembershipPlanDescription[$lInfo['id']]->name)){
			$htmlInput->val($Package->MembershipPlanDescription[$lInfo['id']]->name);
		}
		
		$nameInputs .= '<br>' . $lInfo['showName']('&nbsp;') . ': ' . $htmlInput->draw();
	}
	$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_PACKAGE_NAME') . $nameInputs);

	$infoBox->addContentRow('Sort Order:' . '<br>' . tep_draw_input_field('sort_order', $Package->sort_order));
	$infoBox->addContentRow('Default Plan:' . '<br>' . tep_draw_checkbox_field('default_plan', '1', ($Package->default_plan == '1')));
	$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_FREE_TRIAL_AMOUNT') . '<br>' . tep_draw_input_field('free_trial_amount', $Package->free_trial_amount));
	$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_MEMBERSHIP_MONTHS') . '<br>' . tep_draw_input_field('membership_months', $Package->membership_months));
	$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_MEMBERSHIP_DAYS') . '<br>' . tep_draw_input_field('membership_days', $Package->membership_days));
	$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_NO_OF_TITLES') . '<br>' . tep_draw_input_field('no_of_titles', $Package->no_of_titles));
	$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_TAX_CLASS') . '<br>' . tep_draw_pull_down_menu('rent_tax_class_id', $tax_class_array, $Package->rent_tax_class_id, 'onchange="updateGross()"'));
	$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_PRICE') . '<br>' . tep_draw_input_field('price', $Package->price));
	$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_PRICE_GROSS') . '<br>' . tep_draw_input_field('gross_price', $Package->price));
	$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_FREE_TRIAL') . '<br>' . tep_draw_input_field('free_trial', $Package->free_trial));

	EventManager::notify('MembershipPackageEditWindowBeforeDraw', $infoBox, $Package);
	
	$javaScript = '<script language="javascript">' . "\n" . 
		'var tax_rates = new Array();' . "\n";

	for($i=0, $n=sizeof($tax_class_array); $i<$n; $i++){
		if ($tax_class_array[$i]['id'] > 0){
			$javaScript .= 'tax_rates["' . $tax_class_array[$i]['id'] . '"] = ' . tep_get_tax_rate_value($tax_class_array[$i]['id']) . ';' . "\n";
		}
	}
	
	$javaScript .= '$(document).ready(function (){' . "\n" . 
		'updateGross();' . "\n" . 
	'});' . "\n" . 
	'</script>' . "\n";
	
	EventManager::attachActionResponse($javaScript . $infoBox->draw(), 'html');
?>