	<table cellpadding="2" cellspacing="0" border="0" width="95%">
		<tr>
			<td class="formAreaTitle"><?php echo sysLanguage::get('TEXT_MEMBERSHIP_DETAILS'); ?></td>
		</tr>
<?php
	$CustomersMembership = $Customer->CustomersMembership;
	if ($CustomersMembership->ismember == 'M'){
?>
		<tr>
			<td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
				<tr>
					<td colspan=2 class="main"><?php echo sysLanguage::get('TEXT_PLAN');?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_NAME');?></td>
					<td class="main"><?php
						echo tep_draw_hidden_field('member','Y');
						
						$Qmembership = Doctrine_Query::create()
						->from('Membership m')
						->leftJoin('m.MembershipPlanDescription md')
						->where('md.language_id = ?', Session::get('languages_id'))
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						
						$MembershipBox = htmlBase::newElement('selectbox')
						->setName('planid')
						->selectOptionByValue($CustomersMembership->plan_id);
						foreach($Qmembership as $mInfo){
							$MembershipBox->addOption($mInfo['plan_id'], $mInfo['MembershipPlanDescription'][0]['name']);
						}
						echo $MembershipBox->draw() . tep_draw_hidden_field('prev_plan_id', $CustomersMembership->plan_id);
					?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_MEMBERSHIP_PERIOD');?></td>
					<td class="main"><?php
						if ($CustomersMembership->Membership->membership_months > 0){
							echo $CustomersMembership->Membership->membership_months . ' ' . sysLanguage::get('TEXT_MEM_MONTHS');
						}else{
							echo $CustomersMembership->Membership->membership_days . ' ' . sysLanguage::get('TEXT_MEM_DAYS');
						}
					?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_NUM_TITLE');?></td>
					<td class="main"><?php echo $CustomersMembership->Membership->no_of_titles;?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_PRICE');?></td>
					<td class="main"><?php echo $CustomersMembership->Membership->price;?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_ACTI_STATUS');?></td>
					<td class="main"><?php
						$ActivateBox = htmlBase::newElement('selectbox')
						->setName('activate')
						->selectOptionByValue($CustomersMembership->activate)
						->addOption('Y', 'Active')
						->addOption('N', 'Inactive');
						echo $ActivateBox->draw();
						echo tep_draw_hidden_field('prev_acti_status', $CustomersMembership->activate);
					?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_MEM_DATE');?></td>
					<td class="main"><?php echo $CustomersMembership->membership_date;?></td>
				</tr>
<?php
		if ($CustomersMembership->free_trial_flag == 'Y'){
?>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_FREE_TRIAL');?></td>
					<td class="main"><?php echo $CustomersMembership->Membership->free_trial . ' ' . sysLanguage::get('TEXT_MEM_DAYS');?></td>
				</tr>
<?php
		}

		if ($CustomersMembership->free_trial_ends > date('Y-m-d')){
?>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_FREE_TRIAL_ENDS');?></td>
					<td class="main"><b><?php echo tep_date_long($CustomersMembership->free_trial_ends);?></b></td>
				</tr>
<?php
		}
?>
			</table></td>
		</tr>
		<tr>
			<td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
		</tr>
		<tr>
			<td class="formAreaTitle"><?php echo sysLanguage::get('TEXT_PAYMENT_DETAILS'); ?></td>
		</tr>
		<tr>
			<td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_PAYMENT_METHOD');?></td>
					<td class="main"><?php
						$PaymentMethodBox = htmlBase::newElement('selectbox')
						->setName('payment_method')
						->selectOptionByValue($CustomersMembership->payment_method)
						->attr('onchange', 'fnPaymentChange(this.value);')
						->addOption('authorizenet', 'Authorize.Net')
						->addOption('paypalipn', 'Paypal')
						->addOption('usaepay', 'USAePay')
						->addOption('cc', 'Credit Card')
						->addOption('cashondelivery', 'Cash On Delivery')
						->addOption('moneyorder', 'Money Order');
						
						echo $PaymentMethodBox->draw();
						
						$nextBillDate = date_parse($CustomersMembership->next_bill_date);
						
						$NextBillDayBox = htmlBase::newElement('selectbox')
						->setName('next_billing_day')
						->selectOptionByValue($nextBillDate['day']);
						for($i=1; $i<=31; $i++){
							$NextBillDayBox->addOption(sprintf('%02d', $i), sprintf('%02d', $i));
						}
						
						$NextBillMonthBox = htmlBase::newElement('selectbox')
						->setName('next_billing_month')
						->selectOptionByValue($nextBillDate['month']);
						for ($i=1; $i<13; $i++) {
							$NextBillMonthBox->addOption(sprintf('%02d', $i), strftime('%B',mktime(0,0,0,$i,1,2000)));
						}
						
						$NextBillYearBox = htmlBase::newElement('selectbox')
						->setName('next_billing_year')
						->selectOptionByValue($nextBillDate['year']);
						$today = getdate();
						for ($i=$today['year']; $i < $today['year']+10; $i++) {
							$NextBillYearBox->addOption(
								strftime('%y',mktime(0,0,0,1,1,$i)),
								strftime('%Y',mktime(0,0,0,1,1,$i))
							);
						}
					?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_NEXT_BILL_DATE');?></td>
					<td class="main"><?php
						echo $NextBillDayBox->draw() . '&nbsp;' . 
							$NextBillMonthBox->draw() . '&nbsp;' . 
							$NextBillYearBox->draw();
					?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_CARD_NUM');?></td>
					<td class="main"><?php echo tep_draw_input_field('cc_number',(tep_not_null($CustomersMembership->card_num) ? cc_decrypt($CustomersMembership->card_num) : '')); ?></td>
				</tr>
				<tr id="card_cvv">
					<td class="main"><?php echo sysLanguage::get('TEXT_CVV');?></td>
					<td class="main"><?php echo tep_draw_input_field('cc_cvv',(tep_not_null($CustomersMembership->card_cvv) ? cc_decrypt($CustomersMembership->card_cvv) : ''), 'size="5"'); ?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_EXP_DATE');?></td>
					<td class="main"><?php
						if (tep_not_null($CustomersMembership->exp_date)){
							$exp_date = cc_decrypt($CustomersMembership->exp_date);
							$expMonth = substr($exp_date, 0, 2);
							$expYear = substr($exp_date, -2);
						}
						
						$CCExpiresMonth = clone $NextBillMonthBox;
						$CCExpiresMonth->setName('cc_expires_month');
						$CCExpiresMonth->selectOptionByValue((isset($expMonth) ? $expMonth : ''));
						
						$CCExpiresYear = clone $NextBillYearBox;
						$CCExpiresYear->setName('cc_expires_year');
						$CCExpiresYear->selectOptionByValue((isset($expYear) ? $expYear : ''));
						
						echo $CCExpiresMonth->draw() . '&nbsp;' . $CCExpiresYear->draw();
					?></td>
				</tr>
<?php
  if ($CustomersMembership['payment_method'] == 'paypal_ipn' || $CustomersMembership['payment_method'] == 'cod' || $CustomersMembership['payment_method'] == 'moneyorder' || $CustomersMembership['payment_method'] == 'dotpay'){
      echo "<script language='javascript'>
             document.customers.cc_number.disabled=true;
             document.customers.cc_expires_month.disabled=true;
             document.customers.cc_expires_year.disabled=true;
            </script>";
  }
?>
			</table></td>
		</tr>
<?php
	}else /*if($cInfo->ismember == 'U')*/{
?>
		<tr>
			<td class="formArea"><table width="100%" cellspacing=0 cellpadding=2>
				<tr>
					<td class="main" width="35%"><?php echo sysLanguage::get('TEXT_MEMBERSHIP_STATUS');?></td>
					<td class="main" width="65%"><?php echo sysLanguage::get('TEXT_NOT_MEMBER');?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_MAKE_MEMBER');?></td>
					<td class="main"><?php echo tep_draw_selection_field('make_member','checkbox','1',false,'','onclick=fnClicked();');?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_CHOOSE_PLAN');?></td>
					<td class="main"><?php
						$Qmembership = Doctrine_Query::create()
						->from('Membership m')
						->leftJoin('m.MembershipPlanDescription md')
						->where('md.language_id = ?', Session::get('languages_id'))
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						
						$i = 0;
						$jsMBM = '';
						foreach($Qmembership as $row_plans){
							$plans[$i]['id']=$row_plans['plan_id'];
							$plans[$i]['text']= $row_plans['MembershipPlanDescription'][0]['name'];
							$plans[$i]['days']=$row_plans['membership_days'];
							$i++;
							$jsMBM .= 'plans['.$row_plans['plan_id'].'] = '.$row_plans['membership_days'].";\n";
						}
						$selected_id = $plans[0]['id'];
						echo tep_draw_pull_down_menu('planid',$plans,$selected_id,' disabled onchange=fnClicked();');
					?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_MAKE_THIS_CANDIDATE');?></td>
					<td class="main"><?php
						$ActivateBox = htmlBase::newElement('selectbox')
						->setName('activate')
						->attr('disabled', 'disabled')
						->selectOptionByValue($CustomersMembership->activate)
						->addOption('Y', 'Active')
						->addOption('N', 'Inactive');
						echo $ActivateBox->draw();
					?></td>
				</tr>
			</table></td>
		</tr>
		<tr>
			<td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
		</tr>
		<tr>
			<td class="formAreaTitle"><?php echo sysLanguage::get('TEXT_PAYMENT_DETAILS'); ?></td>
		</tr>
<?php
	$nextBillDate = date_parse($CustomersMembership->next_bill_date);
						
	$NextBillDayBox = htmlBase::newElement('selectbox')
	->setName('next_billing_day')
	->selectOptionByValue($nextBillDate['day'])
	->disable();
	for($i=1; $i<=31; $i++){
		$NextBillDayBox->addOption(sprintf('%02d', $i), sprintf('%02d', $i));
	}
						
	$NextBillMonthBox = htmlBase::newElement('selectbox')
	->setName('next_billing_month')
	->selectOptionByValue($nextBillDate['month'])
	->disable();
	for ($i=1; $i<13; $i++) {
		$NextBillMonthBox->addOption(sprintf('%02d', $i), strftime('%B',mktime(0,0,0,$i,1,2000)));
	}
						
	$NextBillYearBox = htmlBase::newElement('selectbox')
	->setName('next_billing_year')
	->selectOptionByValue($nextBillDate['year'])
	->disable();
	$today = getdate();
	for ($i=$today['year']; $i < $today['year']+10; $i++) {
		$NextBillYearBox->addOption(
			strftime('%y',mktime(0,0,0,1,1,$i)),
			strftime('%Y',mktime(0,0,0,1,1,$i))
		);
	}

	$AuthNetExpiresMonth = clone $NextBillMonthBox;
	$AuthNetExpiresMonth->setName('authorizenet_cc_expires_month')->disable();
						
	$AuthNetExpiresYear = clone $NextBillYearBox;
	$AuthNetExpiresYear->setName('authorizenet_cc_expires_year')->disable();

	$UsaEpayExpiresMonth = clone $NextBillMonthBox;
	$UsaEpayExpiresMonth->setName('usaepay_cc_expires_month')->disable();
						
	$UsaEpayExpiresYear = clone $NextBillYearBox;
	$UsaEpayExpiresYear->setName('usaepay_cc_expires_year')->disable();

	$CCExpiresMonth = clone $NextBillMonthBox;
	$CCExpiresMonth->setName('cc_expires_month')->disable();
						
	$CCExpiresYear = clone $NextBillYearBox;
	$CCExpiresYear->setName('cc_expires_year')->disable();
?>
		<tr>
			<td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
				<tr>
					<td class="main"><?php echo '<b>'.sysLanguage::get('TEXT_PAYMENT_METHOD').'</b>';?></td>
					<td class="main">&nbsp;</td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_NEXT_BILL_DATE');?></td>
					<td class="main"><?php
						echo $NextBillDayBox->draw() . '&nbsp;' . 
							$NextBillMonthBox->draw() . '&nbsp;' . 
							$NextBillYearBox->draw();
					?></td>
				</tr>
				<tr>
					<td class="main"><?php echo tep_draw_radio_field('payment_method','paypal_ipn',true,'',' disabled ');?></td>
					<td class="main"><?php echo sysLanguage::get('TEXT_PAYPAL');?></td>
				</tr>
				<tr>
					<td class="main"><?php echo tep_draw_radio_field('payment_method','authorizenet_aim',true,'',' disabled ');?></td>
					<td class="main"><?php echo sysLanguage::get('TEXT_AUTHORIZE');?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_CARD_NUM');?></td>
					<td class="main"><?php echo tep_draw_input_field('authorizenet_cc_number','',' disabled ');?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_EXP_DATE');?></td>
					<td class="main"><?php echo $AuthNetExpiresMonth->draw() . '&nbsp;' . $AuthNetExpiresYear->draw();?></td>
				</tr>
				<tr>
					<td class="main"><?php echo tep_draw_radio_field('payment_method','usaepay',true,'',' disabled ');?></td>
					<td class="main"><?php echo sysLanguage::get('TEXT_USAEPAY');?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_CARD_NUM');?></td>
					<td class="main"><?php echo tep_draw_input_field('usaepay_cc_number','',' disabled ');?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_EXP_DATE');?></td>
					<td class="main"><?php echo $UsaEpayExpiresMonth->draw() . '&nbsp;' . $UsaEpayExpiresYear->draw();?></td>
				</tr>
				<tr>
					<td class="main"><?php echo tep_draw_radio_field('payment_method','cc',true,'',' disabled ');?></td>
					<td class="main"><?php echo sysLanguage::get('TEXT_CREDIT_CARD');?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_CARD_NUM');?></td>
					<td class="main"><?php echo tep_draw_input_field('cc_number','',' disabled ');?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('TEXT_EXP_DATE');?></td>
					<td class="main"><?php echo $CCExpiresMonth->draw() . '&nbsp;' . $CCExpiresYear->draw();?></td>
				</tr>
			</table></td>
		</tr>
  <?php
  	}
  ?>
	</table>