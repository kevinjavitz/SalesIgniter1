<?php
	ob_start();
?>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<table border="0" width="100%" cellspacing="0" cellpadding="2">
				<tr>
            	<td>
            		<table border="0" width="100%" cellspacing="0" cellpadding="2">
						<tr>
							<td class="main"><b><?php echo sysLanguage::get('TEXT_VIEW_CANCEL'); ?></b></td>
							<td class="inputRequirement" align="right"><?php echo sysLanguage::get('FORM_REQUIRED_INFORMATION'); ?></td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
						<tr class="infoBoxContents">
							<td>
								<table border="0" cellspacing="2" cellpadding="2">
<?php

if($email_sent=="tristate") {
   	$customer_plan_query = "select plan_id from ".TABLE_CUSTOMERS." where customers_id=".(int)$userAccount->getCustomerId();
   	$customer_plan = tep_db_query($customer_plan_query);
   	$pID = $customer_plan['plan_id'];
	$customers_query_raw = "select tm.*,tmd.name as package_name, tt.tax_rate as tax from ".TABLE_MEMBER." tm left join membership_plan_description tmd on tm.plan_id=tmd.plan_id left join ".TABLE_TAX_RATES." tt on tt.tax_rates_id = tm.rent_tax_class_id where tmd.language_id=".Session::get('languages_id')." and plan_id= " . (int)$pID;
	$customers_query = tep_db_query($customers_query_raw);
	while($customers = tep_db_fetch_array($customers_query))
	{

?>
								<tr>
									<td class="main"><?= sysLanguage::get('AMC_PACKAGE_NAME') ?></td>
									<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
									<td class="main"><?php echo $customers['package_name']; ?></td>
								</tr>
								<tr>
									<td class="main"><?= sysLanguage::get('TEXT_MEMBERSHIP_PERIOD') ?></td>
									<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
									<td class="main"><?php echo  ($customers[membership_months]>0?$customers[membership_months].' Month(s) ':'').($customers[membership_days]>0?$customers[membership_days].' days':''); ?></td>
								</tr>
								<tr>
									<td class="main"><?= sysLanguage::get('AMC_N_OF_TITLES') ?></td>
									<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
									<td class="main"><?php echo $customers['no_of_titles']; ?></td>
								</tr>
								<tr>
									<td class="main"><?= sysLanguage::get('AMC_PRICE') ?></td>
									<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
									<td class="main"><?php echo $currencies->format(tep_add_tax($customers['price'],$customers['tax']), true) ; ?></td>
								</tr>
<?php
	}

}	else if($email_sent = "true") {
?>
								<tr>
									<td colspan=3 class="main"><?= sysLanguage::get('AMC_EMAIL_SENT') ?></td>
								</tr>
<?php
}

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
		->draw();
	}else{
		$pageContent->set('pageForm', array(
			'name' => 'checkout_confirmation',
			'action' => $payment->form_action_url,
			'method' => 'post'
		));
		$pageButtons = $payment->process_cancel_button();
	}
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
