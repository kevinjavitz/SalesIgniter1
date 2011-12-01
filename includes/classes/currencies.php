<?php
/*
$Id: currencies.php,v 1.16 2003/06/05 23:16:46 hpdl Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2003 osCommerce

Released under the GNU General Public License
*/

////
// Class to handle currencies
// TABLES: currencies
class currencies {
	public $currencies;

	public function __construct(){
		$this->currencies = array();

		$currencies = Doctrine_Query::create()
			->select('*')
			->from('CurrenciesTable')
			->orderBy('title')->fetchArray();
		if ($currencies){
			foreach($currencies as $currency){
				$this->currencies[$currency['code']] = array(
					'title'           => $currency['title'],
					'symbol_left'     => $currency['symbol_left'],
					'symbol_right'    => $currency['symbol_right'],
					'decimal_point'   => $currency['decimal_point'],
					'thousands_point' => $currency['thousands_point'],
					'decimal_places'  => $currency['decimal_places'],
					'value'           => $currency['value']
				);
			}
		}
	}

	public function format($number, $calculate_currency_value = true, $currency_type = '', $currency_value = ''){
		if (empty($currency_type)) $currency_type = Session::get('currency');

		$symbolLeft = $this->currencies[$currency_type]['symbol_left'];
		$symbolRight = $this->currencies[$currency_type]['symbol_right'];
		$decimalPlaces = $this->currencies[$currency_type]['decimal_places'];
		$decimalPoint = $this->currencies[$currency_type]['decimal_point'];
		$thousandsPoint = $this->currencies[$currency_type]['thousands_point'];

		$rate = 1;
		if ($calculate_currency_value == true){
			$rate = (!empty($currency_value) ? $currency_value : $this->currencies[$currency_type]['value']);
			$number = $number * $rate;

			/*
			* if the selected currency is in the european euro-conversion and the default currency is euro,
			* the currency will displayed in the national currency and euro currency
			*/
			$checkArr = array(
			'DEM', 'BEF', 'LUF', 'ESP', 'FRF', 'IEP', 'ITL', 'NLG', 'ATS', 'PTE', 'FIM', 'GRD'
			);
			if (sysConfig::get('DEFAULT_CURRENCY') == 'EUR' && in_array($currency_type, $checkArr)){
				$symbolRight .= ' <small>[' . $this->format($number, true, 'EUR') . ']</small>';
			}
		}

		$format_string = $symbolLeft . number_format(
		tep_round($number, $decimalPlaces),
		$decimalPlaces,
		$decimalPoint,
		$thousandsPoint
		) . $symbolRight;
		return $format_string;
	}

	function is_set($code) {
		if (isset($this->currencies[$code]) && tep_not_null($this->currencies[$code])){
			return true;
		}
		return false;
	}

	function get_value($code) {
		return $this->currencies[$code]['value'];
	}

	function get_decimal_places($code) {
		return $this->currencies[$code]['decimal_places'];
	}

	function display_price($products_price, $products_tax, $quantity = 1) {
		return $this->format(tep_add_tax($products_price, $products_tax) * $quantity);
	}
}
?>