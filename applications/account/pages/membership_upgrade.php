<?php
$membership = $userAccount->plugins['membership'];

 $currentPlan = $membership->getPlanId();
 ob_start();
?>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="main"><b><?php echo sysLanguage::get('TEXT_MEMBERSHIP_CURRENT'); ?></b></td>
			</tr>
			<tr>
				<td>
					<table border="0" cellspacing="2" cellpadding="2" style="width:300px;">
						<tr>
							<td class="main"><?php echo sysLanguage::get('TEXT_MEMBERSHIP_PACKAGE_NAME'); ?>:</td>
							<td class="main"><?php echo $membership->planInfo['package_name']; ?></td>
						</tr>
						<tr>
							<td class="main"><?php echo sysLanguage::get('TEXT_MEMBERSHIP_PERIOD'); ?>:</td>
							<td class="main"><?php
								if ($membership->planInfo['membership_months'] > 0){
									echo $membership->planInfo['membership_months'] . ' Month(s) ';
								}
								
								if ($membership->planInfo['membership_days'] > 0){
									echo $membership->planInfo['membership_days'] . ' Day(s) ';
								}
							?></td>
						</tr>
						<tr>
							<td class="main"><?php echo sysLanguage::get('TEXT_MEMBERSHIP_NUMBER_OF_RENTALS'); ?>:</td>
							<td class="main"><?php echo $membership->planInfo['no_of_titles']; ?></td>
						</tr>
						<tr>
							<td class="main"><?php echo sysLanguage::get('TEXT_MEMBERSHIP_PRICE'); ?>:</td>
							<td class="main"><?php echo $currencies->format($membership->planInfo['price']) ; ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="main"><b><?php echo sysLanguage::get('TEXT_MEMBERSHIP_CHANGE'); ?></b></td>
			</tr>
			<tr>
				<td>
					<div class="ui=widget ui-widget-content">
						<table border="0" cellspacing="2" cellpadding="2" style="width:100%;">
							<thead>
								<tr>
									<th align="left">&nbsp;</th>
									<th align="left"><?php echo sysLanguage::get('TEXT_MEMBERSHIP_PACKAGE_NAME'); ?></th>
									<th align="left"><?php echo sysLanguage::get('TEXT_MEMBERSHIP_PERIOD'); ?></th>
									<th align="left"><?php echo sysLanguage::get('TEXT_MEMBERSHIP_NUMBER_OF_RENTALS'); ?></th>
									<th align="left"><?php echo sysLanguage::get('TEXT_MEMBERSHIP_PRICE'); ?></th>
								</tr>
							</thead>
							<tbody><?php
								$Qplans = Doctrine_Query::create()
								->from('Membership m')
								->leftJoin('m.MembershipPlanDescription md')
								->where('md.language_id =?', Session::get('languages_id'))
								->orderBy('sort_order')
								->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
								foreach($Qplans as $pInfo){
									$radioButton = tep_draw_radio_field('plan_id', $pInfo['plan_id']);
									$style = '';
									if ($pInfo['plan_id'] == $currentPlan){
										$style = ' style="background:#ffffd7"';
										$radioButton = '<span style="color:red">' . sysLanguage::get('TEXT_CURRENT_MEMBERSHIP') . '</span>';
									}
									
									echo '<tr' . $style . '>' . 
										'<td>' . $radioButton . '</td>' . 
										'<td>' . $pInfo['MembershipPlanDescription'][0]['name'] . '</td>' . 
										'<td>' . 
											($pInfo['membership_months'] > 0 ? $pInfo['membership_months'] . ' Month(s)' : '') . 
											($pInfo['membership_days'] > 0 ? $pInfo['membership_days'] . ' Days' : '') .
										'</td>' . 
										'<td>' . $pInfo['no_of_titles'] . '</td>' . 
										'<td>' . $currencies->format($pInfo['price']) . '</td>' . 
									'</tr>';
								}
							?></tbody>
						</table>
					</div>
				</td>
			</tr>
		</table>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_MEMBERSHIP_UPGRADE');
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('save')
	->setText(sysLanguage::get('TEXT_BUTTON_UPGRADE'))
	->setType('submit')
	->setName('upgrade')
	->draw();
	
	$pageButtons .= htmlBase::newElement('button')
	->usePreset('cancel')
	->setHref(itw_app_link(null, 'account', 'default'))
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'membership_upgrade',
		'action' => itw_app_link('action=upgradeMembership', 'account', 'membership_upgrade', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
