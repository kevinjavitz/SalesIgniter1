<?php
	$BillingPasswordInput = htmlBase::newElement('input')
	->setType('password')
	->attr('maxlength', '40')
	->setName('password')
	->setRequired(true);

	$BillingPasswordConfirmInput = htmlBase::newElement('input')
	->setType('password')
	->attr('maxlength', '40')
	->setName('confirmation')
	->setRequired(true);
	if(sysConfig::get('ACCOUNT_NEWSLETTER') == 'true') {
		$NewsletterInput = htmlBase::newElement('checkbox')
		->setLabel(sysLanguage::get('ENTRY_NEWSLETTER'))
		->setLabelPosition('before')
		->setName('newsletter')
		->setValue('1');
	}

	$FormTable = htmlBase::newElement('formTable');

	if (sysConfig::get('ACCOUNT_COMPANY') == 'true'){
		$FormTable->addRow(sysLanguage::get('ENTRY_COMPANY'));
		$FormTable->addRow(tep_draw_input_field('company'));
	}

	$FormTable->addRow(sysLanguage::get('ENTRY_EMAIL_ADDRESS'));
	$FormTable->addRow(tep_draw_input_field('email_address') . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>');

	if (sysConfig::get('ACCOUNT_GENDER') == 'true'){
		$FormTable->addRow(sysLanguage::get('ENTRY_GENDER'));
		$FormTable->addRow(tep_draw_radio_field('gender', 'm') . '&nbsp;&nbsp;' . sysLanguage::get('MALE') . '&nbsp;&nbsp;' . tep_draw_radio_field('gender', 'f') . '&nbsp;&nbsp;' . sysLanguage::get('FEMALE') . '&nbsp;' );
	}
	if (sysConfig::get('ACCOUNT_DOB') == 'true'){
		$FormTable->addRow(sysLanguage::get('ENTRY_DATE_OF_BIRTH'));
		$FormTable->addRow(tep_draw_input_field('dob') . '&nbsp;');
	}

	$FormTable->addRow(sysLanguage::get('ENTRY_FIRST_NAME'), sysLanguage::get('ENTRY_LAST_NAME'));
	$FormTable->addRow(tep_draw_input_field('firstname') . '&nbsp;' . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>', tep_draw_input_field('lastname') . '&nbsp;' . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>');

	$FormTable->addRow(sysLanguage::get('ENTRY_STREET_ADDRESS'));
	$FormTable->addRow(tep_draw_input_field('street_address') . '&nbsp;' . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>');

	if (sysConfig::get('ACCOUNT_SUBURB') == 'true'){
		$FormTable->addRow(sysLanguage::get('ENTRY_SUBURB'));
		$FormTable->addRow(tep_draw_input_field('suburb') . '&nbsp;' . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>');
	}

	if (sysConfig::get('ACCOUNT_FISCAL_CODE_REQUIRED') == 'true'){
		$FormTable->addRow(sysLanguage::get('ENTRY_FISCAL_CODE'));
		$FormTable->addRow(tep_draw_input_field('fiscal_code') . '&nbsp;' . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>');
	}

	if (sysConfig::get('ACCOUNT_VAT_NUMBER_REQUIRED') == 'true'){
		$FormTable->addRow(sysLanguage::get('ENTRY_VAT_NUMBER'));
		$FormTable->addRow(tep_draw_input_field('vat_number') . '&nbsp;' . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>');
	}

	if (sysConfig::get('ACCOUNT_CITY_BIRTH_REQUIRED') == 'true'){
		$FormTable->addRow(sysLanguage::get('ENTRY_CITY_BIRTH'));
		$FormTable->addRow(tep_draw_input_field('city_birth'));
	}

	$FormTable->addRow(sysLanguage::get('ENTRY_CITY'));
	$FormTable->addRow(tep_draw_input_field('city') . '&nbsp;' . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>');

	if (sysConfig::get('ACCOUNT_STATE') == 'true'){
		$FormTable->addRow(sysLanguage::get('ENTRY_STATE'));
		$FormTable->addRow( tep_draw_input_field('state','','id="state"'). '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>');
	}

	$FormTable->addRow(sysLanguage::get('ENTRY_POST_CODE'));
	$FormTable->addRow(tep_draw_input_field('postcode') . '&nbsp;' . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>');

	$FormTable->addRow(sysLanguage::get('ENTRY_COUNTRY'));
	$FormTable->addRow(tep_get_country_list('country', sysLanguage::get('STORE_COUNTRY')) . '&nbsp;' . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>');

	$FormTable->addRow(sysLanguage::get('ENTRY_TELEPHONE_NUMBER'));
	$FormTable->addRow(tep_draw_input_field('telephone') . '&nbsp;' . ((sysConfig::get('ACCOUNT_TELEPHONE_REQUIRED') == 'true')?'<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>':''));

	ob_start();

	echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">
		<tr id="logInRow"' . ($userAccount->isLoggedIn() === true ? ' style="display:none"' : '') . '>
			<td class="main">'.sysLanguage::get('TEXT_ALREADY_HAVE_ACCOUNT') . '&nbsp;&nbsp;' . 
				htmlBase::newElement('a')
				->setHref(itw_app_link(null, 'account', 'login', 'SSL'))
				->html(sysLanguage::get('TEXT_BUTTON_LOGIN'))
				->attr('id', 'loginButton')
				->draw() .
			'</td>
		</tr>
	</table>';
?>
	<div class="">
		<div class="" style="margin-top:10px;margin-bottom:10px;line-height:2em;"><?php echo sysLanguage::get('TEXT_ADDRESS'); ?></div>
		<?php
			echo $FormTable->draw();
		?>
	</div>
	<div class="">
		<div class="" style="margin-top:10px;margin-bottom:10px;line-height:2em;"><?php echo sysLanguage::get('TEXT_ACCOUNT_SETTINGS'); ?></div>
		<table class="accountSettings" cellpadding="0" cellspacing="0" border="0" style="margin:.3em;">
			<tr>
				<td><?php echo sysLanguage::get('ENTRY_PASSWORD'); ?></td>
				<td><?php echo $BillingPasswordInput->draw(); ?></td>
				<td><div id="pstrength_password"></div></td>
			</tr>
			<tr>
				<td><?php echo sysLanguage::get('ENTRY_PASSWORD_CONFIRMATION'); ?></td>
				<td colspan="2"><?php echo $BillingPasswordConfirmInput->draw(); ?></td>
			</tr>
			<?php if(sysConfig::get('ACCOUNT_NEWSLETTER') == 'true') { ?>
			<tr>
				<td colspan="3"><?php echo $NewsletterInput->draw(); ?></td>
			</tr>
			<?php } ?>
		</table>
		<?php
			if (sysConfig::get('TERMS_CONDITIONS_CREATE_ACCOUNT') == 'true'){
		?>
		<div onclick="window.document.create_account.terms.checked = !window.document.create_account.terms.checked;" style="margin-top:.5em;padding:.5em;text-align:center;"><b><?php echo sprintf(sysLanguage::get('ENTRY_PRIVACY_AGREEMENT'), itw_app_link('appExt=infoPages', 'show_page', 'conditions') . '" onclick="popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'conditions', 'SSL') . '\',\'800\',\'600\');return false;'); ?></b>&nbsp;<?php echo tep_draw_checkbox_field('terms','1', false, 'onclick="window.document.create_account.terms.checked = !window.document.create_account.terms.checked;"'); ?></div>
		<?php
			}else{
				echo $htmlTerms = htmlBase::newElement('input')
				->setType('hidden')
				->setName('terms')
				->attr('checked', true)
				->setValue('1')
				->draw();
			}
		?>
	</div>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_CREATE');
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setType('submit')
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'create_account',
		'action' => itw_app_link('action=createAccount', 'account', 'create', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
?>