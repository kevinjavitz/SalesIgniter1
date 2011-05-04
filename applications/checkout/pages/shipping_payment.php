<input type="hidden" name="currentPage" id="currentPage" value="payment_shipping">

<div class="ui-widget ui-widget-content ui-corner-all">
<?php if (!$onePageCheckout->isMembershipCheckout()){

	ob_start();
	require(sysConfig::getDirFsCatalog() . 'applications/checkout/pages/cart.php');
	$pageHtml = ob_get_contents();
	ob_end_clean();
	echo $pageHtml;
}else{
	?>
<div id="rentalMembership"><?php
	 /*This part needs revised*/
    $notEnabledMemberships = array();
	if(Session::exists('add_to_queue_product_id')){
		$add_queue_product_id = Session::get('add_to_queue_product_id');
		$QProduct = Doctrine_Query::create()
		->select('membership_enabled')
		->from('Products')
		->where('products_id=?',$add_queue_product_id)
		->fetchOne();
		$notEnabledMemberships = explode(';',$QProduct->membership_enabled);
	}

	$Qsum = dataAccess::setQuery('select sum(membership_months) as months_sum, sum(membership_days) as days_sum from {members}');
	$Qsum->setTable('{members}', TABLE_MEMBER);
	$Qsum->runQuery();

	$months = $Qsum->getVal('months_sum');
	$days = $Qsum->getVal('days_sum');

	$sep = tep_draw_separator('pixel_trans.gif', '100%', '10');

	$productTable = htmlBase::newElement('table')->css('width', '100%')->setCellPadding(3)->setCellSpacing(0);

	$tableColumns = array();
	$tableColumns[] = array('addCls' => 'main', 'text' => '&nbsp;');
	$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_PACKAGE_NAME') . '</b>', 'align' => 'left');
	if ($months > 0){
		$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_MEMBERSHIP_MONTHS') . '</b>', 'align' => 'center');
	}
	if ($days > 0){
		$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_MEMBERSHIP_DAYS') . '</b>', 'align' => 'center');
	}
	$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_N_OF_TITLES') . '</b>', 'align' => 'center');
	if (sysConfig::get('RENTAL_SHOW_TAX_COLUMN') == 'true'){
		$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_TAX') . '</b>', 'align' => 'center');
	}
	$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_PRICE') . '</b>', 'align' => 'center');
	if (sysConfig::get('RENTAL_SHOW_TOTAL_PRICE_COLUMN') == 'true'){
		$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_PRICE_WITH_TAX') . '</b>', 'align' => 'center');
	}
	if (sysConfig::get('RENTAL_SHOW_TRIAL_COLUMN') == 'true'){
		$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_FREE_TRIAL_DAYS') . '</b>', 'align' => 'center');
	}

	$productTable->addBodyRow(array(
		'columns' => $tableColumns
	));

	$Qcheck = dataAccess::setQuery('select plan_id from {membership} where default_plan = "1"')
	->setTable('{membership}', TABLE_MEMBER)
	->runQuery();
	$hasDefault = false;
	if ($Qcheck->numberOfRows() > 0){
		$hasDefault = true;
		$default = $Qcheck->getVal('plan_id');
	}
	$Qplan = dataAccess::setQuery('select tm.*,tmd.name as package_name, tt.tax_rate as tax from {membership} tm left join {membershipdescription} tmd on tmd.plan_id=tm.plan_id left join {tax_rates} tt on tt.tax_rates_id = tm.rent_tax_class_id where tmd.language_id = '.Session::get('languages_id').' order by tm.sort_order asc')
	->setTable('{membership}', TABLE_MEMBER)
	->setTable('{membershipdescription}', 'membership_plan_description')
	->setTable('{tax_rates}', TABLE_TAX_RATES);
	$i=1;
	while($Qplan->next() !== false) {
		if(in_array($Qplan->getVal('plan_id'), $notEnabledMemberships)) continue;
		if (($hasDefault === false && $i == 1) || ($hasDefault === true && $Qplan->getVal('plan_id') == $default)) {
			$chk = true;
		} else {
			$chk = false;
		}

		$tableColumns = array();
		$tableColumns[] = array('addCls' => 'main', 'text' => tep_draw_radio_field('plan_id', $Qplan->getVal('plan_id'), $chk, 'class="rentalPlans"'), 'align' => 'center');
		$tableColumns[] = array('addCls' => 'main', 'text' => $Qplan->getVal('package_name'));
		if ($months > 0){
			$tableColumns[] = array('addCls' => 'main', 'text' => $Qplan->getVal('membership_months'), 'align' => 'center');
		}
		if ($days > 0){
			$tableColumns[] = array('addCls' => 'main', 'text' => $Qplan->getVal('membership_days'), 'align' => 'center');
		}
		$tableColumns[] = array('addCls' => 'main', 'text' => $Qplan->getVal('no_of_titles'), 'align' => 'center');
		if (sysConfig::get('RENTAL_SHOW_TAX_COLUMN') == 'true'){
			$tableColumns[] = array('addCls' => 'main', 'text' => tep_display_tax_value($Qplan->getVal('tax'), 0) . '%', 'align' => 'center');
		}
		$tableColumns[] = array('addCls' => 'main', 'text' => $currencies->format($Qplan->getVal('price'), true, $order->info['currency'], $order->info['currency_value']), 'align' => 'center');
		if (sysConfig::get('RENTAL_SHOW_TOTAL_PRICE_COLUMN') == 'true'){
			$tableColumns[] = array('addCls' => 'main', 'text' => $currencies->format(tep_add_tax($Qplan->getVal('price'), $Qplan->getVal('tax')), true, $order->info['currency'], $order->info['currency_value']), 'align' => 'center');
		}
		if (sysConfig::get('RENTAL_SHOW_TRIAL_COLUMN') == 'true'){
			$tableColumns[] = array('addCls' => 'main', 'text' => $Qplan->getVal('free_trial'), 'align' => 'center');
		}

		$productTable->addBodyRow(array(
			'columns' => $tableColumns
		));
		$i++;
	}
	echo $productTable->draw();
?></div>
<?php
}
	?>
	<div align="right"><table class="orderTotalsList" cellpadding="2" cellspacing="0" border="0" style="margin:.3em;"><?php
		OrderTotalModules::process();
		echo OrderTotalModules::output();
	?></table></div>
