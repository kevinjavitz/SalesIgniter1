<?php
	$membership = $userAccount->plugins['membership'];
	$membership->loadMembershipInfo();
ob_start();
?>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<table border="0" width="100%" cellspacing="0" cellpadding="2">

				<tr>
					<td>
						<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
						<tr class="infoBoxContents">
							<td>
								<table border="0" cellspacing="2" cellpadding="2">
									<?php

if($payment_method = "paypal" || $payment_method = "paypal_ipn") {
?>
								<tr>
									<td colspan=3 class="main"><?php echo sysLanguage::get('TEXT_INFO_PAYPAL');?></td>
								</tr>
<?php
}
?>


								</table>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_MEMBERSHIP_CANCEL');
	$payment_method = $membership->membershipInfo['payment_method'];

	if ($payment_method != 'paypal' && $payment_method != 'paypal_ipn'){
		$pageContent->set('pageForm', array(
			'name' => 'account_edit',
			'action' => itw_app_link('action=cancelMembership', 'account', 'membership_cancel', 'SSL'),
			'method' => 'post'
		));
		
		$pageButtons = htmlBase::newElement('button')
		->usePreset('continue')
		->setType('submit')
		->setName('continue')
		->setText(sysLanguage::get('TEXT_BUTTON_CANCEL'))
		->draw();
	}else{
		$pageContent->set('pageForm', array(
			'name' => 'checkout_confirmation',
			'action' => OrderPaymentModules::getModule($payment_method, true)->form_action_url,
			'method' => 'post'
		));
		$pageButtons = OrderPaymentModules::getModule($payment_method, true)->process_cancel_button();
	}
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
