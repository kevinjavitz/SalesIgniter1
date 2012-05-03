<?php

$GEOIP_COUNTRY_NAMES = array(
	"", "Asia/Pacific Region", "Europe", "Andorra", "United Arab Emirates",
	"Afghanistan", "Antigua and Barbuda", "Anguilla", "Albania", "Armenia",
	"Netherlands Antilles", "Angola", "Antarctica", "Argentina", "American Samoa",
	"Austria", "Australia", "Aruba", "Azerbaijan", "Bosnia and Herzegovina",
	"Barbados", "Bangladesh", "Belgium", "Burkina Faso", "Bulgaria", "Bahrain",
	"Burundi", "Benin", "Bermuda", "Brunei Darussalam", "Bolivia", "Brazil",
	"Bahamas", "Bhutan", "Bouvet Island", "Botswana", "Belarus", "Belize",
	"Canada", "Cocos (Keeling) Islands", "Congo, The Democratic Republic of the",
	"Central African Republic", "Congo", "Switzerland", "Cote D'Ivoire", "Cook Islands",
	"Chile", "Cameroon", "China", "Colombia", "Costa Rica", "Cuba", "Cape Verde",
	"Christmas Island", "Cyprus", "Czech Republic", "Germany", "Djibouti",
	"Denmark", "Dominica", "Dominican Republic", "Algeria", "Ecuador", "Estonia",
	"Egypt", "Western Sahara", "Eritrea", "Spain", "Ethiopia", "Finland", "Fiji",
	"Falkland Islands (Malvinas)", "Micronesia, Federated States of", "Faroe Islands",
	"France", "France, Metropolitan", "Gabon", "United Kingdom",
	"Grenada", "Georgia", "French Guiana", "Ghana", "Gibraltar", "Greenland",
	"Gambia", "Guinea", "Guadeloupe", "Equatorial Guinea", "Greece", "South Georgia and the South Sandwich Islands",
	"Guatemala", "Guam", "Guinea-Bissau",
	"Guyana", "Hong Kong", "Heard Island and McDonald Islands", "Honduras",
	"Croatia", "Haiti", "Hungary", "Indonesia", "Ireland", "Israel", "India",
	"British Indian Ocean Territory", "Iraq", "Iran, Islamic Republic of",
	"Iceland", "Italy", "Jamaica", "Jordan", "Japan", "Kenya", "Kyrgyzstan",
	"Cambodia", "Kiribati", "Comoros", "Saint Kitts and Nevis", "Korea, Democratic People's Republic of",
	"Korea, Republic of", "Kuwait", "Cayman Islands",
	"Kazakhstan", "Lao People's Democratic Republic", "Lebanon", "Saint Lucia",
	"Liechtenstein", "Sri Lanka", "Liberia", "Lesotho", "Lithuania", "Luxembourg",
	"Latvia", "Libyan Arab Jamahiriya", "Morocco", "Monaco", "Moldova, Republic of",
	"Madagascar", "Marshall Islands", "Macedonia",
	"Mali", "Myanmar", "Mongolia", "Macau", "Northern Mariana Islands",
	"Martinique", "Mauritania", "Montserrat", "Malta", "Mauritius", "Maldives",
	"Malawi", "Mexico", "Malaysia", "Mozambique", "Namibia", "New Caledonia",
	"Niger", "Norfolk Island", "Nigeria", "Nicaragua", "Netherlands", "Norway",
	"Nepal", "Nauru", "Niue", "New Zealand", "Oman", "Panama", "Peru", "French Polynesia",
	"Papua New Guinea", "Philippines", "Pakistan", "Poland", "Saint Pierre and Miquelon",
	"Pitcairn Islands", "Puerto Rico", "Palestinian Territory",
	"Portugal", "Palau", "Paraguay", "Qatar", "Reunion", "Romania",
	"Russian Federation", "Rwanda", "Saudi Arabia", "Solomon Islands",
	"Seychelles", "Sudan", "Sweden", "Singapore", "Saint Helena", "Slovenia",
	"Svalbard and Jan Mayen", "Slovakia", "Sierra Leone", "San Marino", "Senegal",
	"Somalia", "Suriname", "Sao Tome and Principe", "El Salvador", "Syrian Arab Republic",
	"Swaziland", "Turks and Caicos Islands", "Chad", "French Southern Territories",
	"Togo", "Thailand", "Tajikistan", "Tokelau", "Turkmenistan",
	"Tunisia", "Tonga", "Timor-Leste", "Turkey", "Trinidad and Tobago", "Tuvalu",
	"Taiwan", "Tanzania, United Republic of", "Ukraine",
	"Uganda", "United States Minor Outlying Islands", "United States", "Uruguay",
	"Uzbekistan", "Holy See (Vatican City State)", "Saint Vincent and the Grenadines",
	"Venezuela", "Virgin Islands, British", "Virgin Islands, U.S.",
	"Vietnam", "Vanuatu", "Wallis and Futuna", "Samoa", "Yemen", "Mayotte",
	"Serbia", "South Africa", "Zambia", "Montenegro", "Zimbabwe",
	"Anonymous Proxy","Satellite Provider","Other",
	"Aland Islands","Guernsey","Isle of Man","Jersey","Saint Barthelemy","Saint Martin"
);
	if (isset($_GET['sID'])){
		$Qstore = Doctrine_Core::getTable('Stores')->findOneByStoresId((int)$_GET['sID']);
	}

	/* Build all store info inputs that are needed --BEGIN-- */
	$storeName = htmlBase::newElement('input')->setName('stores_name');
	$storeDomain = htmlBase::newElement('input')->setName('stores_domain');
	$storeSslDomain = htmlBase::newElement('input')->setName('stores_ssl_domain');
	$storeEmail = htmlBase::newElement('input')->setName('stores_email');
	$storeZip = htmlBase::newElement('input')->setName('stores_zip');
	$storeLocation = htmlBase::newElement('input')->setName('stores_location');
	$storeTelephone = htmlBase::newElement('input')->setName('stores_telephone');
	$storeGroup = htmlBase::newElement('input')->setName('stores_group');
	$storeOwner = htmlBase::newElement('input')->setName('stores_owner');
	$isDefault = htmlBase::newElement('checkbox')->setName('is_default');
	$homeRedirect = htmlBase::newElement('checkbox')->setName('home_redirect_store_info');
	$defaultCurrency = htmlBase::newElement('selectbox')->setName('default_currency');
	$defaultLanguage = htmlBase::newElement('selectbox')->setName('default_language');
	$storeInfo = htmlBase::newElement('ck_editor')->setName('stores_info')->attr('rows','20')->attr('cols','90');

