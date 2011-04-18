<?php
/*
	Pay Per Rentals Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class payPerRentals_ot_deposit {
	var $title, $output;

	public function __construct() {
		$langDefines = simplexml_load_file(sysConfig::getDirFsCatalog() . 'extensions/payPerRentals/order_total/language_defines/' . Session::get('language') . '.xml', 'SimpleXMLElement', LIBXML_NOCDATA);
		$titleArr = $langDefines->xpath('//define[@key="EXTENSION_PAY_PER_RENTALS_ORDER_TOTAL_DEPOSIT_TITLE"]');
		$descArr = $langDefines->xpath('//define[@key="EXTENSION_PAY_PER_RENTALS_ORDER_TOTAL_DEPOSIT_DESCRIPTION"]');

		$this->code = 'payPerRentals_ot_deposit';

		$this->title = (string) $titleArr[0];
		$this->description = (string) $descArr[0];
		$this->enabled = false;
		$this->output = array();

		if (sysConfig::exists('EXTENSION_PAY_PER_RENTALS_ORDER_TOTAL_DEPOSIT_STATUS')){
			$this->sort_order = sysConfig::get('EXTENSION_PAY_PER_RENTALS_ORDER_TOTAL_DEPOSIT_SORT_ORDER');
			$this->enabled = ((sysConfig::get('EXTENSION_PAY_PER_RENTALS_ORDER_TOTAL_DEPOSIT_STATUS') == 'True') ? true : false);
		}
	}

	public function process() {
		global $order, $currencies;

		$amount = 0;
		$products = $order->products;
		foreach($products as $pInfo){
			if (isset($pInfo['reservation']) && isset($pInfo['reservation']['deposit_amount'])){
				if ($pInfo['reservation']['deposit_amount'] > 0){
					$amount += $pInfo['reservation']['deposit_amount'];
				}
			}
		}

		if ($amount > 0){
			$order->info['total'] += $amount;
			$this->output[] = array(
				'title' => $this->title . ':',
				'help' => itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'ppr_deposit_help', 'SSL'),
				'text' => $currencies->format($amount, true, $order->info['currency'], $order->info['currency_value']),
				'value' => $amount
		);
		}
	}

	public function check() {
		if (!isset($this->_check)) {
			$check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'EXTENSION_PAY_PER_RENTALS_ORDER_TOTAL_DEPOSIT_STATUS'");
			$this->_check = tep_db_num_rows($check_query);
		}

		return $this->_check;
	}

	public function keys() {
		return array('EXTENSION_PAY_PER_RENTALS_ORDER_TOTAL_DEPOSIT_STATUS', 'EXTENSION_PAY_PER_RENTALS_ORDER_TOTAL_DEPOSIT_SORT_ORDER');
	}

	public function install() {
		$dataArray = array(
			'sortOrderKey'      => 'EXTENSION_PAY_PER_RENTALS_ORDER_TOTAL_DEPOSIT_SORT_ORDER',
			array(
				'configuration_title'       => 'Display Pay Per Rentals Deposit',
				'configuration_key'         => 'EXTENSION_PAY_PER_RENTALS_ORDER_TOTAL_DEPOSIT_STATUS',
				'configuration_value'       => 'True',
				'configuration_description' => 'Do you want to display the pay per rentals deposit?',
				'set_function'              => 'tep_cfg_select_option(array(\'True\', \'False\'),'
			)
		);
		return $dataArray;
	}
}
?>