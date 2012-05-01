<?php
class OrderShippingUsps extends OrderShippingModuleBase
{

	private $handlingCost;

	private $useServer;

	private $userId;

	private $userPass;

	private $types;

	private $intl_types;

	private $quotes;

	private $pounds;

	private $ounces;

	private $container;

	private $machinable;

	private $size;

	private $fctype;

	private $countries;

	private $selectedTypes;

	private $selectedIntlTypes;



	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('USPS');
		$this->setDescription('United States Postal Service');
		
		$this->init('usps');
		
		if ($this->isEnabled() === true){
			$this->handlingCost = $this->getConfigData('MODULE_ORDER_SHIPPING_USPS_HANDLING');
			$this->useServer = $this->getConfigData('MODULE_ORDER_SHIPPING_USPS_SERVER');
			$this->userId = $this->getConfigData('MODULE_ORDER_SHIPPING_USPS_USERID');
			$this->userPass = $this->getConfigData('MODULE_ORDER_SHIPPING_USPS_PASSWORD');
			$this->selectedTypes = $this->getConfigData('MODULE_ORDER_SHIPPING_USPS_TYPES');
			$this->selectedIntlTypes = $this->getConfigData('MODULE_ORDER_SHIPPING_USPS_INTL_TYPES');

			$this->nationalTitle = $this->getConfigData('MODULE_ORDER_SHIPPING_USPS_NATIONAL_ERROR_DESC');
			$this->nationalCost = $this->getConfigData('MODULE_ORDER_SHIPPING_USPS_NATIONAL_ERROR_COST');
			$this->intnationalTitle = $this->getConfigData('MODULE_ORDER_SHIPPING_USPS_INTNATIONAL_ERROR_DESC');
			$this->intnationalCost = $this->getConfigData('MODULE_ORDER_SHIPPING_USPS_INTNATIONAL_ERROR_COST');

			$this->types = array('EXPRESS' => 'Express Mail',
			        'FIRST CLASS' => 'First-Class Mail',
			        'PRIORITY' => 'Priority Mail',
			        'PARCEL' => 'Parcel Post',
			        'MEDIA' => 'Media Mail',
			        'BPM' => 'Bound Printed Matter',
			        'LIBRARY' => 'Library'
			        );

			$this->intl_types = array(
			        'Global Express' => 'Global Express Guaranteed&lt;sup&gt;&amp;reg;&lt;/sup&gt; (GXG)**',
			        'Global Express Non-Doc Rect' => 'Global Express Guaranteed&lt;sup&gt;&amp;reg;&lt;/sup&gt; Non-Document Rectangular',
			        'Global Express Non-Doc Non-Rect' => 'Global Express Guaranteed&lt;sup&gt;&amp;reg;&lt;/sup&gt; Non-Document Non-Rectangular',
			        'Global Express Envelopes' => 'USPS GXG&lt;sup&gt;&amp;trade;&lt;/sup&gt; Envelopes**',
			        'Express Mail Int' => 'Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International',
			        'Express Mail Int Flat Rate Env' => 'Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Flat Rate Envelope',
			        'Priority Mail International' => 'Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International',
			        'Priority Mail Int Flat Rate Env' => 'Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Flat Rate Envelope**',
			        'Priority Mail Int Flat Rate Small Box' => 'Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Small Flat Rate Box**',
			        'Priority Mail Int Flat Rate Med Box' => 'Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Medium Flat Rate Box',
			        'Priority Mail Int Flat Rate Lrg Box' => 'Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Large Flat Rate Box',
			        'First Class Mail Int Lrg Env' => 'First-Class Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Large Envelope**',
			        'First Class Mail Int Package' => 'First-Class Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Package**'
			);


			$this->countries = $this->country_list();
		}
		$isEnabled = true;
		EventManager::notify('ShippingMethodCheckBeforeConstruct', &$isEnabled);
		$this->setEnabled($isEnabled);
	}
	
	public function quote($method = ''){
		global $shipping_weight, $shipping_num_boxes;

		if ($method != ''){
			$this->_setService(urldecode($method));
		}

		$usps_shipping_weight = ($shipping_weight <= 0.0 ? 0.0625 : $shipping_weight);
		$shipping_pounds = floor ($usps_shipping_weight);
		$shipping_ounces = (16 * ($usps_shipping_weight - floor($usps_shipping_weight)));
		$shipping_ounces = number_format($shipping_ounces, 3);

		switch(true) {
		  case ($shipping_pounds == 0 and $shipping_ounces < 6):
		  // override admin choice too light
		  $is_machinable = 'False';
		  break;

		  case ($usps_shipping_weight > 35):
		  // override admin choice too heavy
		  $is_machinable = 'False';
		  break;

		  default:
		  // admin choice on what to use
		  $is_machinable = 'False';
		}

		$this->_setMachinable($is_machinable);
		$this->_setContainer('None');
		$this->_setSize('REGULAR');
		$this->_setFirstClassType('FLAT');

		$this->_setWeight($shipping_pounds, $shipping_ounces);
		$uspsQuote = $this->_getQuote();

		$this->quotes = array(
			'id'      => $this->getCode(),
			'module'  => $this->getTitle(),
			'methods' => array(),
			'icon'    => tep_image(DIR_WS_ICONS . 'shipping_usps.gif', $this->getTitle())
		);
		
		if (is_array($uspsQuote)){
			if (isset($uspsQuote['error'])){
				$this->quotes['error'] = $uspsQuote['error'];
			}else{
				$this->quotes['module'] .= ' (' . $shipping_num_boxes . ' x ' . $shipping_weight . 'lbs)';

				foreach($uspsQuote as $qInfo){
					$methodName = $qInfo['method'];
					
					$methodShow = (isset($this->types[$methodName]) ? $this->types[$methodName] : $methodName);
					if (isset($qInfo['time'])){
						$methodShow .= '<br><small><i> - Est. Delivery: ' . $qInfo['time'] . '</i></small>';
					}
					
					$this->quotes['methods'][] = array(
						'id'    => urlencode($methodName),
						'title' => html_entity_decode(htmlspecialchars_decode($methodShow)),
						'cost'  => ($qInfo['cost'] + $this->handlingCost) * $shipping_num_boxes
					);
				}

				$classId = $this->getTaxClass();
				if ($classId > 0){
					$deliveryAddress = $this->getDeliveryAddress();
					$this->quotes['tax'] = tep_get_tax_rate($classId, $deliveryAddress['entry_country_id'], $deliveryAddress['entry_zone_id']);
				}
			}
		}else{
			$deliveryAddress = $this->getDeliveryAddress();
			if($deliveryAddress['entry_country_id'] == sysConfig::get('STORE_COUNTRY')){
				$this->quotes['methods'][] = array(
					'id'    => 'usps_error_national',
					'title' => $this->nationalTitle,
					'cost'  => $this->nationalCost
				);
			}else{
				$this->quotes['methods'][] = array(
					'id'    => 'usps_error_intnational',
					'title' => $this->intnationalTitle,
					'cost'  => $this->intnationalCost
				);
			}
			//$this->quotes['error'] = sysLanguage::get('MODULE_ORDER_SHIPPING_USPS_TEXT_ERROR');
		}

		return $this->quotes;
	}
	public function _setService($service) {
		$this->service = $service;
	}

	public function _setWeight($pounds, $ounces=0) {
		$this->pounds = $pounds;
		$this->ounces = $ounces;
	}

	public function _setContainer($container) {
		$this->container = $container;
	}

	public function _setSize($size) {
		$this->size = $size;
	}

	function _setFirstClassType($fctype) {
	  $this->fctype = $fctype;
	}

	public function _setMachinable($machinable) {
		$this->machinable = $machinable;
	}

	function _getQuote() {
		global $order;
		$deliveryAddress = $this->getDeliveryAddress();
		$deliveryCountry = OrderShippingModules::getCountryInfo($deliveryAddress['entry_country_id']);
			//print_r($deliveryCountry);
		if ($deliveryAddress['entry_country_id'] == sysConfig::get('SHIPPING_ORIGIN_COUNTRY')) {
			$request  = '<RateV3Request USERID="' . $this->userId . '" PASSWORD="' . $this->userPass . '">';
			$services_count = 0;

			if (isset($this->service)) {
				$this->types = array($this->service => $this->types[$this->service]);
			}

			$dest_zip = str_replace(' ', '', $deliveryAddress['entry_postcode']);
			if ($deliveryCountry['countries_iso_code_2'] == 'US') $dest_zip = substr($dest_zip, 0, 5);

			reset($this->types);
			$allowed_types = explode(',', $this->selectedTypes);
		  while (list($key, $value) = each($this->types)) {

			if ( !in_array($key, $allowed_types) ) continue;
			  if ($key == 'FIRST CLASS') {
				$this->FirstClassMailType = '<FirstClassMailType>LETTER</FirstClassMailType>';
			  } else {
				$this->FirstClassMailType = '';
			  }

			  if ($key == 'PRIORITY'){
				$this->container = ''; // Blank, Flate Rate Envelope, or Flat Rate Box // Sm Flat Rate Box, Md Flat Rate Box and Lg Flat Rate Box

			  }

			  if ($key == 'EXPRESS'){
				$this->container = '';  // Blank, or Flate Rate Envelope
			  }

			  if ($key == 'PARCEL'){
				$this->container = 'Regular';
				$this->machinable = 'true';
			  }
			$request .= '<Package ID="' . $services_count . '">' .
			'<Service>' . $key . '</Service>' .
			'<FirstClassMailType>' . $this->fctype . '</FirstClassMailType>' .
			'<ZipOrigination>' . sysConfig::get('SHIPPING_ORIGIN_ZIP') . '</ZipOrigination>' .
			'<ZipDestination>' . $dest_zip . '</ZipDestination>' .
			'<Pounds>' . $this->pounds . '</Pounds>' .
			'<Ounces>' . $this->ounces . '</Ounces>' .
			'<Container>' . $this->container . '</Container>' .
			'<Size>' . $this->size . '</Size>' .
			'<Machinable>' . $this->machinable . '</Machinable>' .
			'</Package>';

			$services_count++;
		  }
		  $request .= '</RateV3Request>';

		  $request = 'API=RateV3&XML=' . urlencode($request);
		} else {
		  $request  = '<IntlRateRequest USERID="' . $this->userId . '">' .
		  '<Package ID="0">' .
		  '<Pounds>' . $this->pounds . '</Pounds>' .
		  '<Ounces>' . $this->ounces . '</Ounces>' .
		  '<MailType>Package</MailType>' .
		  '<Country>' . $this->countries[$deliveryCountry['countries_iso_code_2']] . '</Country>' .
		  '</Package>' .
		  '</IntlRateRequest>';

		  $request = 'API=IntlRate&XML=' . urlencode($request);
		}

		switch ($this->useServer) {
		  case 'production':
		  $usps_server = 'production.shippingapis.com';
		  $api_dll = 'shippingapi.dll';
		  break;
		  case 'test':
		  default:
		  $usps_server = 'testing.shippingapis.com';
		  $api_dll = 'ShippingAPI.dll';
		  break;
		}

		$body = '';

		$http = new httpClient();
		$http->timeout = 5;
		if ($http->Connect($usps_server, 80)) {
		  $http->addHeader('Host', $usps_server);
		  $http->addHeader('User-Agent', 'SES2.0');
		  $http->addHeader('Connection', 'Close');
		  if ($http->Get('/' . $api_dll . '?' . $request)) $body = $http->getBody();
		  $http->Disconnect();
		} else {
		  return -1;
		}

		$response = array();
		while (true) {
		  if ($start = strpos($body, '<Package ID=')) {
			$body = substr($body, $start);
			$end = strpos($body, '</Package>');
			$response[] = substr($body, 0, $end+10);
			$body = substr($body, $end+9);
		  } else {
			break;
		  }
		}

		$rates = array();

		if ($deliveryAddress['entry_country_id'] == sysConfig::get('SHIPPING_ORIGIN_COUNTRY') && $deliveryCountry['countries_iso_code_2'] == 'US') {
		  if (sizeof($response) == '1') {
			if (preg_match('/<Error>/i', $response[0])) {
			  $number = preg_match('/<Number>(.*)<\/Number>/msi', $response[0], $regs);
			  $number = $regs[1];
			  $description = preg_match('/<Description>(.*)<\/Description>/msi', $response[0], $regs);
			  $description = $regs[1];

			  return array('error' => $number . ' - ' . $description);
			}
		  }

		  $n = sizeof($response);
		  for ($i=0; $i<$n; $i++) {
			if (strpos($response[$i], '<Rate>')) {
			  $service = preg_match('/<MailService>(.*)<\/MailService>/msi', $response[$i], $regs);
			  $service = $regs[1];
			  if (preg_match('/Express/i', $service)) $service = 'EXPRESS';
			  if (preg_match('/Priority/i', $service)) $service = 'PRIORITY';
			  if (preg_match('/First-Class Mail/i', $service)) $service = 'FIRST CLASS';
			  if (preg_match('/Parcel/i', $service)) $service = 'PARCEL';
			  if (preg_match('/Media/i', $service)) $service = 'MEDIA';
			  if (preg_match('/Bound Printed/i', $service)) $service = 'BPM';
			  if (preg_match('/Library/i', $service)) $service = 'LIBRARY';
			  $postage = preg_match('/<Rate>(.*)<\/Rate>/msi', $response[$i], $regs);
			  $postage = $regs[1];

			  $rates[] = array('method' => $service, 'cost' => $postage);
			}
		  }
		} else {
		  if (preg_match('/<Error>/i', $response[0])) {
			$number = preg_match('/<Number>(.*)<\/Number>/msi', $response[0], $regs);
			$number = $regs[1];
			$description = preg_match('/<Description>(.*)<\/Description>/msi', $response[0], $regs);
			$description = $regs[1];

			return array('error' => $number . ' - ' . $description);
		  } else {
			$body = $response[0];
			$services = array();
			while (true) {
			  if ($start = strpos($body, '<Service ID=')) {
				$body = substr($body, $start);
				$end = strpos($body, '</Service>');
				$services[] = substr($body, 0, $end+10);
				$body = substr($body, $end+9);
			  } else {
				break;
			  }
			}

			$allowed_types = array();
			foreach( explode(',', $this->selectedIntlTypes) as $value ){
				$allowed_types[$value] = htmlspecialchars($this->intl_types[$value]);
			}

			$size = sizeof($services);
			for ($i=0, $n=$size; $i<$n; $i++) {
			  if (strpos($services[$i], '<Postage>')) {
				$service = preg_match('/<SvcDescription>(.*)<\/SvcDescription>/msi', $services[$i], $regs);
				$service = $regs[1];
				$postage = preg_match('/<Postage>(.*)<\/Postage>/i', $services[$i], $regs);
				$postage = $regs[1];
				$time = preg_match('/<SvcCommitments>(.*)<\/SvcCommitments>/msi', $services[$i], $tregs);
				$time = $tregs[1];
				$time = preg_replace('/Weeks$/', 'Weeks', $time);
				$time = preg_replace('/Days$/', 'Days', $time);
				$time = preg_replace('/Day$/', 'Day', $time);

				if( !in_array($service, $allowed_types) ) continue;
				if ($order->info['total'] > 400 && strstr($services[$i], 'Priority Mail International Flat Rate Envelope')) continue; // skip value > $400 Priority Mail International Flat Rate Envelope
				if (isset($this->service) && ($service != $this->service) ) {
				  continue;
				}

				$rates[] = array('method' => $service, 'cost' => $postage, 'time'=> $time);
			  }
			}
		  }
		}

		return ((sizeof($rates) > 0) ? $rates : false);
	}

	public function country_list() {
		$list = array(
			'AF' => 'Afghanistan',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AG' => 'Antigua and Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BA' => 'Bosnia-Herzegovina',
			'BW' => 'Botswana',
			'BR' => 'Brazil',
			'VG' => 'British Virgin Islands',
			'BN' => 'Brunei Darussalam',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'MM' => 'Burma',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island (Australia)',
			'CC' => 'Cocos Island (Australia)',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CG' => 'Congo (Brazzaville),Republic of the',
			'ZR' => 'Congo, Democratic Republic of the',
			'CK' => 'Cook Islands (New Zealand)',
			'CR' => 'Costa Rica',
			'CI' => 'Cote d\'Ivoire (Ivory Coast)',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'TP' => 'East Timor (Indonesia)',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia, Republic of',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GB' => 'Great Britain and Northern Ireland',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GT' => 'Guatemala',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Laos',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia, Republic of',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte (France)',
			'MX' => 'Mexico',
			'MD' => 'Moldova',
			'MC' => 'Monaco (France)',
			'MN' => 'Mongolia',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'AN' => 'Netherlands Antilles',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'KP' => 'North Korea (Korea, Democratic People\'s Republic of)',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn Island',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'QA' => 'Qatar',
			'RE' => 'Reunion',
			'RO' => 'Romania',
			'RU' => 'Russia',
			'RW' => 'Rwanda',
			'SH' => 'Saint Helena',
			'KN' => 'Saint Kitts (St. Christopher and Nevis)',
			'LC' => 'Saint Lucia',
			'PM' => 'Saint Pierre and Miquelon',
			'VC' => 'Saint Vincent and the Grenadines',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome and Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'YU' => 'Serbia-Montenegro',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SK' => 'Slovak Republic',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia (Falkland Islands)',
			'KR' => 'South Korea (Korea, Republic of)',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syrian Arab Republic',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TG' => 'Togo',
			'TK' => 'Tokelau (Union) Group (Western Samoa)',
			'TO' => 'Tonga',
			'TT' => 'Trinidad and Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks and Caicos Islands',
			'TV' => 'Tuvalu',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VA' => 'Vatican City',
			'VE' => 'Venezuela',
			'VN' => 'Vietnam',
			'WF' => 'Wallis and Futuna Islands',
			'WS' => 'Western Samoa',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe'
		);

		return $list;
	}
}
?>