$table = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->css('width', '100%');

$table->addHeaderRow(array(
		'columns' => array(
			array('attr' => array('width' => '40%'), 'text' => 'Countries'),
			array('text' => '&nbsp;'),
			array('attr' => array('width' => '30%'), 'text' => 'Selected Countries')
		)
	));

$storeCountries = '';
$countryList = '';

foreach($GEOIP_COUNTRY_NAMES as $aCountry){
	if($aCountry != ''){
		$countryList .= '<option value="'.$aCountry.'">'.$aCountry.'</option>';
	}
}

if (isset($Qstore) && !empty($Qstore['stores_countries'])){
	$countries = explode(',', $Qstore['stores_countries']);
	foreach($countries as $cID){
		$storeCountries .= '<div><a href="#" class="ui-icon ui-icon-circle-close removeButton"></a><span class="main">' . $cID . '</span>' . tep_draw_hidden_field('stores_countries[]', $cID) . '</div>';
	}
}

$table->addBodyRow(array(
		'columns' => array(
			array(
				'addCls' => 'main',
				'attr' => array(
					'valign' => 'top'
				),
				'text' => '<select size="30" style="width:100%;" id="countryList">' . $countryList . '</select>'
			),
			array(
				'addCls' => 'main',
				'text' => '<button type="button" id="moveRight"><span>&nbsp;&nbsp;>>&nbsp;&nbsp;</span></button>'
			),
			array(
				'addCls' => 'main',
				'attr' => array(
					'id' => 'countries',
					'valign' => 'top'
				),
				'text' => $storeCountries
			)
		)
	));
			
	if (isset($Qstore)){
		$storeName->setValue($Qstore['stores_name']);
		$storeDomain->setValue($Qstore['stores_domain']);
		$storeSslDomain->setValue($Qstore['stores_ssl_domain']);
		$storeEmail->setValue($Qstore['stores_email']);
		$storeZip->setValue($Qstore['stores_zip']);
		$storeLocation->setValue($Qstore['stores_location']);
		$storeTelephone->setValue($Qstore['stores_telephone']);
		$storeGroup->setValue($Qstore['stores_group']);
		$storeInfo->html($Qstore['stores_info']);
		$storeOwner->setValue($Qstore['stores_owner']);
		$isDefault->setChecked($Qstore['is_default'] == '1'?true:false);
		$homeRedirect->setChecked($Qstore['home_redirect_store_info'] == '1'?true:false);

		$defaultCurrency->selectOptionByValue($Qstore['default_currency']);
		$defaultLanguage->selectOptionByValue($Qstore['default_language']);
	}

	$QCurrencies = Doctrine_Query::create()
	->from('CurrenciesTable')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	foreach($QCurrencies as $currency){
		$defaultCurrency->addOption($currency['code'], $currency['title']);
	}

	$QLanguages = Doctrine_Query::create()
		->from('Languages')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	foreach($QLanguages as $language){
		$defaultLanguage->addOption($language['code'], $language['name']);
	}

	$templatesSet = htmlBase::newElement('selectbox')->setName('stores_template');
	$dir = new DirectoryIterator(DIR_FS_CATALOG . 'templates/');
	$ignoreTemplates = array('email', 'help', 'help-text');
	$templatesArray = array();
	foreach($dir as $fileObj){
		if ($fileObj->isDot() || $fileObj->isDir() === false) continue;
		if (in_array(strtolower($fileObj->getBasename()), $ignoreTemplates)) continue;

		$templatesSet->addOption($fileObj->getBasename(), ucfirst($fileObj->getBasename()));
	}
	
	if (isset($Qstore)){
		$templatesSet->selectOptionByValue($Qstore['stores_template']);
	}
	/* Build all store info inputs that are needed --END-- */

	/* Build all categories inputs that are needed --BEGIN-- */
	$checkedCats = array();
	if (isset($Qstore)){
		$Qcategories = Doctrine_Query::create()
		->select('categories_id')
		->from('CategoriesToStores')
		->where('stores_id = ?', $Qstore['stores_id'])
		->execute();
		if ($Qcategories){
			foreach($Qcategories->toArray() as $cInfo){
				$checkedCats[] = $cInfo['categories_id'];
			}
		}
	}
	$categoriesList = tep_get_category_tree_list('0', $checkedCats);
	/* Build all categories inputs that are needed --END-- */
	
	/* Build the store info table --BEGIN-- */
	$storeInfoTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);

	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_NAME')),
			array('addCls' => 'main','text' => $storeName->draw())
		)
	));

	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_DOMAIN')),
			array('addCls' => 'main','text' => $storeDomain->draw())
		)
	));

	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_SSL_DOMAIN')),
			array('addCls' => 'main','text' => $storeSslDomain->draw())
		)
	));

