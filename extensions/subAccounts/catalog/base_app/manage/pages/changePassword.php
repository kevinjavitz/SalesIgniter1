<?php
	if(Session::exists('childrenAccount')){
		$cID = Session::get('childrenAccount');
	}
	$QAccount = Doctrine_Query::create()
	->from('Customers')
	->where('customers_id=?', $cID)
	->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

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

	$FormTable = htmlBase::newElement('formTable');

	ob_start();
?>

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
	->setText(sysLanguage::get('TEXT_EDIT_ACCOUNT'))
	->setType('submit')
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'save_password',
		'action' => itw_app_link('action=savePassword&appExt=subAccounts&cID='.$cID, 'manage', 'changePassword'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
?>