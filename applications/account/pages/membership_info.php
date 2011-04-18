<?php
	$membership = $userAccount->plugins['membership'];
	$membership->loadMembershipInfo();
	ob_start();
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td class="main"><b><?php echo sysLanguage::get('TABLE_HEADING_MEMBER_BILLING_INFO'); ?></b></td>
		<td class="inputRequirement" align="right"><?php echo sysLanguage::get('FORM_REQUIRED_INFORMATION'); ?></td>
	</tr>
</table>

	<table border="0" cellspacing="2" cellpadding="2" style="margin:.5em;">
		<tr>
			<td class="main"><?php echo sysLanguage::get('TEXT_INFO_PACKAGE_NAME');?></td>
			<td class="main"><?php echo $membership->planInfo['package_name']; ?></td>
		</tr>
		<tr>
			<td class="main"><?php echo sysLanguage::get('TEXT_INFO_MEMBERSHIP_PERIOD');?></td>
			<td class="main"><?php echo  ($membership->planInfo['membership_months'] > 0 ? $membership->planInfo['membership_months'] . ' Month(s) ':'').($membership->planInfo['membership_days']>0?$membership->planInfo['membership_days'].' days':''); ?></td>
		</tr>
		<tr>
			<td class="main"><?php echo sysLanguage::get('TEXT_INFO_NUMBER_OF_TITLES');?></td>
			<td class="main"><?php echo $membership->planInfo['no_of_titles']; ?></td>
		</tr>
		<tr>
			<td class="main"><?php echo sysLanguage::get('TEXT_INFO_PACKAGE_PRICE');?></td>
			<td class="main"><?php echo $currencies->format($membership->planInfo['price']) ; ?></td>
		</tr>
		<tr>
			<td class="main"><?php echo sysLanguage::get('TEXT_INFO_ACTIVATION_STATUS');?></td>
			<td class="main"><?php
				if ($membership->membershipInfo['activate']=='Y'){
					echo sysLanguage::get('TEXT_ACTIVE');
				}else{
					echo sysLanguage::get('TEXT_INACTIVE');
				}
			?></td>
		</tr>
		<tr>
			<td class="main"><?php echo sysLanguage::get('TEXT_INFO_MEMBER_SINCE');?></td>
			<td class="main"><?php echo strftime(sysLanguage::getDateFormat('long'), strtotime($membership->membershipInfo['membership_date'])); ?></td>
		</tr>
		<tr>
			<td class="main"><?php echo sysLanguage::get('TEXT_INFO_PAYMENT_METHOD');?></td>
			<td class="main"><?php echo $membership->membershipInfo['payment_method']; ?></td>
		</tr>
		<tr>
			<td class="main"><?php echo sysLanguage::get('TEXT_INFO_NEXT_BILLING_DATE');?></td>
			<td class="main"><?php echo strftime(sysLanguage::getDateFormat('long'), $membership->membershipInfo['next_bill_date']); ?></td>
		</tr>
<?php
	$contents = EventManager::notifyWithReturn('AccountMembershipInfoAddToTable', $membership);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
?>
	</table>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE');
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null, 'account', 'default', 'SSL'))
	->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
