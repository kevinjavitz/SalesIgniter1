<?php
ob_start();
?>
<script src="<?php echo sysConfig::getDirWsCatalog() . 'applications/mobile/javascript/checkout.js';?>"></script>
<style>
	.pstrength-minchar {
		font-size : 10px;
	}
</style>
<script type="text/javascript">
	var CONTINUE_TO_HOMEPAGE = '<?php echo sysLanguage::get('TEXT_CONTINUE_TO_HOMEPAGE')?>';
	var kioskMode = <?php echo (Session::exists('kiosk_active') ? 'true' : 'false');?>;
</script>
<div class="checkoutContent">
	<?php
	if (Session::exists('kiosk_active')){
		$AddressBook = $userAccount->plugins['addressBook'];
		$onePageCheckout->onePage['createAccount'] = false;

		$useAddress = $AddressBook->getAddress($AddressBook->getDefaultAddressId());
		$userAccount->plugins['addressBook']->addAddressEntry('customer', $useAddress);
		$userAccount->plugins['addressBook']->addAddressEntry('billing', $useAddress);
		$userAccount->plugins['addressBook']->addAddressEntry('delivery', $useAddress);
		ob_start();
		?>
	<input type="hidden" name="currentPage" id="currentPage" value="payment_shipping">

	<div class="ui-widget ui-widget-content ui-corner-all">
		<?php if ($onePageCheckout->isNormalCheckout()){

		ob_start();
		require(sysConfig::getDirFsCatalog() . 'applications/checkout/pages/cart.php');
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

					if ($totalModules > 1){
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

									$addClass = ' ui-state-default';
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
		if(sysConfig::get('SHOW_COMMENTS_CHECKOUT') == 'true'){
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
			<?php
		}
		?>
	<br>
	<div align="right"><table class="orderTotalsList" cellpadding="2" cellspacing="0" border="0" style="margin:.5em;"><?php
		echo OrderTotalModules::output();
		?></table></div>
		<?php
		$pageHtml = ob_get_contents();
		ob_end_clean();
	}else{
		ob_start();
		require(sysConfig::getDirFsCatalog() . 'applications/mobile/pagesTabs/checkout/addresses.php');
	$pageHtml = ob_get_contents();
	ob_end_clean();
}
echo $pageHtml;
?>
</div>
<?php
if (sysConfig::get('TERMS_CONDITIONS_SHOPPING_CART') == 'false'){
	?>
<br><br><div style="text-align:center;">
	<label for="terms"><?php echo sysLanguage::get('ENTRY_AGREE_TO_TERMS');?></a></label>&nbsp;<input data-theme="c" type="checkbox" name="terms" id="terms" value="1">
	<b><a id="readTerms" href="<?php echo itw_app_link('appExt=infoPages&ui-state=dialog', 'show_page', 'conditions');?>"><?php echo sysLanguage::get('TEXT_AGREE_TO_TERMS');?></a>
</div>
<br><br>
<?php
}
else {
	echo htmlBase::newElement('input')
		->setType('hidden')
		->setName('terms')
		->attr('checked', true)
		->setValue('1')
		->draw();
}
?>
<div class="ui-widget ui-widget-content ui-corner-all" style="padding:.3em;"><table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td class="main" id="checkoutMessage"><?php echo '<b>' . sysLanguage::get('TITLE_CONTINUE_CHECKOUT_PROCEDURE') . '</b><br>' . sysLanguage::get('TEXT_CONTINUE_CHECKOUT_PROCEDURE'); ?></td>
		<td class="main" align="right"><?php
			echo htmlBase::newElement('button')
				->setType('submit')
				->usePreset('continue')
				->setId('continueButton')
				->setName('continueButton')
				->draw();
			?></td>
	</tr>
</table></div>
<br>
<div class="ui-grid-b checkoutProgress">
	<div class="ui-block-a" style="text-align:center;"><div class="ui-bar ui-bar-b"><?php echo sysLanguage::get('TEXT_BEGIN'); ?></div></div>
	<div class="ui-block-b" style="text-align:center;"><div class="ui-bar ui-bar-c"><?php echo sysLanguage::get('TEXT_PAYMENT_SHIPPING');?></div></div>
	<div class="ui-block-c" style="text-align:center;"><div class="ui-bar ui-bar-c"><?php echo sysLanguage::get('CHECKOUT_BAR_FINISHED'); ?></div></div>
</div>
<script type="text/javascript">

	var TEXT_CONFIRM_ORDER = '<?php echo sysLanguage::get('TEXT_CONFIRM_ORDER');?>';
	var POST_TO_URL = '<?php echo itw_app_link('rType=ajax&action=checkoutProcess&showErrors=true', 'mobile', 'checkout');?>';
	var SET_PAYMENT_URL = '<?php echo itw_app_link('rType=ajax&action=setPaymentMethod&showErrors=true', 'checkout', 'default');?>';
	var SET_SHIPPING_URL = '<?php echo itw_app_link('rType=ajax&action=setShippingMethod&showErrors=true', 'checkout', 'default');?>';
	var GET_COUNTRY_ZONES_URL = '<?php echo itw_app_link('rType=ajax&action=getCountryZones&showErrors=true', 'checkout', 'default');?>';
	var REDEEM_VOUCHER_URL = '<?php echo itw_app_link('rType=ajax&action=redeemVoucher&showErrors=true', 'checkout', 'default');?>';
	var ADDRESS_BOOK_URL = '<?php echo itw_app_link('rType=ajax&action=getAddressBook&showErrors=true', 'mobile', 'checkout');?>';

	var checkoutProgressInActiveClass = 'ui-bar-c';
	var checkoutProgressActiveClass = 'ui-bar-b';
	var checkoutProgressPastClass = 'ui-bar-a';

	function loadPageOne($content){
		$content.find('#changeBillingAddress, #changeShippingAddress, #changePickupAddress').click(function (e) {
			e.preventDefault();

			var addressType = 'billing';
			if ($(this).attr('id') == 'changeShippingAddress'){
				addressType = 'shipping';
			}
			if ($(this).attr('id') == 'changePickupAddress'){
				addressType = 'pickup';
			}
			$.mobile.changePage(ADDRESS_BOOK_URL + '&addressType=' + addressType, {
				transition: "pop",
				role: 'dialog',
				reverse: false,
				changeHash: true
			});

			/*$('#addressBook').clone().show().appendTo(document.body).dialog({
				shadow : false,
				width : 550,
				// height: 450,
				minWidth : 550,
				//minHeight: 500,
				open : function (e, ui) {
					var $dialog = $(this);
					showAjaxLoader($dialog.parent(), 'xlarge');
					var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
					$.ajax({
						url : js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=getAddressBook'),
						data : 'addressType=' + addressType,
						type : 'post',
						success : function (data) {
							$dialog.html(data);
							hideAjaxLoader($dialog.parent(), 'xlarge');
						}
					});
				},
				buttons : {
					'Cancel' : function () {
						var self = $(this);
						var action = $('input[name="action"]', self).val();
						if (action == 'selectAddress'){
							self.dialog('close');
						}
					},
					'Continue' : function () {
						var $this = $(this);
						var action = $('input[name="action"]', $this).val();
						//alert($(':input, :select, :radio, :checkbox', this).serialize());
						if (action == 'selectAddress'){
							showAjaxLoader($this.parent(), 'xlarge');
							var linkParams = js_get_all_get_params(['app', 'appPage', 'action', 'type']);
							$.ajax({
								url : js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=process&type=addressBook'),
								dataType : 'json',
								data : $(':input, :radio', this).serialize(),
								type : 'post',
								success : function (data) {
									$this.dialog('close');
									hideAjaxLoader($this.parent(), 'xlarge');
									$('.checkoutContent').html(data.pageHtml);
									$('#changeBillingAddress').button();
									$('#changeShippingAddress').button();
									$('#changePickupAddress').button();
									if (data.isShipping == true){
										$('.shippingAddressDiff').trigger('click');
										$('.shippingAddress').show();
									}
									if (data.isPickup == true){
										$('.pickupAddressDiff').trigger('click');
										$('.pickupAddress').show();
									}
								}
							});
						}
					}

				}});*/
			return false;
		});

		$content.find('select[name="billing[entry_country]"], select[name="shipping[entry_country]"], select[name="pickup[entry_country]"]').change(function () {
			var fieldName = $(this).attr('name');
			if (/billing/i.test(fieldName)){
				var stateType = 'billing';
			}else if (/shipping/i.test(fieldName)){
				var stateType = 'shipping';
			} else if (/pickup/i.test(fieldName)){
				var stateType = 'pickup';
			}
			var $stateField = $('[name="' + stateType + '[entry_state]"]');
			if ($stateField.size() > 0){
				$.ajax({
					url : GET_COUNTRY_ZONES_URL,
					cache : false,
					dataType : 'html',
					data : 'cID=' + $(this).val() + '&curVal=' + $stateField.val() + '&state_type=namedLater',
					success : function (data) {
						var $newField = $(data);
						$newField.attr('name', stateType + '[entry_state]');

						if ($stateField.parent().parent().hasClass('ui-select')){
							$stateField.replaceWith($newField);
							$newField.parent().trigger( "refresh" );
						}else{
							$stateField.replaceWith($newField);
							$newField.parent().trigger( "create" );
						}

					}
				});
			}
		}).trigger('change');
	}

	function loadPageTwo($content){
		$content.find('input[name=payment_method]').click(function (e) {
			e.stopPropagation();

			$('.paymentFields').hide();
			$('.paymentFields *').attr('disabled', 'disabled');
			var $Fields = $('.paymentFields.' + $(this).val());
			if ($Fields){
				$Fields.show();
				$Fields.find('input, select').removeAttr('disabled');
			}

			$.mobile.showPageLoadingMsg();
			$.ajax({
				url : SET_PAYMENT_URL,
				cache : false,
				dataType : 'json',
				type : 'post',
				data : 'payment_method=' + $(this).val(),
				success : function (data) {
					$.mobile.hidePageLoadingMsg();
					$content.find('.orderTotalsList').html(data.orderTotalRows);
				}
			});
		});

		$content.find('input[name=shipping_method]').click(function (e) {
			e.stopPropagation();

			$.mobile.showPageLoadingMsg();
			$.ajax({
				url : SET_SHIPPING_URL,
				cache : false,
				dataType : 'json',
				type : 'post',
				data : 'shipping_method=' + $(this).val(),
				success : function (data) {
					$.mobile.hidePageLoadingMsg();
					$content.find('.orderTotalsList').html(data.orderTotalRows);
				}
			});
		});

		$content.find('input[name=shipping_method]:checked').click();

		$content.find('#voucherRedeem').click(function () {
			$.mobile.showPageLoadingMsg();
			$.ajax({
				url : REDEEM_VOUCHER_URL,
				data : 'code=' + $content.find('input[name="redeem_code"]').val(),
				type : 'post',
				dataType : 'json',
				success : function (data) {
					$.mobile.hidePageLoadingMsg();
					if (data.errorMsg != ''){
						alert(data.errorMsg);
					}
					$content.find('.orderTotalsList').html(data.orderTotalRows);
				}
			});
			return false;
		});
	}

	function loadPageThree($content){
		$('#terms').parent().parent().remove();

		$('#continueButton').unbind('click').click(function (){
			document.location = 'index/default.php';
		});
	}

	$('#continueButton').click(function () {
		$.mobile.silentScroll(0);

		//validate_form for currentpage addresses and shipping_payment
		if ($('#currentPage').val() == 'payment_shipping'){
			if ($(':radio[name="payment_method"]:checked').size() <= 0){
				if ($('input[name="payment_method"]:hidden').size() <= 0){
					alert('Please Select a Payment Method');
					return false;
				}
			}
		}
		if ($('#currentPage').val() == 'success'){
			js_redirect(DIR_WS_CATALOG);
			return false;
		}
		$.mobile.showPageLoadingMsg();
		$.ajax({
			url : POST_TO_URL,
			cache : false,
			dataType : 'json',
			data : $('form[name=checkout]').serialize(),
			type : 'post',
			success : function (data) {
				$.mobile.hidePageLoadingMsg();

				var $page = $(':jqmData(role=page)');
				// Get the header for the page.
				var $header = $page.children( ":jqmData(role=header)" );
				// Get the content area element for the page.
				var $content = $('.checkoutContent');

				$content.html(data.pageHtml);
				$content.trigger( "create" );

				if (data.isShipping == true){
					$('.shippingAddressDiff').trigger('click');
					$('.shippingAddress').show();
				}
				if (data.isPickup == true){
					$('.pickupAddressDiff').trigger('click');
					$('.pickupAddress').show();
				}
				if ($('#currentPage').val() == 'processing'){
					$('#continueButton').hide();
					$('.breadCrumb').html('<a class="headerNavigation" href="' + js_app_link('app=index&appPage=default') + '">You Are Here: Home</a> &raquo; Checkout &raquo; Processing');
				}
				else {
					$('#continueButton').show();
				}

				if ($('#currentPage').val() == 'addresses'){
					loadPageOne($content);
				}
				else if ($('#currentPage').val() == 'payment_shipping'){
					$('.checkoutProgress').find('.ui-block-a .' + checkoutProgressActiveClass).removeClass(checkoutProgressActiveClass).addClass(checkoutProgressPastClass);
					$('.checkoutProgress').find('.ui-block-b .' + checkoutProgressInActiveClass).removeClass(checkoutProgressInActiveClass).addClass(checkoutProgressActiveClass);

					loadPageTwo($content);
				}
				else if ($('#currentPage').val() == 'success'){
					$('.checkoutProgress').find('.ui-block-b .' + checkoutProgressActiveClass).removeClass(checkoutProgressActiveClass).addClass(checkoutProgressPastClass);
					$('.checkoutProgress').find('.ui-block-c .' + checkoutProgressInActiveClass).removeClass(checkoutProgressInActiveClass).addClass(checkoutProgressActiveClass);

					loadPageThree($content);
				}
				$.mobile.silentScroll(0, 0);
			}
		});
		return false;
	});

	$('#addressBook').live('pageinit', function (){
		$(this).find('#selectAddress').click(function (){
			if ($('input[name=address]:checked').size() == 1){
				$.ajax({
					url : '<?php echo itw_app_link('action=checkoutProcess&type=addressBook', 'mobile', 'checkout');?>',
					dataType : 'json',
					data : 'address=' + $('input[name=address]:checked').val(),
					type : 'post',
					success : function (data) {
						$('.ui-icon-delete').parent().parent().click();

						var $content = $('.checkoutContent');
						$content.html(data.pageHtml);
						$content.trigger( "create" );

						loadPageOne($('.checkoutContent'));
					}
				});
			}
		});
	});

	$('#loginDialog').live('pageinit', function (){
		$(this).find('#loginDialogSubmit').click(function (){
			$.ajax({
				url: js_app_link('rType=ajax&app=account&appPage=login&action=processLogin'),
				cache: false,
				data: $('input[name=email_address], input[name=password]').serialize(),
				type: 'post',
				dataType: 'json',
				success: function (data){
					if (data.success === true){
						$('div:jqmData(role=page)').remove();
						$.mobile.changePage(js_app_link('app=mobile&appPage=checkout'), {
							reverse: true,
							reloadPage: true,
							changeHash: true
						});
					}else{
						alert('There was an error logging in, please try again.');
					}
				}
			});
		});
	});

	$('#loginButton').click(function (e){
		e.preventDefault();

		$.mobile.changePage('<?php echo itw_app_link('rType=ajax&ui_state=dialog', 'mobile', 'loginDialog');?>', {
			transition: "pop",
			role: 'dialog',
			reverse: false,
			changeHash: true
		});
		return false;
	});

	$('#readTerms').click(function (e){
		e.preventDefault();

		$.mobile.changePage($(this).attr('href'), {
			transition: "pop",
			role: 'dialog',
			reverse: false,
			changeHash: true
		});
		return false;
	});

	$('div').live('pageinit', function (){
		if ($(this).jqmData('role') != 'dialog'){
			return;
		}
		var self = this;
		$(this).find('.ui-icon-delete').parent().parent().click(function (e){
			$(self).dialog('close');
			return false;
		});
	});

	loadPageOne($('.checkoutContent'));
</script>
<?php
$pageContents = ob_get_contents();
ob_end_clean();

$pageContent->set('pageForm', array(
	'name' => 'checkout',
	'action' => 'Javascript:void(0)',
	'method' => 'post'
));
$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE'));
$pageContent->set('pageContent', $pageContents);
?>