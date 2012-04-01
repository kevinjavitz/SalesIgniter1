<input type="hidden" name="currentPage" id="currentPage" value="payment_shipping">

<div>
	<?php if ($onePageCheckout->isNormalCheckout()){

	ob_start();
	require(sysConfig::getDirFsCatalog() . 'applications/mobile/pagesTabs/checkout/cart.php');
	$pageHtml = ob_get_contents();
	ob_end_clean();
	echo $pageHtml;
}else if ($onePageCheckout->isMembershipCheckout()){
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

		$Sum = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select sum(membership_months) as months_sum, sum(membership_days) as days_sum from ' . TABLE_MEMBER);

		$months = $Sum[0]['months_sum'];
		$days = $Sum[0]['days_sum'];

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

		$Check = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select plan_id from ' . TABLE_MEMBER . ' where default_plan = "1"');
		$hasDefault = false;
		if (sizeof($Check) > 0){
			$hasDefault = true;
			$default = $Check[0]['plan_id'];
		}

		$Plan = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select tm.*,tmd.name as package_name, tt.tax_rate as tax from ' . TABLE_MEMBER . ' tm left join membership_plan_description tmd on tmd.plan_id=tm.plan_id left join ' . TABLE_TAX_RATES . ' tt on tt.tax_rates_id = tm.rent_tax_class_id where tmd.language_id = '.Session::get('languages_id').' order by tm.sort_order asc');
		$i=1;
		foreach($Plan as $pInfo) {
			$planId = $pInfo['plan_id'];
			if(in_array($planId, $notEnabledMemberships)) continue;
			if (($hasDefault === false && $i == 1) || ($hasDefault === true && $planId == $default)) {
				$chk = true;
			} else {
				$chk = false;
			}

			$tableColumns = array();
			$tableColumns[] = array('addCls' => 'main', 'text' => tep_draw_radio_field('plan_id', $planId, $chk, 'class="rentalPlans"'), 'align' => 'center');
			$tableColumns[] = array('addCls' => 'main', 'text' => $pInfo['package_name']);
			if ($months > 0){
				$tableColumns[] = array('addCls' => 'main', 'text' => $pInfo['membership_months'], 'align' => 'center');
			}
			if ($days > 0){
				$tableColumns[] = array('addCls' => 'main', 'text' => $pInfo['membership_days'], 'align' => 'center');
			}
			$tableColumns[] = array('addCls' => 'main', 'text' => $pInfo['no_of_titles'], 'align' => 'center');
			if (sysConfig::get('RENTAL_SHOW_TAX_COLUMN') == 'true'){
				$tableColumns[] = array('addCls' => 'main', 'text' => tep_display_tax_value($pInfo['tax'], 0) . '%', 'align' => 'center');
			}
			$tableColumns[] = array('addCls' => 'main', 'text' => $currencies->format($pInfo['price'], true, $order->info['currency'], $order->info['currency_value']), 'align' => 'center');
			if (sysConfig::get('RENTAL_SHOW_TOTAL_PRICE_COLUMN') == 'true'){
				$tableColumns[] = array('addCls' => 'main', 'text' => $currencies->format(tep_add_tax($pInfo['price'], $pInfo['tax']), true, $order->info['currency'], $order->info['currency_value']), 'align' => 'center');
			}
			if (sysConfig::get('RENTAL_SHOW_TRIAL_COLUMN') == 'true'){
				$tableColumns[] = array('addCls' => 'main', 'text' => $pInfo['free_trial'], 'align' => 'center');
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
	$contents = EventManager::notifyWithReturn('CheckoutAddBlockBeforeOrderTotalsTop');
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
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
	$InputField = htmlBase::newElement('input')
		->setName('redeem_code')
		->setId('redeem_code')
		->attr('placeholder', sysLanguage::get('REDEEM_CODE'));

	$Button = htmlBase::newElement('button')
		->attr('id','voucherRedeem')
		->setText(sysLanguage::get('REDEEM'))
		->setName('voucherRedeem');

	echo '<table cellpadding="2" cellspacing="0" border="0">' .
		'<tr>' .
		'<td class="main"><b>'.sysLanguage::get('HAVE_A_COUPON').'</b></td>' .
		'</tr>' .
		'<tr>' .
		'<td class="main">' . $InputField->draw() . '</td>' .
		'<td class="main">' . $Button->draw() . '</td>' .
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
<fieldset data-role="controlgroup">
	<legend><?php
		if ($totalModules > 1){
			echo sysLanguage::get('TEXT_SELECT_PAYMENT_METHOD');
		}else{
			echo sysLanguage::get('TEXT_ENTER_PAYMENT_INFORMATION');
		}
		?></legend>
	<?php
	$cnt = 0;
	foreach(OrderPaymentModules::getModules() as $Module){
		$mInfo = $Module->onSelect();

		$code = $Module->getCode();
		$title = $Module->getTitle();
		$fields = null;
		if (isset($mInfo['fields'])){
			$fields = $mInfo['fields'];
		}

		$selected = false;
		if (isset($onePageCheckout->onePage['info']['payment']['id']) && $code == $onePageCheckout->onePage['info']['payment']['id']){
			$selected = true;
		}elseif ($cnt == 0){
			$selected = true;
		}

		if ($selected === true){
			$onePageCheckout->setPaymentMethod($code);
		}

		echo htmlBase::newElement('radio')
			->setId('payment_method_' . $code)
			->setName('payment_method')
			->setValue($code)
			->setChecked($selected)
			->setLabel($title)
			->setLabelPosition('after')
			->draw();

		if (isset($mInfo['error'])){
			echo '<div class="smallText">' . $mInfo['error'] . '</div>';
		}elseif (is_null($fields) === false){
			$display = 'none';
			if ($selected === true){
				$display = 'block';
			}
			echo '<div class="paymentFields ' . $code . ' ui-body-c" style="display:' . $display . '">';
			foreach($fields as $fInfo){
				if (stristr($fInfo['field'], 'cardExpMonth')){
					echo '<div style="margin:.3em;">' .
						'<fieldset data-role="controlgroup" data-type="horizontal">' .
							'<legend>' . $fInfo['title'] . '</legend>' .
							$fInfo['field'] .
						'</fieldset>' .
						'</div>';
				}else{
					echo '<div style="margin:.3em;">' .
						'<div style="margin:.3em;">' . $fInfo['title'] . '</div>' .
						'<div style="margin:.3em;">' . $fInfo['field'] . '</div>' .
						'</div>';
				}
			}
			echo '</div>';
		}
		$cnt++;
	}

	// Start - CREDIT CLASS Gift Voucher Contribution
	if ($userAccount->isLoggedIn() && OrderTotalModules::isEnabled('giftvoucher')) {
		$gvModule = OrderTotalModules::getModule('giftvoucher');
		if ($gvModule !== false && $gvModule->user_has_gv_account($userAccount->getCustomerId())){
			echo $gvModule->sub_credit_selection();
		}
	}
	// End - CREDIT CLASS Gift Voucher Contribution
?>
</fieldset>

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
<fieldset data-role="controlgroup">
	<legend><?php
		if (sizeof($quotes) > 1 && sizeof($quotes[0]) > 1){
			echo sysLanguage::get('TEXT_CHOOSE_SHIPPING_METHOD');
		}else{
			echo sysLanguage::get('TEXT_ENTER_SHIPPING_INFORMATION');
		}
		?></legend>
	<?php
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

		echo '<div class="ui-bar ui-bar-b">';
		echo $quotes[$i]['module'];
		if (isset($quotes[$i]['icon']) && tep_not_null($quotes[$i]['icon'])){
			echo $quotes[$i]['icon'];
		}
		echo '</div>';

		if (isset($quotes[$i]['error'])){
			echo '<div class="ui-body-c">' . $quotes[$i]['error'] . '</div>';
		}else{
			echo '<div class="shippingQuotes ' . $quotes[$i]['id'] . ' ui-body-c" style="padding:.5em;">';
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

				$selected = (isset($onePageCheckout->onePage['info']['shipping']['id']) && $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $onePageCheckout->onePage['info']['shipping']['id'] ? true : false);
				if ($n2 == 1){
					$selected = true;
				}

				$methodTitle = $quotes[$i]['methods'][$j]['title'];
				$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsAfterTitle', &$quotes[$i]['methods'][$j]);
				if (!empty($contents)){
					foreach($contents as $content){
						$methodTitle .= $content;
					}
				}

				$QuoteTotal = '<div style="float:right;">' .
					$currencies->format(tep_add_tax($quotes[$i]['methods'][$j]['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))) .
					'</div>';

				echo htmlBase::newElement('radio')
					->setId('shipping_method_' . $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'])
					->setName('shipping_method')
					->setValue($quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'])
					->setChecked($selected)
					->setLabel($methodTitle . $QuoteTotal)
					->setLabelPosition('after')
					->draw();
			}
			echo '</div>';
		}
	}

	$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsAfterList');
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
	?>
</fieldset>
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
if (sysConfig::get('SHOW_COMMENTS_CHECKOUT') == 'true'){
	echo '<div class="ui-bar ui-bar-b">' . sysLanguage::get('TEXT_COMMENTS') . '</div>';
	echo '<div class="ui-body-c" style="padding:.5em;">';
	echo htmlBase::newElement('textarea')
		->setId('comments')
		->setName('comments')
		->html((isset($onePageCheckout->onePage['info']['comments']) ? $onePageCheckout->onePage['info']['comments'] : ''))
		->draw();
	echo '</div>';
}
?>
<br>
<div align="right"><table class="orderTotalsList" cellpadding="2" cellspacing="0" border="0" style="margin:.5em;"><?php
	echo OrderTotalModules::output();
	?></table></div>