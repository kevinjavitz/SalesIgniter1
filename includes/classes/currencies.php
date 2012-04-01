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

	private $exchange_rates = array();

	public function __construct(){
		$this->currencies = array();

		$ResultSet = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from currencies');
		if ($ResultSet && sizeof($ResultSet) > 0){
			foreach($ResultSet as $currency){
				$this->currencies[$currency['code']] = array(
					'code'            => $currency['code'],
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

		$useLanguageCurrency = sysConfig::get('USE_DEFAULT_LANGUAGE_CURRENCY');
		$systemDefaultCurrency = sysConfig::get('DEFAULT_CURRENCY');
		$languageDefaultCurrency = sysLanguage::getCurrency();
		if (Session::exists('currency') === false || isset($_GET['currency']) || ($useLanguageCurrency == 'true' && $languageDefaultCurrency != Session::get('currency'))){
			if (isset($_GET['currency'])) {
				if ($this->is_set($_GET['currency']) === false){
					$currency = ($useLanguageCurrency == 'true') ? $languageDefaultCurrency : $systemDefaultCurrency;
				}
			} else {
				$currency = ($useLanguageCurrency == 'true') ? $languageDefaultCurrency : $systemDefaultCurrency;
			}

			Session::set('currency', $currency);
			Session::set('currency_value', $this->currencies[$currency]['value']);
		}
	}

	public function format($number, $calculate_currency_value = true, $currency_type = '', $currency_value = ''){
		if (empty($currency_type)){
			if(Session::exists('current_store_id') == true){
				if(Session::exists('currencyStore'.Session::get('current_store_id')) == false){
					$currency_type = Session::get('currency');
				}else{
					$currency_type = Session::get('currencyStore'.Session::get('current_store_id'));
					if(Session::exists('mainCurrencyStore'.Session::get('current_store_id'))&& Session::get('mainCurrencyStore'.Session::get('current_store_id')) == Session::get('currency')){
						$currency_value = 1;
					}else{
						if(Session::exists('mainCurrencyStore'.Session::get('current_store_id'))){
							$currency_value = $this->currencies[$currency_type]['value']/$this->currencies[Session::get('mainCurrencyStore'.Session::get('current_store_id'))]['value'];
						}
					}
				}
			}else{
				$currency_type = Session::get('currency');
			}
		}


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

	public function get($code){
		if (isset($this->currencies[$code])){
			return $this->currencies[$code];
		}
		return false;
	}

	function is_set($code) {
		if (isset($this->currencies[$code])){
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

	public function convert($amount = 1, $from = "GBP", $to = null, $decimals = 2){
		if (is_null($to)){
			$to = Session::get('currency');
		}

		if (!isset($this->exchange_rates[$from][$to])){
			//make string to be put in API
			$string = 1 . $from."=?".$to;

			//Call Google API
			$google_url = "http://www.google.com/ig/calculator?hl=en&q=1" . $from . "=?" . $to;

			//Get and Store API results into a variable
			$result = file_get_contents($google_url);
			$result = preg_replace('/(\w+):/', '"\\1":', $result);
			$result = json_decode($result);
			$result->lhs = (float) $result->lhs;
			$result->rhs = (float) $result->rhs;

			$this->exchange_rates[$from][$to] = $result->rhs;
		}

		return (number_format($amount * $this->exchange_rates[$from][$to], $decimals));
	}
}
?>