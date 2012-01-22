<?php
class OrderTotalGiftvoucher extends OrderTotalModuleBase
{

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Gift Vouchers');
		$this->setDescription('Gift Vouchers');

		$this->init('giftvoucher');

		if ($this->isInstalled() === true){
			$this->include_shipping = $this->getConfigData('MODULE_ORDER_TOTAL_GV_INC_SHIPPING');
			$this->include_tax = $this->getConfigData('MODULE_ORDER_TOTAL_GV_INC_TAX');
			$this->calculate_tax = $this->getConfigData('MODULE_ORDER_TOTAL_GV_CALC_TAX');
			$this->credit_tax = $this->getConfigData('MODULE_ORDER_TOTAL_GV_CREDIT_TAX');
			$this->tax_class = $this->getConfigData('MODULE_ORDER_TOTAL_GV_TAX_CLASS');
			$this->user_prompt = $this->getConfigData('MODULE_ORDER_TOTAL_GV_USER_PROMPT');
			$this->header = $this->getConfigData('MODULE_ORDER_TOTAL_GV_HEADER');
			$this->checkbox = htmlBase::newElement('checkbox')->setId('voucherPayment')
				->setName('c' . $this->getCode());
		}
	}

	public function process() {
		global $order;
		if (Session::exists('cot_gv') === true){
			$order_total = $this->get_order_total();
			$od_amount = $this->calculate_credit($order_total);

			if ($this->calculate_tax != "None"){
				$tod_amount = $this->calculate_tax_deduction($order_total, $od_amount, $this->calculate_tax);
				$od_amount = $this->calculate_credit($order_total);
			}

			$this->deduction = $od_amount;
			$order->info['total'] = $order->info['total'] - $od_amount;
			if ($od_amount > 0){
				$this->addOutput(array(
						'title' => $this->getTitle() . ':',
						'text' => '<b>' . $this->formatAmount($od_amount) . '</b>',
						'value' => $od_amount
					));
			}
		}
	}

	public function mod_process() {
		global $currencies;
		$my_order_total = $this->get_order_total();
		$my_od_amount = $this->calculate_credit($my_order_total);

		if ($this->calculate_tax != "None"){
			$tod_amount = $this->calculate_tax_deduction($my_order_total, $my_od_amount, $this->calculate_tax);
			$my_od_amount = $this->calculate_credit($my_order_total);
		}

		$this->deduction = $my_od_amount;

		if ($my_od_amount > 0){
			$this->my_output[] = array(
				'title' => $this->getTitle() . ':',
				'text' => '<b>' . $currencies->format($my_od_amount) . '</b>',
				'value' => $my_od_amount
			);
		}
	}

	// #################### End Added CGV JONYO ######################

	public function selection_test() {
		$userAccount = &Session::getReference('userAccount');
		if ($this->user_has_gv_account($userAccount->getCustomerId())){
			return true;
		}
		else {
			return false;
		}
	}

	public function pre_confirmation_check($order_total) {
		global $order;
		$od_amount = 0;
		if (Session::exists('cot_gv') === true){
			if ($this->include_tax == 'False'){
				$order_total = $order_total - $order->info['tax'];
			}

			if ($this->include_shipping == 'False'){
				$order_total = $order_total - $order->info['shipping_cost'];
			}

			$od_amount = $this->calculate_credit($order_total);

			if ($this->calculate_tax != "None"){
				$tod_amount = $this->calculate_tax_deduction($order_total, $od_amount, $this->calculate_tax);
				$od_amount = $this->calculate_credit($order_total) + $tod_amount;
			}
		}
		return $od_amount;
	}

	public function use_credit_amount() {
		if (Session::exists('cot_gv')){
			Session::remove('cot_gv');
		}
		if ($this->selection_test()){
			$output_string = $this->checkbox . '</b>' . '</td>' . "\n";
		}

		return $output_string;
	}

	public function use_credit_amount_sub() {
		global $order, $currencies;
		if (Session::exists('cot_gv')){
			Session::remove('cot_gv');
		}
		if ($this->selection_test()){
			$customersBalance = $this->getCustomerGvAmount();
			if ($customersBalance > $order->info['total']){
				$customersBalance = $order->info['total'];
				$this->checkbox->addClass('coversAll');
			}
			$output_string .= '<b>' . $this->checkbox->draw() . $currencies->format($customersBalance) . $this->user_prompt . '</b>' . "\n";
		}
		return $output_string;
	}

	public function update_credit_account($cartProduct) {
		global $order;

		$userAccount = &Session::getReference('userAccount');
		if (ereg('^GIFT', addslashes($cartProduct->getModel()))){
			$gv_order_amount = ($cartProduct->getFinalPrice() * $cartProduct->getQuantity());
			if ($this->credit_tax == 'True') {
				$gv_order_amount = $gv_order_amount * (100 + $cartProduct->getTaxRate()) / 100;
			}
			$gv_order_amount = $gv_order_amount * 100 / 100;

			if (MODULE_ORDER_TOTAL_GV_QUEUE == 'false'){
				$customer_gv = false;
				$total_gv_amount = 0;

				$giftVoucherAmount = $this->getCustomerGvAmount();
				if ($giftVoucherAmount){
					$total_gv_amount = $giftVoucherAmount;
					$customer_gv = true;
				}

				$total_gv_amount = $total_gv_amount + $gv_order_amount;
				if ($customer_gv === true){
					$gvQuery = Doctrine_Core::getTable('CouponGvCustomer')
						->findOneByCustomerId($userAccount->getCustomerId());
				}
				else {
					$gvQuery = new CouponGvCustomer();
					$gvQuery->customer_id = $userAccount->getCustomerId();
				}
				$gvQuery->amount = $total_gv_amount;
			}
			else {
				$gvQuery = new CouponGvQueue();
				$gvQuery->customer_id = $userAccount->getCustomerId();
				$gvQuery->order_id = $order->newOrder['orderID'];
				$gvQuery->amount = $gv_order_amount;
				$gvQuery->ipaddr = $_SERVER['REMOTE_ADDR'];
			}
			$gvQuery->save();
		}
	}

	public function credit_selection() {
		$selection_string = '';
		$Qcoupon = Doctrine_Query::create()
			->select('coupon_id')
			->from('Coupons')
			->where('coupon_type = ?', 'G')
			->andWhere('coupon_active = ?', 'Y')
			->fetchOne();
		if ($Qcoupon){
			$coupon = $Qcoupon->toArray();

			$selection_string .= '<tr>' . "\n";
			$selection_string .= '  <td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>';
			$selection_string .= '  <td class="main">' . "\n";
			$image_submit = htmlBase::newElement('button')->setType('submit')->attr('onclick', 'submitFunction()')
				->setName('submit_redeem')->setText(sysLanguage::get('IMAGE_REDEEM_VOUCHER'))->draw();
			$selection_string .= sysLanguage::get('TEXT_ENTER_GV_CODE') . tep_draw_input_field('gv_redeem_code') . '</td>';
			$selection_string .= ' <td align="right">' . $image_submit . '</td>';
			$selection_string .= '  <td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>';
			$selection_string .= '</tr>' . "\n";
		}
		return $selection_string;
	}

	public function apply_credit() {
		$gv_payment_amount = 0;
		if (Session::exists('cot_gv') === true){
			$userAccount = &Session::getReference('userAccount');

			$giftVoucherAmount = $this->getCustomerGvAmount();

			$gv_payment_amount = $this->deduction;
			$gv_amount = $giftVoucherAmount - $gv_payment_amount;

			Doctrine_Query::create()
				->update('CouponGvCustomer')
				->set('amount', '?', $gv_amount)
				->where('customer_id = ?', $userAccount->getCustomerId())
				->execute();
		}
		return $gv_payment_amount;
	}

	public function collect_posts() {
		global $currencies;
		if (array_key_exists('gv_redeem_code', $_POST)){
			$Qcoupon = Doctrine_Query::create()
				->select('coupon_id, coupon_type, coupon_amount')
				->from('Coupons')
				->where('coupon_code = ?', $_POST['gv_redeem_code'])
				->fetchOne();
			if ($Qcoupon){
				$coupon = $Qcoupon->toArray();

				$Qcheck = Doctrine_Query::create()
					->select('count(coupon_id) as total')
					->from('CouponRedeemTrack')
					->where('coupon_id = ?', $coupon['coupon_id'])
					->execute()->toArray();
				if ($Qcheck['total'] > 0 && $coupon['coupon_type'] == 'G'){
					tep_redirect(itw_app_link('payment_error=' . $this->getCode() . '&error=' . urlencode(sysLanguage::get('ERROR_NO_INVALID_REDEEM_GV')), 'checkout', 'default', 'SSL'));
				}
			}
			elseif (array_key_exists('submit_redeem_x', $_POST)) {
				tep_redirect(itw_app_link('payment_error=' . $this->getCode() . '&error=' . urlencode(sysLanguage::get('ERROR_NO_INVALID_REDEEM_GV')), 'checkout', 'default', 'SSL'));
			}

			if ($coupon['coupon_type'] == 'G'){
				$userAccount = &Session::getReference('userAccount');

				$coupon_amount = $coupon['coupon_amount'];
				// Things to set
				// ip address of claimant
				// customer id of claimant
				// date
				// redemption flag
				// now update customer account with gv_amount
				$giftVoucherAmount = $this->getCustomerGvAmount();
				$customer_gv = false;
				$total_gv_amount = $coupon['coupon_amount'];
				if ($giftVoucherAmount){
					$total_gv_amount += $giftVoucherAmount;
					$customer_gv = true;
				}

				Doctrine_Query::create()
					->update('Coupons')
					->set('coupon_active', '?', 'N')
					->where('coupon_id = ?', $coupon['coupon_id'])
					->execute();

				$redeemTrack = new CouponRedeemTrack();
				$redeemTrack->coupon_id = $coupon['coupon_id'];
				$redeemTrack->customer_id = $userAccount->getCustomerId();
				$redeemTrack->redeem_date = date('Y-m-d');
				$redeemTrack->redeem_ip = $_SERVER['REMOTE_ADDR'];
				$redeemTrack->save();

				$CouponGvCustomer = Doctrine_Core::getTable('CouponGvCustomer');
				if ($customer_gv === true){
					$gvQuery = $CouponGvCustomer->findOneByCustomerId($userAccount->getCustomerId());
				}
				else {
					$gvQuery = $CouponGvCustomer->create();
					$gvQuery->customer_id = $userAccount->getCustomerId();
				}
				$gvQuery->amount = $total_gv_amount;
				$gvQuery->save();

				tep_redirect(itw_app_link('payment_error=' . $this->getCode() . '&error=' . urlencode(sysLanguage::get('ERROR_REDEEMED_AMOUNT') . $currencies->format($coupon_amount)), 'checkout', 'default', 'SSL'));
			}
		}

		if (array_key_exists('submit_redeem_x', $_POST) && !array_key_exists('gv_redeem_code', $_POST)){
			tep_redirect(itw_app_link('payment_error=' . $this->getCode() . '&error=' . urlencode(sysLanguage::get('ERROR_NO_REDEEM_CODE')), 'checkout', 'default', 'SSL'));
		}
	}

	public function getCustomerGvAmount($customerId = null) {
		$userAccount = &Session::getReference('userAccount');

		$QgiftVoucher = Doctrine_Query::create()
			->select('amount')
			->from('CouponGvCustomer')
			->where('customer_id = ?', (is_null($customerId) ? $userAccount->getCustomerId() : $customerId))
			->fetchOne();
		if ($QgiftVoucher){
			$voucher = $QgiftVoucher->toArray();
			return $voucher['amount'];
		}
		return false;
	}

	public function calculate_credit($amount) {
		global $order;
		$userAccount = &Session::getReference('userAccount');
		$gv_payment_amount = 0;

		$giftVoucherAmount = $this->getCustomerGvAmount();
		if ($giftVoucherAmount){
			$gv_payment_amount += $giftVoucherAmount;
		}

		$gv_amount = $gv_payment_amount;
		$save_total_cost = $amount;
		$full_cost = $save_total_cost - $gv_payment_amount;
		if ($full_cost <= 0){
			$full_cost = 0;
			$gv_payment_amount = $save_total_cost;
		}
		return tep_round($gv_payment_amount, 2);
	}

	public function calculate_tax_deduction($amount, $od_amount, $method) {
		global $order;
		switch($method){
			case 'Standard':
				// Amended line, was giving an error when a zero value was arriving here. v5.13 by Rigadin
				// v5.13 spelling error introduced an error, corrected
				//$ratio1 = tep_round($od_amount / $amount,2);
				$ratio1 = ($amount == 0 ? 0 : tep_round($od_amount / $amount, 2));
				$tod_amount = 0;
				reset($order->info['tax_groups']);
				while(list($key, $value) = each($order->info['tax_groups'])){
					$tax_rate = tep_get_tax_rate_from_desc($key);
					$total_net += $tax_rate * $order->info['tax_groups'][$key];
				}
				if ($od_amount > $total_net) {
					$od_amount = $total_net;
				}
				reset($order->info['tax_groups']);
				while(list($key, $value) = each($order->info['tax_groups'])){
					$tax_rate = tep_get_tax_rate_from_desc($key);
					$net = $tax_rate * $order->info['tax_groups'][$key];
					if ($net > 0){
						$god_amount = $order->info['tax_groups'][$key] * $ratio1;
						$tod_amount += $god_amount;
						$order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] - $god_amount;
					}
				}
				$order->info['tax'] -= $tod_amount;
				$order->info['total'] -= $tod_amount;
				break;
			case 'Credit Note':
				$tax_rate = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
				$tax_desc = tep_get_tax_description($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
				$tod_amount = $this->deduction / (100 + $tax_rate) * $tax_rate;
				$order->info['tax_groups'][$tax_desc] -= $tod_amount;
				//          $order->info['total'] -= $tod_amount;   //// ????? Strider
				break;
			default:
		}
		return $tod_amount;
	}

	public function user_has_gv_account($c_id) {
		$giftVoucherAmount = $this->getCustomerGvAmount($c_id);
		if ($giftVoucherAmount !== false && $giftVoucherAmount > 0){
			return true;
		}
		return false;
	}

	public function get_order_total() {
		global $order;
		$order_total = $order->info['total'];
		if ($this->include_tax == 'False') {
			$order_total = $order_total - $order->info['tax'];
		}
		if ($this->include_shipping == 'False') {
			$order_total = $order_total - $order->info['shipping_cost'];
		}

		return $order_total;
	}

	// START added by Rigadin in v5.13, needed to show module errors on checkout_payment page
	public function get_error() {
		if (isset($_GET['error'])) {
			$error_req = $_GET['error'];
		}
		$error = array('title' => sysLanguage::get('MODULE_ORDER_TOTAL_GV_TEXT_ERROR'),
			'error' => stripslashes(urldecode($_GET['error'])));

		return $error;
	}
	// END added by Rigadin
}

?>