/* Auto Upgrade ( Version 1.0 to 1.1 ) --BEGIN-- */
	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_OWNER')),
			array('addCls' => 'main','text' => $storeOwner->draw())
		)
	));
/* Auto Upgrade ( Version 1.0 to 1.1 ) --END-- */

	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_EMAIL')),
			array('addCls' => 'main','text' => $storeEmail->draw())
		)
	));

	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_TEMPLATE')),
			array('addCls' => 'main','text' => $templatesSet->draw())
		)
	));
	
	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_ZIP')),
			array('addCls' => 'main','text' => $storeZip->draw())
		)
	));
	
	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_LOCATION')),
			array('addCls' => 'main','text' => $storeLocation->draw())
		)
	));
	$storeInfoTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_TELEPHONE')),
				array('addCls' => 'main','text' => $storeTelephone->draw())
			)
		));
	$storeInfoTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_GROUP')),
				array('addCls' => 'main','text' => $storeGroup->draw())
			)
		));
	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_DESCRIPTION')),
			array('addCls' => 'main','text' => $storeInfo->draw())
		)
	));
	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_DEFAULT_CURRENCY')),
			array('addCls' => 'main','text' => $defaultCurrency->draw())
		)
	));
	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_DEFAULT_LANGUAGE')),
			array('addCls' => 'main','text' => $defaultLanguage->draw())
		)
	));
	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_IS_DEFAULT')),
			array('addCls' => 'main','text' => $isDefault->draw())
		)
	));
	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_HOME_REDIRECT_STORE_INFO')),
			array('addCls' => 'main','text' => $homeRedirect->draw())
		)
	));
	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_COUNTRIES')),
			array('addCls' => 'main','text' => $table->draw())
		)
	));
/* Build the store info table --END-- */

	/* Build the tabbed interface --BEGIN-- */
	$tabsObj = htmlBase::newElement('tabs')
	->setId('storeTabs')
	->addTabHeader('tab_store_info', array('text' => 'Store Info'))
	->addTabPage('tab_store_info', array('text' => $storeInfoTable->draw()))
	->addTabHeader('tab_categories', array('text' => 'Categories'))
	->addTabPage('tab_categories', array('text' => /*'<div style="color:red;">Note: All products inside the categories will be added to this store also.</div><br />' . */$categoriesList));
	/* Build the tabbed interface --END-- */
	
	EventManager::notify('NewStoreAddTab', &$tabsObj);
   
	$saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
	->setHref(itw_app_link(tep_get_all_get_params(array('action')), null, 'default'));

	$buttonContainer = new htmlElement('div');
	$buttonContainer->append($saveButton)->append($cancelButton)->css(array(
		'float' => 'right',
		'width' => 'auto'
	))->addClass('ui-widget');
	
	$pageForm = htmlBase::newElement('form')
	->attr('name', 'new_store')
	->attr('action', itw_app_link(tep_get_all_get_params(array('action')) . 'action=save'))
	->attr('enctype', 'multipart/form-data')
	->attr('method', 'post')
	->html($tabsObj->draw() . '<br />' . $buttonContainer->draw());
	
	$headingTitle = htmlBase::newElement('div')
	->addClass('pageHeading')
	->html(sysLanguage::get('HEADING_TITLE'));
	
	echo $headingTitle->draw() . '<br />' . $pageForm->draw();
?>