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
	
	$NewsletterInput = htmlBase::newElement('checkbox')
	->setLabel(sysLanguage::get('ENTRY_NEWSLETTER'))
	->setLabelPosition('before')
	->setName('newsletter')
	->setValue('1');

	$FormTable = htmlBase::newElement('formTable');

	$FormTable->addRow(sysLanguage::get('ENTRY_EMAIL_ADDRESS'));
	$FormTable->addRow(tep_draw_input_field('email_address') . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>');

	$FormTable->addRow(sysLanguage::get('ENTRY_GENDER'));
	$FormTable->addRow(tep_draw_radio_field('gender', 'm') . '&nbsp;&nbsp;' . sysLanguage::get('MALE') . '&nbsp;&nbsp;' . tep_draw_radio_field('gender', 'f') . '&nbsp;&nbsp;' . sysLanguage::get('FEMALE') . '&nbsp;' );


	$FormTable->addRow(sysLanguage::get('ENTRY_DATE_OF_BIRTH'));
	$FormTable->addRow(tep_draw_input_field('dob') . '&nbsp;');

	$FormTable->addRow(sysLanguage::get('ENTRY_FIRST_NAME'), sysLanguage::get('ENTRY_LAST_NAME'));
	$FormTable->addRow(tep_draw_input_field('firstname') . '&nbsp;' . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>', tep_draw_input_field('lastname') . '&nbsp;' . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>');

	ob_start();
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

		</table>

	</div>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_CREATE');
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setText(sysLanguage::get('TEXT_CREATE_ACCOUNT'))
	->setType('submit')
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'create_account',
		'action' => itw_app_link('action=createAccount&appExt=subAccounts', 'manage', 'create', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
?>