</div>
<br>
<?php
	$Module = OrderTotalModules::getModule('coupon');
	if ($Module !== false && $Module->isEnabled() === true){
		echo '<table cellpadding="2" cellspacing="0" border="0">' . 
			'<tr>' . 
				'<td class="main"><b>'.sysLanguage::get('HAVE_A_COUPON').'</b></td>' .
			'</tr>' . 
			'<tr>' . 
				'<td class="main">' . tep_draw_input_field('redeem_code', sysLanguage::get('REDEEM_CODE')) . '</td>' .
				'<td class="main">' .
					htmlBase::newElement('div')
					->attr('id','voucherRedeem')
					->html(sysLanguage::get('REDEEM'))
					->setName('voucherRedeem')
					->draw() .
				'</td>' .
			'</tr>' . 
		'</table><br>';
	}

	$contents = EventManager::notifyWithReturn('CheckoutAddBlockAfterCart');
	if (!empty($contents)){
		foreach($contents as $content){
			echo '<tr><td>' .
				$content .
			'</td></tr>';
			echo '<tr><td>' . tep_draw_separator('pixel_trans.gif', '100%', '10') . '</td></tr>';
		}
	}
	
	$totalModules = OrderPaymentModules::countEnabled();
?>
<div class="ui-widget">
	<div class="ui-widget-content ui-corner-all">
		<div class="ui-widget-header ui-corner-all">
			<span class="ui-widget-header-text">&nbsp;<?php
				if ($totalModules > 1){
					echo sysLanguage::get('TEXT_SELECT_PAYMENT_METHOD');
				}else{
					echo sysLanguage::get('TEXT_ENTER_PAYMENT_INFORMATION');
				}
			?></span>
		</div>
		<div class="ui-widget-text"><?php
			foreach(OrderPaymentModules::getModules() as $Module){
				$mInfo = $Module->onSelect();
				
				$code = $Module->getCode();
				$title = $Module->getTitle();
				$fields = null;
				if (isset($mInfo['fields'])){
					$fields = $mInfo['fields'];
				}
				
				$addClass = ' ui-state-default';
				if (isset($onePageCheckout->onePage['info']['payment']['id']) && $code == $onePageCheckout->onePage['info']['payment']['id']){
					$addClass = ' ui-state-active';
				}
				
				echo '<div class="moduleRow paymentRow' . $addClass . '">';
				echo '<div class="smallText">';
				
				if ($totalModules > 0){
					echo tep_draw_radio_field('payment_method', $code, (isset($onePageCheckout->onePage['info']['payment']['id']) && $mInfo['id'] == $onePageCheckout->onePage['info']['payment']['id']));
				}else{
					echo tep_draw_hidden_field('payment_method', $code);
				}
				
				echo '<b>' . $title . '</b></div>';
				
				if (isset($mInfo['error'])){
					echo '<div class="smallText">' . $mInfo['error'] . '</div>';
				}elseif (is_null($fields) === false){
					echo '<div class="paymentFields" style="display:none;margin:.3em;margin-left:2em;"><table cellpadding="3" cellspacing="0" border="0">';
					foreach($fields as $fInfo){
						echo '<tr>' . 
							'<td class="main">' . $fInfo['title'] . '</td>' .
							'<td class="main">' . $fInfo['field'] . '</td>' .
						'</tr>';
					}
					echo '</table></div>';
				}
				
				echo '</div>';
			}
			
			// Start - CREDIT CLASS Gift Voucher Contribution
			if ($userAccount->isLoggedIn() && OrderTotalModules::isEnabled('giftvoucher')) {
				$gvModule = OrderTotalModules::getModule('giftvoucher');
				if ($gvModule !== false && $gvModule->user_has_gv_account($userAccount->getCustomerId())){
					echo $gvModule->sub_credit_selection();
				}
			}
			// End - CREDIT CLASS Gift Voucher Contribution
		?></div>
	</div>
