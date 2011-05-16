<?php
class OrderShippingUsps extends OrderShippingModule {

	public function __construct(){
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
			
			$this->types = array(
				'EXPRESS'     => 'Express Mail',
				'FIRST CLASS' => 'First-Class Mail',
				'PRIORITY'    => 'Priority Mail',
				'PARCEL'      => 'Parcel Post'
			);

			$this->intl_types = array(
				'GXG DOCUMENT'     => 'Global Express Guaranteed Document Service',
				'GXG NON-DOCUMENT' => 'Global Express Guaranteed Non-Document Service',
				'EXPRESS'          => 'Global Express Mail (EMS)',
				'PRIORITY LG'      => 'Global Priority Mail - Flat-rate Envelope (large)',
				'PRIORITY SM'      => 'Global Priority Mail - Flat-rate Envelope (small)',
				'PRIORITY VAR'     => 'Global Priority Mail - Variable Weight Envelope (single)',
				'AIRMAIL LETTER'   => 'Airmail Letter Post',
				'AIRMAIL PARCEL'   => 'Airmail Parcel Post',
				'SURFACE LETTER'   => 'Economy (Surface) Letter Post',
				'SURFACE POST'     => 'Economy (Surface) Parcel Post'
			);

			$this->countries = $this->country_list();
		}
	}
	
	public function quote($method = ''){
		global $shipping_weight, $shipping_num_boxes;

		if ($method != ''){
			$this->_setService(urldecode($method));
		}

		$this->_setMachinable('False');
		$this->_setContainer('None');
		$this->_setSize('REGULAR');

		// usps doesnt accept zero weight
		//$shipping_weight = ($shipping_weight < 0.1 ? 0.1 : $shipping_weight);
		$shipping_pounds = floor ($shipping_weight);
		$shipping_ounces = round(16 * ($shipping_weight - floor($shipping_weight)));
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
						'title' => $methodShow,
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
			$this->quotes['error'] = sysLanguage::get('MODULE_ORDER_SHIPPING_USPS_TEXT_ERROR');
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

	public function _setMachinable($machinable) {
		$this->machinable = $machinable;
	}

	public function _getQuote() {
		$deliveryAddress = $this->getDeliveryAddress();
		$deliveryCountry = OrderShippingModules::getCountryInfo($deliveryAddress['entry_country_id']);
		//print_r($deliveryCountry);
		if ($deliveryAddress['entry_country_id'] == sysConfig::get('SHIPPING_ORIGIN_COUNTRY')) {
			$request  = '<RateRequest USERID="' . $this->userId . '" PASSWORD="' . $this->userPass . '">';
			$services_count = 0;

			if (isset($this->service)) {
				$this->types = array($this->service => $this->types[$this->service]);
			}

			$dest_zip = str_replace(' ', '', $deliveryAddress['entry_postcode']);
			if ($deliveryCountry['countries_iso_code_2'] == 'US') $dest_zip = substr($dest_zip, 0, 5);

			reset($this->types);
			while (list($key, $value) = each($this->types)) {
				$request .= '<Package ID="' . $services_count . '">' .
					'<Service>' . $key . '</Service>' .
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
			$request .= '</RateRequest>';

			$request = 'API=Rate&XML=' . urlencode($request);
		} else {
			$request  = '<IntlRateRequest USERID="' . $this->userId . '" PASSWORD="' . $this->userPass . '">' .
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
			case 'Production':
				$usps_server = 'production.shippingapis.com';
				$api_dll = 'shippingapi.dll';
				break;
			case 'Test':
			default:
				$usps_server = 'testing.shippingapis.com';
				$api_dll = 'ShippingAPITest.dll';
				break;
		}

		$body = '';

		$http = new httpClient();
		if ($http->Connect($usps_server, 80)) {
			$http->addHeader('Host', $usps_server);
			$http->addHeader('User-Agent', 'SalesIgniter');
			$http->addHeader('Connection', 'Close');

			if ($http->Get('/' . $api_dll . '?' . $request)) $body = $http->getBody();

			$http->Disconnect();
		} else {
			return false;
		}
		$Response = simplexml_load_string(
			$body,
			'SimpleXMLElement',
			LIBXML_NOCDATA
		);

		$ResponseInfo = $Response;
		
		$rates = array();
		if (isset($ResponseInfo->Error)){
			return array(
				'error' => (string) $ResponseInfo->Error->Number . ' - ' . (string) $ResponseInfo->Error->Description,
			);
		}else{
			if ($deliveryAddress['entry_country_id'] == sysConfig::get('SHIPPING_ORIGIN_COUNTRY')) {
				foreach($ResponseInfo->Package as $pInfo){
					if ($pInfo->Error){
						//$rates[] = array(
						//	(string) $pInfo->Service => (string) $pInfo->Error->Description
						//);
					}else{
						$rateInfo = array(
							'method' => (string) $pInfo->Service,
							'cost'   => (float) $pInfo->Postage
						);
						
						/*
						 * @TODO: figure out how to get delivery time for in-country shipments
						 */
						if ($pInfo->SvcCommitments){
							$rateInfo['time'] = (string) $pInfo->SvcCommitments;
						}
						
						$rates[] = $rateInfo;
					}
				}
			} else {
				foreach($Response->Package as $pInfo){
					foreach($pInfo->Service as $sInfo){
						if (isset($this->service) && ((string) $sInfo->SvcDescription != $this->service) ) {
							continue;
						}

						$rateInfo = array(
							'method' => (string) $sInfo->SvcDescription,
							'cost' => (float) $sInfo->Postage
						);
						
						if ($sInfo->SvcCommitments){
							$rateInfo['time'] = (string) $sInfo->SvcCommitments;
						}
						
						$rates[] = $rateInfo;
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