</div>
<br>
<?php
	if ($onePageCheckout->onePage['shippingEnabled'] === true){
		$showStoreMethods = true;
		$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsBeforeList', &$showStoreMethods);
		if (!empty($contents)){
			foreach($contents as $content){
				echo $content;
			}
		}

		if ($showStoreMethods === true && OrderShippingModules::hasModules() === true && !$onePageCheckout->isMembershipCheckout()){
			$quotes = OrderShippingModules::quote();
			if (!isset($onePageCheckout->onePage['info']['shipping']['id']) || (isset($onePageCheckout->onePage['info']['shipping']) && $onePageCheckout->onePage['info']['shipping'] == false && sizeof($quotes) > 1)){
				$onePageCheckout->onePage['info']['shipping'] = OrderShippingModules::getCheapestMethod();
			}
?>
<div class="ui-widget">
	<div class="ui-widget-content ui-corner-all">
		<div class="ui-widget-header ui-corner-all">
			<span class="ui-widget-header-text">&nbsp;<?php
				if (sizeof($quotes) > 1 && sizeof($quotes[0]) > 1){
					echo sysLanguage::get('TEXT_CHOOSE_SHIPPING_METHOD');
				}else{
					echo sysLanguage::get('TEXT_ENTER_SHIPPING_INFORMATION');
				}
			?></span>
		</div>
		<div class="ui-widget-text"><?php
			$radio_buttons = 0;
			for ($i=0, $n=sizeof($quotes); $i<$n; $i++) {
				if ($quotes[$i]['id'] == 'reservation'){ //should be zonereservation
					continue;
				}

				$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsAfterQuote', &$quotes[$i]);
				if (!empty($contents)){
					foreach($contents as $content){
						echo $content;
					}
				}
				
				echo '<div class="smallText">';
				echo '<b>' . $quotes[$i]['module'] . '</b>';
				if (isset($quotes[$i]['icon']) && tep_not_null($quotes[$i]['icon'])){
					echo $quotes[$i]['icon'];
				}
				echo '</div>';
				
				if (isset($quotes[$i]['error'])){
					echo '<div>' . $quotes[$i]['error'] . '</div>';
				}else{
					echo '<table cellpadding="3" cellspacing="0" border="0" width="100%">';
					for($j=0, $n2=sizeof($quotes[$i]['methods']); $j<$n2; $j++){
						$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsAfterQuoteMethod', &$quotes[$i]['methods'][$j]);
						$show_method = true;
						if (!empty($contents)){
							foreach($contents as $content){
								if ($content === false){
									$show_method = false;
									break;
								}
							}
						}
						if ($show_method === false)   continue;

						$checked = (isset($onePageCheckout->onePage['info']['shipping']['id']) && $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $onePageCheckout->onePage['info']['shipping']['id'] ? true : false);

						$addClass = ' ';
						if (isset($onePageCheckout->onePage['info']['shipping']['id']) && $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $onePageCheckout->onePage['info']['shipping']['id']){
							$addClass = ' ui-state-active';
						}

						$methodTitle = $quotes[$i]['methods'][$j]['title'];
						$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsAfterTitle', &$quotes[$i]['methods'][$j]);
						if (!empty($contents)){
							foreach($contents as $content){
								$methodTitle .= $content;
							}
						}

						if ( ($n > 1) || ($n2 > 1) ) {
							$methodSelector = tep_draw_radio_field('shipping_method', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'], $checked);
						}else{
							$methodSelector = tep_draw_hidden_field('shipping_method', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id']);
						}
						
						echo '<tr class="moduleRow shippingRow' . $addClass . '">
							<td class="smallText">' . $methodSelector . $methodTitle . '</td>
							<td class="smallText" align="right">' . $currencies->format(tep_add_tax($quotes[$i]['methods'][$j]['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))) . '</td>
						</tr>';
					}
					echo '</table>';
				}
			}

			$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsAfterList');
			if (!empty($contents)){
				foreach($contents as $content){
					echo $content;
				}
			}
		?></div>
	</div>
</div>
<?php
		}
	}
?>
<br>
<?php
$contents = EventManager::notifyWithReturn('CheckoutAddBlock');
if (!empty($contents)){
	foreach($contents as $content){
		echo '<div>' . 
			$content . 
		'</div>';
		echo '<br>';
	}
}
?>
<div class="ui-widget">
	<div class="ui-widget-content ui-corner-all">
		<div class="ui-widget-header ui-corner-all">
			<span class="ui-widget-header-text"><?php echo sysLanguage::get('TEXT_COMMENTS');?></span>
		</div>
		<div class="ui-widget-text">
			<?php echo tep_draw_textarea_field('comments', 'soft', '30', '5', (isset($onePageCheckout->onePage['info']['comments']) ? $onePageCheckout->onePage['info']['comments'] : ''), 'class="ui-widget-content" style="width:98%;"');?>
			<div class="ui-helper-clearfix"></div>
		</div>
	</div>
</div>
<br>
<div align="right"><table class="orderTotalsList" cellpadding="2" cellspacing="0" border="0" style="margin:.5em;"><?php
	echo OrderTotalModules::output();
?></table></div>