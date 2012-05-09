<?php
/*
 * Sales Igniter E-Commerce System
 * Version: 2.0
 *
 * I.T. Web Experts
 * http://www.itwebexperts.com
 *
 * Copyright (c) 2011 I.T. Web Experts
 *
 * This script and its source are not distributable without the written conscent of I.T. Web Experts
 */

function itwExit(){
	Session::stop();
	exit;
}

//Admin begin
////
//Check login
function tep_admin_check_login() {
	global $navigation, $App;
	if (Session::exists('login_id') === false) {
		$navigation->set_snapshot();
		tep_redirect(itw_app_link(null, 'login', 'default', 'SSL'));
	}
}

function tep_cfg_get_shipping_methods(){
	if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'False'){
		$module = OrderShippingModules::getModule('zonereservation');
	} else{
		$module = OrderShippingModules::getModule('upsreservation');
	}
	$shippingInputs = array();
	if($module){
		$quotes = $module->quote();
		for($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++){
			$shippingInputs[] = $quotes['methods'][$i]['title'];
		}
	}
    return $shippingInputs;
}

function tep_array_to_string($array, $exclude = '', $equals = '=', $separator = '&') {
    if (!is_array($exclude)) $exclude = array();

    $get_string = '';
    if (sizeof($array) > 0) {
      while (list($key, $value) = each($array)) {
        if ( (!in_array($key, $exclude)) && ($key != 'x') && ($key != 'y') ) {
          $get_string .= $key . $equals . $value . $separator;
        }
      }
      $remove_chars = strlen($separator);
      $get_string = substr($get_string, 0, -$remove_chars);
    }

    return $get_string;
}


function tep_get_country_list($name, $selected = '', $parameters = '', $only = '')
{
	if (!$only)
	$countries_array = array(array('id' => '', 'text' => sysLanguage::get('PULL_DOWN_DEFAULT')));
	$countries = tep_get_countriesArray($only);

	if (!$only)
	{
		for ($i = 0, $n = sizeof($countries); $i < $n; $i++)
		{
			$countries_array[] = array('id' => $countries[$i]['countries_id'], 'text' => $countries[$i]['countries_name']);
		}
	}
	else
	{
		$countries_array[] = array('id' => $only, 'text' => $countries['countries_name']);
	}

	return tep_draw_pull_down_menu($name, $countries_array, $selected, $parameters);
}

function tep_get_countriesArray($countries_id = '', $with_iso_codes = false)
{
	$countries_array = array();
	if ($countries_id != '')
	{
		if ($with_iso_codes == true)
		{
			$query = "select countries_name, countries_iso_code_2, countries_iso_code_3 from countries where countries_id = '" . (int)$countries_id . "' order by countries_name";
		}
		else
		{
			$query = "select countries_name from countries where countries_id = '" . (int)$countries_id . "'";
		}
	}
	else
	{
		$query = "select countries_id, countries_name from countries order by countries_name";
	}
	
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc($query);

	return $ResultSet;
}


////
// Redirect to another page or site
function tep_redirect($url) {
	global $logger;

	if ( (strstr($url, "\n") != false) || (strstr($url, "\r") != false) ) {
		tep_redirect(tep_href_link(FILENAME_DEFAULT, '', 'NONSSL', false));
	}

	header('Location: ' . $url);

	if (sysConfig::get('STORE_PAGE_PARSE_TIME') == 'true') {
		if (!is_object($logger)) $logger = new logger;
		$logger->timer_stop();
	}

	itwExit();
}

////
// Parse the data used in the html tags to ensure the tags will not break
function tep_parse_input_field_data($data, $parse) {
	return strtr(trim($data), $parse);
}

function tep_output_string($string, $translate = false, $protected = false) {
	if ($protected == true) {
		return htmlspecialchars($string);
	} else {
		if ($translate == false) {
			return tep_parse_input_field_data($string, array('"' => '&quot;'));
		} else {
			return tep_parse_input_field_data($string, $translate);
		}
	}
}

function tep_output_string_protected($string) {
	return tep_output_string($string, false, true);
}

function tep_sanitize_string($string) {
	$string = preg_replace('/ +/', ' ', $string);

	return preg_replace("/[<>]/", '_', $string);
}

function getPackageName($packName, $langName){

    $packagesNameArr = explode(';',$packName);
    $languagesArr = explode(';',$langName);
    for ($i=0, $n=sizeof($languagesArr); $i<$n; $i++) {
        if ((int)$languagesArr[$i] == (int)Session::get('languages_id')){
            return $packagesNameArr[$i];
        }
    }
    return false;
}

function tep_get_path($current_category_id = '') {
	global $cPath_array;

	if ($current_category_id == '') {
		$cPath_new = implode('_', $cPath_array);
	} else {
		if (sizeof($cPath_array) == 0) {
			$cPath_new = $current_category_id;
		} else {
			$cPath_new = '';
			$Categories = Doctrine_Core::getTable('Categories')->getRecordInstance();

			$lastParent = $Categories->getParentId((int) $cPath_array[(sizeof($cPath_array)-1)]);
			$currentParent = $Categories->getParentId((int) $current_category_id);

			if ($lastParent == $currentParent) {
				for ($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
					$cPath_new .= '_' . $cPath_array[$i];
				}
			} else {
				for ($i = 0, $n = sizeof($cPath_array); $i < $n; $i++) {
					$cPath_new .= '_' . $cPath_array[$i];
				}
			}

			$cPath_new .= '_' . $current_category_id;

			if (substr($cPath_new, 0, 1) == '_') {
				$cPath_new = substr($cPath_new, 1);
			}
		}
	}
	return 'cPath=' . $cPath_new;
}


function tep_get_all_get_params($exclude_array = '') {
	if ($exclude_array == '') $exclude_array = array();

	$get_url = '';

	reset($_GET);
	while (list($key, $value) = each($_GET)) {
		if (($key != Session::getSessionName()) && ($key != 'error') && (!in_array($key, $exclude_array))) $get_url .= strip_tags($key) . '=' . strip_tags($value) . '&';
	}

	return $get_url;
}



function tep_date_long($raw_date) {
	if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

	$year = (int)substr($raw_date, 0, 4);
	$month = (int)substr($raw_date, 5, 2);
	$day = (int)substr($raw_date, 8, 2);
	$hour = (int)substr($raw_date, 11, 2);
	$minute = (int)substr($raw_date, 14, 2);
	$second = (int)substr($raw_date, 17, 2);

	return strftime(sysLanguage::getDateFormat('long'), mktime($hour, $minute, $second, $month, $day, $year));
}

////
// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
// NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
function tep_date_short($raw_date) {
	if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') || $raw_date == '0000-00-00') return false;

	$year = substr($raw_date, 0, 4);
	$month = (int)substr($raw_date, 5, 2);
	$day = (int)substr($raw_date, 8, 2);
	$hour = (int)substr($raw_date, 11, 2);
	$minute = (int)substr($raw_date, 14, 2);
	$second = (int)substr($raw_date, 17, 2);

	if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {
		return date(sysLanguage::getDateFormat(), mktime($hour, $minute, $second, $month, $day, $year));
	} else {
		return false;
	}

}

function tep_datetime_short($raw_datetime) {
	if ( ($raw_datetime == '0000-00-00 00:00:00') || ($raw_datetime == '') ) return false;

	$year = (int)substr($raw_datetime, 0, 4);
	$month = (int)substr($raw_datetime, 5, 2);
	$day = (int)substr($raw_datetime, 8, 2);
	$hour = (int)substr($raw_datetime, 11, 2);
	$minute = (int)substr($raw_datetime, 14, 2);
	$second = (int)substr($raw_datetime, 17, 2);

	return strftime(sysLanguage::getDateTimeFormat(), mktime($hour, $minute, $second, $month, $day, $year));
}

function tep_get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
	if (!is_array($category_tree_array)) $category_tree_array = array();
	if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => sysLanguage::get('TEXT_TOP'));

	$Categories = Doctrine_Core::getTable('Categories')->getRecordInstance();
	$CategoriesDescription = Doctrine_Core::getTable('CategoriesDescription')->getRecordInstance();
	if ($include_itself) {
		$category_tree_array[] = array(
			'id'   => $parent_id,
			'text' => $CategoriesDescription->getCategoryName((int) $parent_id, (int) Session::get('languages_id'))
		);
	}

	$CategoryTree = $Categories->getParentSubCategories((int) $parent_id, (int) Session::get('languages_id'));
	if ($CategoryTree){
		foreach($CategoryTree as $cInfo){
			if ($exclude != $cInfo['categories_id']){
				$category_tree_array[] = array(
					'id'   => $cInfo['categories_id'],
					'text' => $spacing . $cInfo['CategoriesDescription'][0]['categories_name']
				);
			}
			$category_tree_array = tep_get_category_tree(
				$cInfo['categories_id'],
				$spacing . '&nbsp;&nbsp;&nbsp;',
				$exclude,
				$category_tree_array
			);
		}
	}

	return $category_tree_array;
}

function tep_info_image($image, $alt, $width = '', $height = '') {
	if (tep_not_null($image) && (file_exists(sysConfig::get('DIR_FS_CATALOG_IMAGES') . $image)) ) {
		$image = tep_image(sysConfig::get('DIR_WS_CATALOG_IMAGES') . $image, $alt, $width, $height);
	} else {
		$image = sysLanguage::get('TEXT_IMAGE_NONEXISTENT');
	}

	return $image;
}

function tep_break_string($string, $len, $break_char = '-') {
	$l = 0;
	$output = '';
	for ($i=0, $n=strlen($string); $i<$n; $i++) {
		$char = substr($string, $i, 1);
		if ($char != ' ') {
			$l++;
		} else {
			$l = 0;
		}
		if ($l > $len) {
			$l = 1;
			$output .= $break_char;
		}
		$output .= $char;
	}

	return $output;
}

function tep_get_zone_name($country_id, $zone_id, $default_zone) {
	$Zones = Doctrine_Core::getTable('Zones')->getRecordInstance();

	$zoneName = $Zones->getZoneName((int) $zone_id, (int) $country_id);
	if ($zoneName) {
		return $zoneName;
	} else {
		return $default_zone;
	}
}

function tep_not_null($value) {
	if (is_array($value)) {
		if (sizeof($value) > 0) {
			return true;
		} else {
			return false;
		}
	} else {
		if ( (is_string($value) || is_int($value)) && ($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0)) {
			return true;
		} else {
			return false;
		}
	}
}

function tep_tax_classes_pull_down($parameters, $selected = '') {
	$select_string = '<select ' . $parameters . '>';
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select tax_class_id, tax_class_title from tax_class order by tax_class_title");
	foreach ($ResultSet as $classes) {
		$select_string .= '<option value="' . $classes['tax_class_id'] . '"';
		if ($selected == $classes['tax_class_id']) $select_string .= ' SELECTED';
		$select_string .= '>' . $classes['tax_class_title'] . '</option>';
	}
	$select_string .= '</select>';

	return $select_string;
}

function tep_geo_zones_pull_down($parameters, $selected = '') {
	$select_string = '<select ' . $parameters . '>';
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select geo_zone_id, geo_zone_name from geo_zones order by geo_zone_name");
	foreach ($ResultSet as $zones) {
		$select_string .= '<option value="' . $zones['geo_zone_id'] . '"';
		if ($selected == $zones['geo_zone_id']) $select_string .= ' SELECTED';
		$select_string .= '>' . $zones['geo_zone_name'] . '</option>';
	}
	$select_string .= '</select>';

	return $select_string;
}

function tep_get_geo_zone_name($geo_zone_id) {
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchArray("select geo_zone_name from geo_zones where geo_zone_id = '" . (int)$geo_zone_id . "'");

	if (sizeof($ResultSet) <= 0) {
		$geo_zone_name = $geo_zone_id;
	} else {
		$geo_zone_name = $ResultSet[0]['geo_zone_name'];
	}

	return $geo_zone_name;
}

function fixImagesPath($htmlCode){
	if(sysConfig::getDirWsCatalog() == '/' || (strpos($htmlCode, sysConfig::getDirWsCatalog()) === 0)){
		$imgPath = $htmlCode;
	}else{
		$imgPath = sysConfig::getDirWsCatalog() .$htmlCode;
	}
	$imgPath = str_replace('//','/', $imgPath);
	return $imgPath;
}

function tep_address_format($address_format_id, $address, $html, $boln, $eoln, $type = 'long') {

	$QAddressAformat = Doctrine_Query::create()
	->from('AddressFormat')
	->where('address_format_id=?', $address_format_id)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if($type == 'long'){
		$fmt = $QAddressAformat[0]['address_format'];
	}else{
		$fmt = $QAddressAformat[0]['address_summary'];
	}
	$fmt = nl2br($fmt);
	$company = $address['entry_company'];
	if (isset($address['entry_firstname']) && ($address['entry_firstname'] != '') || isset($address['entry_lastname']) && ($address['entry_lastname'] != '')) {
		$firstname = (isset($address['entry_firstname'])?$address['entry_firstname']:'');
		$lastname = (isset($address['entry_lastname'])?$address['entry_lastname']:'');
	} elseif (isset($address['entry_name']) && tep_not_null($address['entry_name'])) {
		$firstname = $address['entry_name'];
		$lastname = '';
	} else {
		$firstname = '';
		$lastname = '';
	}

	$street_address = $address['entry_street_address'];
	$suburb = $address['entry_suburb'];
	$city = $address['entry_city'];
	$vat = $address['entry_vat'];
	$cif = $address['entry_cif'];
	$city_birth = $address['entry_city_birth'];
	$state = $address['entry_state'];
	$abbrstate = '';
	$country = '';
	if (isset($address['entry_country_id']) && tep_not_null($address['entry_country_id'])) {
		$country = tep_get_country_name($address['entry_country_id']);

		if (isset($address['entry_zone_id']) && tep_not_null($address['entry_zone_id'])) {
			$abbrstate = tep_get_zone_code($address['entry_country_id'], $address['entry_zone_id'], $state);
		}
	}
	$postcode = $address['entry_postcode'];

	eval("\$address = \"$fmt\";");

	return $address;
}

////
// Returns the address_format_id for the given country
// TABLES: countries;
function tep_get_address_format_id($country_id) {
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchArray("select address_format_id as format_id from countries where countries_id = '" . (int)$country_id . "'");
	if (sizeof($ResultSet) > 0) {
		return $ResultSet[0]['format_id'];
	} else {
		return '1';
	}
}

// Return a formatted address
// TABLES: customers, address_book
function tep_address_label($customers_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n") {
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchArray("select entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from address_book where customers_id = '" . (int)$customers_id . "' and address_book_id = '" . (int)$address_id . "'");

	$format_id = tep_get_address_format_id($ResultSet[0]['country_id']);

	return tep_address_format($format_id, $address, $html, $boln, $eoln);
}

////
// Return the tax description for a zone / class
// TABLES: tax_rates;
function tep_get_tax_description($class_id, $country_id, $zone_id) {
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select tax_description from tax_rates tr left join zones_to_geo_zones za on (tr.tax_zone_id = za.geo_zone_id) left join geo_zones tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . (int)$country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') and tr.tax_class_id = '" . (int)$class_id . "' order by tr.tax_priority");
	if (sizeof($ResultSet) > 0) {
		$tax_description = '';
		foreach ($ResultSet as $tax) {
			$tax_description .= $tax['tax_description'] . ' + ';
		}
		$tax_description = substr($tax_description, 0, -3);

		return $tax_description;
	} else {
		return sysLanguage::get('TEXT_UNKNOWN_TAX_RATE');
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////
//
// Function    : tep_get_zone_code
//
// Arguments   : country           country code string
//               zone              state/province zone_id
//               def_state         default string if zone==0
//
// Return      : state_prov_code   state/province code
//
// Description : Function to retrieve the state/province code (as in FL for Florida etc)
//
////////////////////////////////////////////////////////////////////////////////////////////////
function tep_get_zone_code($country, $zone, $def_state) {

	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select zone_code from zones where zone_country_id = '" . (int)$country . "' and zone_id = '" . (int)$zone . "'");

	if (sizeof($ResultSet) <= 0) {
		$state_prov_code = $def_state;
	}
	else {
		$state_prov_code = $ResultSet[0]['zone_code'];
	}

	return $state_prov_code;
}

function tep_get_uprid($prid, $params) {
	$uprid = $prid;
	if ( (is_array($params)) && (!strstr($prid, '{')) ) {
		while (list($option, $value) = each($params)) {
			$uprid = $uprid . '{' . $option . '}' . $value;
		}
	}

	return $uprid;
}

function tep_get_prid($uprid) {
	$pieces = explode('{', $uprid);

	return $pieces[0];
}

function tep_get_languages() {
	$languages_query = tep_db_query("select languages_id, name, code, image, directory from " . TABLE_LANGUAGES . " where status = '1' order by sort_order");
	while ($languages = tep_db_fetch_array($languages_query)) {
		$languages_array[] = array('id' => $languages['languages_id'],
		'name' => $languages['name'],
		'code' => $languages['code'],
		'image' => $languages['image'],
		'directory' => $languages['directory']);
	}

	return $languages_array;
}

function tep_get_orders_status_name($orders_status_id, $language_id = '') {
	if (!$language_id) $language_id = Session::get('languages_id');

	return tep_get_order_status_name($orders_status_id, $language_id);
}

function tep_get_products_name($product_id, $language_id = 0) {
	if ($language_id == 0) $language_id = Session::get('languages_id');
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select products_name from products_description where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");

	return $ResultSet[0]['products_name'];
}

////
// Wrapper for class_exists() function
// This function is not available in all PHP versions so we test it before using it.
function tep_class_exists($class_name) {
	return class_exists($class_name);
}

////
// Count how many products exist in a category
// TABLES: products, products_to_categories, categories
function tep_products_in_category_count($categories_id, $include_deactivated = false) {
	$products_count = 0;

	if ($include_deactivated) {
		$query = "select count(*) as total from products p, products_to_categories p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$categories_id . "'";
	} else {
		$query = "select count(*) as total from products p, products_to_categories p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '" . (int)$categories_id . "'";
	}

	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchArray($query);

	$products_count += $ResultSet[0]['total'];

	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select categories_id from categories where parent_id = '" . (int)$categories_id . "'");
	if (sizeof($ResultSet) > 0) {
		foreach ($ResultSet as $childs) {
			$products_count += tep_products_in_category_count($childs['categories_id'], $include_deactivated);
		}
	}

	return $products_count;
}

////
// Count how many subcategories exist in a category
// TABLES: categories
function tep_childs_in_category_count($categories_id) {
	$categories_count = 0;

	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select categories_id from categories where parent_id = '" . (int)$categories_id . "'");
	foreach ($ResultSet as $categories) {
		$categories_count++;
		$categories_count += tep_childs_in_category_count($categories['categories_id']);
	}

	return $categories_count;
}

	////
	// Returns an array with countries
	// TABLES: countries
function tep_get_countries($default = '') {
	$countries_array = array();
	if ($default) {
		$countries_array[] = array('id' => '',
		'text' => $default);
	}
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select countries_id, countries_name, countries_iso_code_2, countries_iso_code_3 from countries order by countries_name");
	foreach ($ResultSet as $countries) {
		$countries_array[] = array('id' => $countries['countries_id'],
		'text' => $countries['countries_name'],
		'countries_iso_code_2' => $countries['countries_iso_code_2'],
		'countries_iso_code_3' => $countries['countries_iso_code_3']);
	}

	return $countries_array;
}

function hex2bin($h){
	if (!is_string($h)) return null;
	$r='';
	for ($a=0; $a<strlen($h); $a+=2) { $r.=chr(hexdec($h{$a}.$h{($a+1)})); }
	return $r;
}

////
// return an array with country zones
function tep_get_country_zones($country_id) {
	$zones_array = array();
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select zone_id, zone_name from zones where zone_country_id = '" . (int)$country_id . "' order by zone_name");
	foreach ($ResultSet as $zones) {
		$zones_array[] = array('id' => $zones['zone_id'],
		'text' => $zones['zone_name']);
	}

	return $zones_array;
}

function tep_browser_detect($component) {
	return stristr($_SERVER['HTTP_USER_AGENT'], $component);
}

function tep_prepare_country_zones_pull_down($country_id = '') {
	// preset the width of the drop-down for Netscape
	$pre = '';
	if ( (!tep_browser_detect('MSIE')) && (tep_browser_detect('Mozilla/4')) ) {
		for ($i=0; $i<45; $i++) $pre .= '&nbsp;';
	}

	$zones = tep_get_country_zones($country_id);

	if (sizeof($zones) > 0) {
		$zones_select = array(array('id' => '', 'text' => sysLanguage::get('PLEASE_SELECT')));
		$zones = array_merge($zones_select, $zones);
	} else {
		$zones = array(array('id' => '', 'text' => sysLanguage::get('TYPE_BELOW')));
		// create dummy options for Netscape to preset the height of the drop-down
		if ( (!tep_browser_detect('MSIE')) && (tep_browser_detect('Mozilla/4')) ) {
			for ($i=0; $i<9; $i++) {
				$zones[] = array('id' => '', 'text' => $pre);
			}
		}
	}

	return $zones;
}

////
// Sets the status of a product
function tep_set_product_status($products_id, $status) {
	if ($status == '1') {
		return Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->exec("update products set products_status = '1', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
	} elseif ($status == '0') {
		return Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->exec("update products set products_status = '0', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
	} else {
		return -1;
	}
}

////
// Sets a product as featured
function tep_set_product_featured($products_id, $status) {
	if ($status == '1') {
		return Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->exec("update products set products_featured = '1', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
	} elseif ($status == '0') {
		return Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->exec("update products set products_featured = '0', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
	} else {
		return -1;
	}
}

////
// Return a product's special price (returns nothing if there is no offer)
// TABLES: products
function tep_get_products_special_price($product_id) {
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchArray("select specials_new_products_price from specials  where products_id = '" . $product_id . "'");

	return $ResultSet[0]['specials_new_products_price'];
}

////
// Sets timeout for the current script.
// Cant be used in safe mode.
function tep_set_time_limit($limit) {
	if (!get_cfg_var('safe_mode')) {
		set_time_limit($limit);
	}
}

////
// Retreive server information
function tep_get_system_information() {
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchArray("select now() as datetime");

	list($system, $host, $kernel) = preg_split('/[\s,]+/', @exec('uname -a'), 5);

	return array('date' => tep_datetime_short(date('Y-m-d H:i:s')),
	'system' => $system,
	'kernel' => $kernel,
	'host' => $host,
	'ip' => gethostbyname($host),
	'uptime' => @exec('uptime'),
	'http_server' => $_SERVER['SERVER_SOFTWARE'],
	'php' => PHP_VERSION,
	'zend' => (function_exists('zend_version') ? zend_version() : ''),
	'db_server' => DB_SERVER,
	'db_ip' => gethostbyname(DB_SERVER),
	'db_version' => 'MySQL ' . (function_exists('mysql_get_server_info') ? mysql_get_server_info() : ''),
	'db_date' => tep_datetime_short($ResultSet[0]['datetime']));
}

function tep_generate_category_path($id, $from = 'category', $categories_array = '', $index = 0) {
	if (!is_array($categories_array)) $categories_array = array();

	if ($from == 'product') {
		$QCategories = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc("select categories_id from products_to_categories where products_id = '" . (int)$id . "'");
		foreach ($QCategories as $categories) {
			if ($categories['categories_id'] == '0') {
				$categories_array[$index][] = array('id' => '0', 'text' => sysLanguage::get('TEXT_TOP'));
			} else {
				$Category = Doctrine_Manager::getInstance()
					->getCurrentConnection()
					->fetchArray("select cd.categories_name, c.parent_id from categories c, categories_description cd where c.categories_id = '" . (int)$categories['categories_id'] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)Session::get('languages_id') . "'");
				$categories_array[$index][] = array('id' => $categories['categories_id'], 'text' => $Category[0]['categories_name']);
				if ( (tep_not_null($Category[0]['parent_id'])) && ($Category[0]['parent_id'] != '0') ) $categories_array = tep_generate_category_path($Category[0]['parent_id'], 'category', $categories_array, $index);
				$categories_array[$index] = array_reverse($categories_array[$index]);
			}
			$index++;
		}
	} elseif ($from == 'category') {
		$Category = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchArray("select cd.categories_name, c.parent_id from categories c, categories_description cd where c.categories_id = '" . (int)$id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)Session::get('languages_id') . "'");
		$categories_array[$index][] = array('id' => $id, 'text' => $Category[0]['categories_name']);
		if ( (tep_not_null($Category[0]['parent_id'])) && ($Category[0]['parent_id'] != '0') ) $categories_array = tep_generate_category_path($Category[0]['parent_id'], 'category', $categories_array, $index);
	}

	return $categories_array;
}

function tep_output_generated_category_path($id, $from = 'category') {
	$calculated_category_path_string = '';
	$calculated_category_path = tep_generate_category_path($id, $from);
	for ($i=0, $n=sizeof($calculated_category_path); $i<$n; $i++) {
		for ($j=0, $k=sizeof($calculated_category_path[$i]); $j<$k; $j++) {
			$calculated_category_path_string .= $calculated_category_path[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
		}
		$calculated_category_path_string = substr($calculated_category_path_string, 0, -16) . '<br>';
	}
	$calculated_category_path_string = substr($calculated_category_path_string, 0, -4);

	if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = sysLanguage::get('TEXT_TOP');

	return $calculated_category_path_string;
}

function tep_get_generated_category_path_ids($id, $from = 'category') {
	$calculated_category_path_string = '';
	$calculated_category_path = tep_generate_category_path($id, $from);
	for ($i=0, $n=sizeof($calculated_category_path); $i<$n; $i++) {
		for ($j=0, $k=sizeof($calculated_category_path[$i]); $j<$k; $j++) {
			$calculated_category_path_string .= $calculated_category_path[$i][$j]['id'] . '_';
		}
		$calculated_category_path_string = substr($calculated_category_path_string, 0, -1) . '<br>';
	}
	$calculated_category_path_string = substr($calculated_category_path_string, 0, -4);

	if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = sysLanguage::get('TEXT_TOP');

	return $calculated_category_path_string;
}

function tep_reset_cache_block($cache_block) {
	global $cache_blocks;

	for ($i=0, $n=sizeof($cache_blocks); $i<$n; $i++) {
		if ($cache_blocks[$i]['code'] == $cache_block) {
			if ($cache_blocks[$i]['multiple']) {
				if ($dir = @opendir(DIR_FS_CACHE)) {
					while ($cache_file = readdir($dir)) {
						$cached_file = $cache_blocks[$i]['file'];
						$languages = tep_get_languages();
						for ($j=0, $k=sizeof($languages); $j<$k; $j++) {
							$cached_file_unlink = preg_replace('/-language/', '-' . $languages[$j]['directory'], $cached_file);
							if (ereg('^' . $cached_file_unlink, $cache_file)) {
								@unlink(DIR_FS_CACHE . $cache_file);
							}
						}
					}
					closedir($dir);
				}
			} else {
				$cached_file = $cache_blocks[$i]['file'];
				$languages = tep_get_languages();
				for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
					$cached_file = preg_replace('/-language/', '-' . $languages[$i]['directory'], $cached_file);
					@unlink(DIR_FS_CACHE . $cached_file);
				}
			}
			break;
		}
	}
}

function tep_remove($source) {
	global $messageStack, $tep_remove_error;

	if (isset($tep_remove_error)) $tep_remove_error = false;

	if (is_dir($source)) {
		$dir = dir($source);
		while ($file = $dir->read()) {
			if ( ($file != '.') && ($file != '..') ) {
				if (is_writeable($source . '/' . $file)) {
					tep_remove($source . '/' . $file);
				} else {
					$messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source . '/' . $file), 'error');
					$tep_remove_error = true;
				}
			}
		}
		$dir->close();

		if (is_writeable($source)) {
			rmdir($source);
		} else {
			$messageStack->add(sprintf(ERROR_DIRECTORY_NOT_REMOVEABLE, $source), 'error');
			$tep_remove_error = true;
		}
	} else {
		if (is_writeable($source)) {
			unlink($source);
		} else {
			$messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source), 'error');
			$tep_remove_error = true;
		}
	}
}

////
// Output the tax percentage with optional padded decimals
function tep_display_tax_value($value, $padding = TAX_DECIMAL_PLACES) {
	if (strpos($value, '.')) {
		$loop = true;
		while ($loop) {
			if (substr($value, -1) == '0') {
				$value = substr($value, 0, -1);
			} else {
				$loop = false;
				if (substr($value, -1) == '.') {
					$value = substr($value, 0, -1);
				}
			}
		}
	}

	if ($padding > 0) {
		if ($decimal_pos = strpos($value, '.')) {
			$decimals = strlen(substr($value, ($decimal_pos+1)));
			for ($i=$decimals; $i<$padding; $i++) {
				$value .= '0';
			}
		} else {
			$value .= '.';
			for ($i=0; $i<$padding; $i++) {
				$value .= '0';
			}
		}
	}

	return $value;
}

function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address, $attachments = '') {
	if (sysConfig::get('SEND_EMAILS') != 'true') return false;

	// Instantiate a new mail object
	$message = new email(array('X-Mailer: osCommerce'));

	// Build the text version
	$text = strip_tags($email_text);
	if (sysConfig::get('EMAIL_USE_HTML') == 'true') {
		$message->add_html($email_text, $text);
	} else {
		$message->add_text($text);
	}
	if(!empty($attachments)){
		if(!is_array($attachments)){
			$attachment = fread(fopen(sysConfig::getDirFsCatalog().$attachments, "r"), filesize(sysConfig::getDirFsCatalog() . $attachments));
			$message->add_attachment($attachment,basename($attachments));
		}else{
			foreach($attachments as $attach){
				$attachment = fread(fopen(sysConfig::getDirFsCatalog().$attach, "r"), filesize(sysConfig::getDirFsCatalog() . $attach));
				$message->add_attachment($attachment,basename($attach));
			}
		}
	}

	// Send message
	$message->build_message();
	$message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
}

function tep_get_tax_class_title($tax_class_id) {
	if ($tax_class_id == '0') {
		return sysLanguage::get('TEXT_NONE');
	} else {
		$ResultSet = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchArray("select tax_class_title from tax_class where tax_class_id = '" . (int)$tax_class_id . "'");

		return $ResultSet[0]['tax_class_title'];
	}
}

function tep_banner_image_extension() {
	if (function_exists('imagetypes')) {
		if (imagetypes() & IMG_PNG) {
			return 'png';
		} elseif (imagetypes() & IMG_JPG) {
			return 'jpg';
		} elseif (imagetypes() & IMG_GIF) {
			return 'gif';
		}
	} elseif (function_exists('imagecreatefrompng') && function_exists('imagepng')) {
		return 'png';
	} elseif (function_exists('imagecreatefromjpeg') && function_exists('imagejpeg')) {
		return 'jpg';
	} elseif (function_exists('imagecreatefromgif') && function_exists('imagegif')) {
		return 'gif';
	}

	return false;
}

////
// Wrapper function for round() for php3 compatibility
function tep_round($value, $precision) {
	return round($value, $precision);
}

////
// Add tax to a products price
function tep_add_tax($price, $tax) {
	global $currencies;

	if (DISPLAY_PRICE_WITH_TAX == 'true') {
		return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) + tep_calculate_tax($price, $tax);
	} else {
		return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
	}
}

// Calculates Tax rounding the result
function tep_calculate_tax($price, $tax) {
	global $currencies;

	return tep_round($price * $tax / 100, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
}

////
// Returns the tax rate for a zone / class
// TABLES: tax_rates, zones_to_geo_zones
function tep_get_tax_rate($class_id, $country_id = -1, $zone_id = -1) {
	global $customer_zone_id, $customer_country_id;

	if ( ($country_id == -1) && ($zone_id == -1) ) {
		if (Session::exists('customer_id') === false) {
			$country_id = STORE_COUNTRY;
			$zone_id = STORE_ZONE;
		} else {
			$country_id = $customer_country_id;
			$zone_id = $customer_zone_id;
		}
	}

	$Qtax = Doctrine_Query::create()
	->select('SUM(tax_rate) as tax_rate')
	->from('TaxRates tr')
	->leftJoin('ZonesToGeoZones za')
	->leftJoin('GeoZones tz')
	->where('(za.zone_country_id IS NULL OR za.zone_country_id = 0 OR za.zone_country_id = ?) AND TRUE', (int)$country_id)
	->andWhere('(za.zone_id IS NULL OR za.zone_id = 0 OR za.zone_id = ?) AND TRUE', (int)$zone_id)
	->andWhere('tr.tax_class_id = ?', (int) $class_id)
	->groupBy('tr.tax_priority')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qtax) {
		$tax_multiplier = 0;
		foreach($Qtax as $tInfo){
			$tax_multiplier += $tInfo['tax_rate'];
		}
		return $tax_multiplier;
	} else {
		return 0;
	}
}

////
// Returns the tax rate for a tax class
// TABLES: tax_rates
function tep_get_tax_rate_value($class_id) {
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select SUM(tax_rate) as tax_rate from tax_rates where tax_class_id = '" . (int)$class_id . "' group by tax_priority");
	if (sizeof($ResultSet) > 0) {
		$tax_multiplier = 0;
		foreach ($ResultSet as $tax) {
			$tax_multiplier += $tax['tax_rate'];
		}
		return $tax_multiplier;
	} else {
		return 0;
	}
}

function tep_call_function($function, $parameter, $object = '') {
	if ($object == '') {
		return call_user_func($function, $parameter);
	} elseif (PHP_VERSION < 4) {
		return call_user_method($function, $object, $parameter);
	} else {
		return call_user_func(array($object, $function), $parameter);
	}
}

////
// Return a random value
function tep_rand($min = null, $max = null) {
	static $seeded;

	if (!$seeded) {
		mt_srand((double)microtime()*1000000);
		$seeded = true;
	}

	if (isset($min) && isset($max)) {
		if ($min >= $max) {
			$return = $min;
		} else {
			$return = mt_rand($min, $max);
		}
	} else {
		$return = mt_rand();
	}

	return $return;
}

// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)
function tep_convert_linefeeds($from, $to, $string) {
	return str_replace($from, $to, $string);
}

function tep_string_to_int($string) {
	return (int)$string;
}

////
// Parse and secure the cPath parameter values
function tep_parse_category_path($cPath) {
	// make sure the category IDs are integers
	$cPath_array = array_map('tep_string_to_int', explode('_', $cPath));

	// make sure no duplicate category IDs exist which could lock the server in a loop
	$tmp_array = array();
	$n = sizeof($cPath_array);
	for ($i=0; $i<$n; $i++) {
		if (!in_array($cPath_array[$i], $tmp_array)) {
			$tmp_array[] = $cPath_array[$i];
		}
	}

	return $tmp_array;
}

function tep_get_ip_address() {
	if (isset($_SERVER)) {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	} else {
		if (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} else {
			$ip = getenv('REMOTE_ADDR');
		}
	}

	return $ip;
}

function tep_draw_products_down($name, $parameters = '', $exclude = '', $selected = 0) {
	global $currencies;

	if ($exclude == '') {
		$exclude = array();
	}

	$select_string = '<select name="' . $name . '"';

	if ($parameters) {
		$select_string .= ' ' . $parameters;
	}

	$select_string .= '>';

	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select p.products_id, pd.products_name from products p, products_description pd where p.products_id = pd.products_id and pd.language_id = '" . (int)Session::get('languages_id') . "' order by products_name");
	foreach ($ResultSet as $products) {
		if (!in_array($products['products_id'], $exclude)) {
			if ($selected == $products['products_id']) $sel = 'selected'; else $sel='';
			$select_string .= '<option value="' . $products['products_id'] . '" '.$sel.'>' . $products['products_name'] . '</option>';
		}
	}

	$select_string .= '</select>';

	return $select_string;
}

function print_DYMO_labels($orders_query, &$pdf)
{

	$orders_count = tep_db_num_rows($orders_query);
	//   for ($t=0; $t<3; $t++)
	//   {
	$t=0;
	$x = 39 + $t*270;
	$y = 180;
	$pos = 177;
	$xstartoffset = 7;
	$labels_count = 0;
	//    while ($labels_count < 3)
	//    {
	$orders = tep_db_fetch_array($orders_query);
	if ($orders)
	{
		$stpos = $pos;
		$product_info_query = tep_db_query("select pd.products_name, pd.products_description, p.products_time from products p, products_description pd where p.products_status = '1' and p.products_id = '" . (int)$orders['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = 1");
		$product_info = tep_db_fetch_array($product_info_query);

		$file_name_src = 'images/barcode/'.$orders['products_barcode'].'.png';
		$size = getimagesize($file_name_src);
		$im_offset = $x + round(((340-$x) - $size[0])/2);

		//    $pdf->addJpegFromFile(BATCH_PRINT_INC . 'templates/' . 'new_label2.jpg',$x,$y,260,133);
		$length = $pdf->getTextWidth(13,$orders['products_barcode']);
		$offset = $x + round(((($x+$xstartoffset+$size[0])-$x) - $length)/2);


		$pdf->addText($offset,$pos-=14,12,$orders['products_barcode']);


		//         $file_name___src = 'images/barcode/__'.$orders['products_barcode'].'.png';
		//         if (!file_exists($file_name___src))
		//           if (file_exists($file_name_src))
		//           {
		//               $src = imagecreatefrompng($file_name_src);
		//               $size = getimagesize($file_name_src);
		//               $dest = imagecreatetruecolor($size[0]-20, $size[1]-20);
		//               imagecopy($dest, $src, 0, 0, 0, 0, $size[0]-20, $size[1]-20);
		//               imagepng($dest,'images/barcode/__'.$orders['products_barcode'].'.png');
		//               imagedestroy($dest);
		//               imagedestroy($src);
		//           }

		if (file_exists($file_name_src))
		$pdf->addPngFromFile('images/barcode/'.$orders['products_barcode'].'.png',$x+$xstartoffset-2,$pos-=28,$size[0],25);

		$pdf->setLineStyle(1);
		$pdf->line($x,$pos-=5,$x+280,$pos);

		$strarrs = arrayStringsByLenght($product_info['products_name'], 31, 2);
		if (sizeof($strarrs)>1) $points = '...';  else $points='';
		$pdf->addText($x,$pos-=16,13,$strarrs[0].$points);


		$pdf->addText($x+$xstartoffset+3,$pos-=12,8,'Time: ');
		if (!empty($product_info['products_time']))
		$pdf->addText($x+$xstartoffset+35,$pos,8,$product_info['products_time'].' minutes');

		$pdf->addText($x+$xstartoffset+3,$pos-=10,8,'Starring: ');
		if (!empty($_actors))
		{
			if ($_actors)
			{
				$strarrs = arrayStringsByLenght($_actors, 56, 2);
				if (sizeof($strarrs)>1) $points = '...';  else $points='';
				$pdf->addText($x+$xstartoffset+35,$pos,8,$strarrs[0].$points);
			}

		}

		$pdf->addText($x+$xstartoffset+3,$pos-=10,8,'Director: ');
		if (!empty($_directors))
		{
			if ($_directors)
			{
				$strarrs = arrayStringsByLenght($_directors, 56, 2);
				if (sizeof($strarrs)>1) $points = '...';  else $points='';
				$pdf->addText($x+$xstartoffset+35,$pos,8,$strarrs[0].$points);
			}
		}

		$pos-=5;
		if (!empty($product_info['products_description']))
		{
			//$pdf->addText($x+$xstartoffset,$pos-=15,10,strip_tags($product_info['products_description']));
			if (strlen(strip_tags($product_info['products_description']))<320) $dots=''; else $dots='...';
			$strarrs = arrayStringsByLenght(substr(strip_tags($product_info['products_description']),0,320).$dots, 66, 2);
			for ($i = 0; $i <sizeof($strarrs); $i++)
			{
				$pdf->addText($x+$xstartoffset,$pos-=9,8,trim($strarrs[$i]));
			}

		}
		$pos = $stpos - 160;
		$y -= 160;
		// $record_count++;
	}
	$labels_count++;
	//   }
	//   }
}

function arrayStringsByLenght($str,$length=50,$offset = 5)
{
	$strings = array();

	$str_tmp = $str;
	while($str_tmp != '')
	{
		$strlenght = strlen($str_tmp);
		$startpos = 0;
		$pos2 = strpos(substr($str_tmp,$length,strlen($str_tmp)-$length),' ');
		if ($pos2 <= $offset)
		{
			$strings[] = substr($str_tmp,$startpos,$length + $pos2);
			$str_tmp = substr($str_tmp,$length + $pos2, $strlenght - ($length + $pos2));
		}
		else
		{
			$pos1 = strrpos(substr($str_tmp, $startpos, $length),' ');
			$strings[] = substr($str_tmp, $startpos, $pos1);
			$str_tmp = substr($str_tmp,$pos1, $strlenght - $pos1);
		}
	}

	return $strings;
}

function tep_res_shipping_name($method){
	switch($method){
		case 'method1':
			return MODULE_SHIPPING_RESERVATION_TEXT1;
			break;
		case 'method2':
			return MODULE_SHIPPING_RESERVATION_TEXT2;
			break;
		case 'method3':
			return MODULE_SHIPPING_RESERVATION_TEXT3;
			break;
		case 'method4':
			return MODULE_SHIPPING_RESERVATION_TEXT4;
			break;
		case 'method5':
			return MODULE_SHIPPING_RESERVATION_TEXT5;
			break;
	}
}

function tep_custom_shipping_name($method){
	$num = substr($method, 6);
	return constant('MODULE_SHIPPING_CUSTOM_METHOD_TEXT' . $num);
}

function tep_inventorycenter_shipping_name($method){
	$num = substr($method, 6);
	return constant('MODULE_SHIPPING_INVENTORYCENTER_METHOD_TEXT' . $num);
}

function tep_create_random_value($length, $type = 'mixed') {
	if ( ($type != 'mixed') && ($type != 'chars') && ($type != 'digits')) return false;

	$rand_value = '';
	while (strlen($rand_value) < $length) {
		if ($type == 'digits') {
			$char = tep_rand(0,9);
		} else {
			$char = chr(tep_rand(0,255));
		}
		if ($type == 'mixed') {
			if (eregi('^[a-z0-9]$', $char)) $rand_value .= $char;
		} elseif ($type == 'chars') {
			if (eregi('^[a-z]$', $char)) $rand_value .= $char;
		} elseif ($type == 'digits') {
			if (ereg('^[0-9]$', $char)) $rand_value .= $char;
		}
	}

	return $rand_value;
}

function tep_translate_order_statuses($value) {
	$statuses_array = array();
	$values = array();
	foreach(explode(',', $value) as $i => $id){
		if ($id == '') continue;

		$Qstatus = Doctrine_Query::create()
		->select('orders_status_name')
		->from('OrdersStatusDescription')
		->where('orders_status_id = ?', (int)$id)
		->andWhere('language_id = ?', (int)Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		$values[] = $Qstatus[0]['orders_status_name'];
	}

	return implode(',', $values);
}

/* cfg functions -- BEGIN -- */
function itw_cfg_upload_field($value, $key = ''){
	//$string = '<br><input type="file" name="configuration_value"><br>Local File:<input type="text" name="configuration_value_local" value="' . $value . '">';
	$config_image = htmlBase::newElement('uploadManagerInput')
		->setName('configuration[' . $key . ']')
		->setFileType('image')
		->autoUpload(true)
		->showPreview(true)
		->showMaxUploadSize(true)
		->allowMultipleUploads(false);

	$config_image->setPreviewFile($value);
	return '<br/>Local File'.$config_image->draw();
}
////
// Alias function for Store configuration values in the Administration Tool
function tep_cfg_pull_down_country_list($country_id, $key = '') {
	return tep_draw_pull_down_menu('configuration[' . $key . ']', tep_get_countries(), $country_id);
}

function tep_cfg_pull_down_order_status_list($status_id, $key = '') {
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
	$Qstatus = Doctrine_Query::create()
	->select('s.orders_status_id, sd.orders_status_name')
	->from('OrdersStatus s')
	->leftJoin('s.OrdersStatusDescription sd')
	->where('sd.language_id = ?', (int) Session::get('languages_id'))
	->orderBy('s.orders_status_id')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$htmlSelect = htmlBase::newElement('selectbox')
	->setName($name)
	->selectOptionByValue($status_id);

	foreach($Qstatus as $sInfo){
		$id = $sInfo['orders_status_id'];
		$name = $sInfo['OrdersStatusDescription'][0]['orders_status_name'];
		$htmlSelect->addOption($id, $name);
	}
	return '<br>' . $htmlSelect->draw();
}

function itw_get_templates_array(){
	$templates = new fileSystemBrowser(sysConfig::getDirFsCatalog()  . 'templates/');
	$directories = $templates->getDirectories(array('email', 'help', 'help-text'));
	$templatesArray = array();
	foreach($directories as $dirInfo){
		$templatesArray[] = array(
			'id' => $dirInfo['basename'],
			'text' => ucfirst($dirInfo['basename'])
		);
	}

	usort($templatesArray, function ($a, $b){
		return ($a['id']{0} > $b['id']{0} ? 1 : -1);
	});

	return $templatesArray;
}

function tep_cfg_pull_down_template_list($templateName){
	$templates = new fileSystemBrowser(sysConfig::getDirFsCatalog()  . 'templates/');
	$directories = $templates->getDirectories(array('email', 'help', 'help-text'));
	$templatesArray = array();
	foreach($directories as $dirInfo){
		$templatesArray[] = ucfirst($dirInfo['basename']);
	}

	sort($templatesArray);

	$switcher = htmlBase::newElement('selectbox')
	->setName('configuration_value')
	->selectOptionByValue($templateName);
	foreach($templatesArray as $dir){
		$lowered = strtolower($dir);
		$switcher->addOption($lowered, $dir);
	}
	return $switcher->draw();
}

function tep_cfg_pull_down_zone_list($zone_id, $key = '') {
	return tep_draw_pull_down_menu('configuration[' . $key . ']', tep_get_country_zones(sysConfig::get('STORE_COUNTRY')), $zone_id);
}

function tep_cfg_pull_down_tax_classes($tax_class_id, $key = '') {
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

	$tax_class_array = array(array('id' => '0', 'text' => sysLanguage::get('TEXT_NONE')));
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select tax_class_id, tax_class_title from tax_class order by tax_class_title");
	foreach ($ResultSet as $tax_class) {
		$tax_class_array[] = array('id' => $tax_class['tax_class_id'],
		'text' => $tax_class['tax_class_title']);
	}

	return tep_draw_pull_down_menu($name, $tax_class_array, $tax_class_id);
}

////
// Function to read in text area in admin
function tep_cfg_textarea($text) {
	return tep_draw_textarea_field('configuration_value', false, 35, 5, $text);
}

function tep_cfg_get_zone_name($zone_id) {
	$Qzone = Doctrine_Query::create()
		->select('zone_name')
		->from('Zones')
		->where('zone_id = ?', (int)$zone_id)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if (!$Qzone) {
		return $zone_id;
	} else {
		return $Qzone[0]['zone_name'];
	}
}
////
// Alias function for Store configuration values in the Administration Tool
function tep_cfg_select_option($select_array, $key_value, $key = '') {
	$string = '';

	for ($i=0, $n=sizeof($select_array); $i<$n; $i++) {
		$name = ((tep_not_null($key)) ? 'configuration[' . $key . ']' : 'configuration_value');

		$string .= '<br><input type="radio" name="' . $name . '" value="' . $select_array[$i] . '"';

		if ($key_value == $select_array[$i]) $string .= ' CHECKED';

		$string .= '> ' . $select_array[$i];
	}

	return $string;
}


function tep_cfg_textarea_element($key_value, $key = '') {
	$string = '';


	$name = ((tep_not_null($key)) ? 'configuration[' . $key . ']' : 'configuration_value');

	$string .= '<textarea name="'.$name.'" cols="8" rows="4">'.$key_value.'</textarea>';



	return $string;
}


function tep_cfg_select_option_elements($select_array, $key_value, $key = '') {
	for ($i=0, $n=sizeof($select_array); $i<$n; $i++) {
		$elArr[] = array(
			'value' => $select_array[$i],
			'labelPosition' => 'after',
			'label' => $select_array[$i]
		);
	}
	$elements = htmlBase::newElement('radio')->addGroup(array(
		'name'      => 'configuration[' . $key . ']',
		'separator' => '<br />',
		'checked'   => $key_value,
		'data'      => $elArr
	));

	return $elements;
}

////
// Alias function for module configuration keys
function tep_mod_select_option($select_array, $key_name, $key_value) {
	reset($select_array);
	while (list($key, $value) = each($select_array)) {
		if (is_int($key)) $key = $value;
		$string .= '<br><input type="radio" name="configuration[' . $key_name . ']" value="' . $key . '"';
		if ($key_value == $key) $string .= ' CHECKED';
		$string .= '> ' . $value;
	}

	return $string;
}

// Alias function for Store configuration values in the Administration Tool
function tep_cfg_select_multioption($select_array, $key_value, $key = '') {
	$string = '';
	for ($i=0; $i<sizeof($select_array); $i++) {
		$name = (($key) ? 'configuration[' . $key . '][]' : 'configuration_value[]');
		if (is_array($select_array[$i]) && array_key_exists('id', $select_array[$i])){
			$string .= '<br><input type="checkbox" name="' . $name . '" value="' . $select_array[$i]['id'] . '"';
		}else{
			$string .= '<br><input type="checkbox" name="' . $name . '" value="' . $select_array[$i] . '"';
		}
		$key_values = explode(",", $key_value);
		array_walk($key_values, 'trim');
		if (is_array($select_array[$i]) && array_key_exists('id', $select_array[$i])){
			if ( in_array($select_array[$i]['id'], $key_values) ) $string .= ' CHECKED';
		}else{
			if ( in_array($select_array[$i], $key_values) ) $string .= ' CHECKED';
		}
		if (is_array($select_array[$i]) && array_key_exists('text', $select_array[$i])){
			$string .= '> ' . $select_array[$i]['text'];
		}else{
			$string .= '> ' . $select_array[$i];
		}
	}
	//if (empty($string)){
		$string .= '<input type="hidden" name="' . $name . '" value="">';
	//}
	return $string;
}

function tep_cfg_payment_fee($selected, $key=''){

	$Qmodules = Doctrine_Query::create()
	->from('Modules m')
	->leftJoin('m.ModulesConfiguration mc')
	->where('m.modules_type = ?', 'order_payment')
	->orderBy('mc.sort_order')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$selectedPayments = explode(',', $selected);
	array_walk($selectedPayments, 'trim');
	$string = '';
	foreach($Qmodules as $module){
		$name = (($key) ? 'configuration[' . $key . '][]' : 'configuration_value[]');
		$val = '';
		foreach($selectedPayments as $sPayment){
			$method_value = explode('-', $sPayment);
			if($method_value[0] == $module['modules_code']){
				$val = $method_value[1];
				break;
			}
		}
		$string .= '<input type="text" name="'.$name.'" value="'.$module['modules_code'].'-'.$val.'"><br/>';
	}
	return $string;
}



function tep_cfg_show_installed_status($value, $key = ''){
	return '<br><b>' . $value . '</b>';
}
function tep_cfg_select_multioption_element($select_array, $key_value, $key = '') {
	for ($i=0; $i<sizeof($select_array); $i++) {
		if (is_array($select_array[$i]) && array_key_exists('id', $select_array[$i])){
			$val = $select_array[$i]['id'];
			$label = $select_array[$i]['text'];
		}else{
			$val = $select_array[$i];
			$label = $select_array[$i];
		}

		$elArr[] = array(
			'value' => $val,
			'labelPosition' => 'after',
			'label' => $label
		);
	}
	$key_values = explode(',', $key_value);
	$elements = htmlBase::newElement('checkbox')->addGroup(array(
		'name'      => (($key) ? 'configuration[' . $key . '][]' : 'configuration_value[]'),
		'separator' => '<br />',
		'checked' => $key_values,
		'data'      => $elArr
	));



	return $elements;
}
////

function tep_cfg_pull_multi_order_statuses($selected, $key = '') {
	$statuses_array = array();
	foreach(getOrderStatuses(null, (int) Session::get('languages_id')) as $sInfo){
		$statuses_array[] = array(
			'id'   => $sInfo['orders_status_id'],
			'text' => $sInfo['OrdersStatusDescription'][(int) Session::get('languages_id')]['orders_status_name']
		);
	}

	return tep_cfg_select_multioption($statuses_array, $selected, $key);
}
function tep_cfg_pull_down_zone_classes($zone_class_id, $key = '') {
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

	$zone_class_array = array(array('id' => '0', 'text' => sysLanguage::get('TEXT_NONE')));
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select geo_zone_id, geo_zone_name from geo_zones order by geo_zone_name");
	foreach ($ResultSet as $zone_class) {
		$zone_class_array[] = array('id' => $zone_class['geo_zone_id'],
		'text' => $zone_class['geo_zone_name']);
	}

	return tep_draw_pull_down_menu($name, $zone_class_array, $zone_class_id);
}

function tep_cfg_pull_down_zone_classes_element($zone_class_id, $key = '') {
	$selectBox = htmlBase::newElement('selectbox')
	->setName((($key) ? 'configuration[' . $key . ']' : 'configuration_value'));

	$selectBox->addOption('0', sysLanguage::get('TEXT_NONE'));
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select geo_zone_id, geo_zone_name from geo_zones order by geo_zone_name");
	foreach ($ResultSet as $zone_class) {
		$selectBox->addOption($zone_class['geo_zone_id'], $zone_class['geo_zone_name']);
	}
	$selectBox->selectOptionByValue($zone_class_id);

	return $selectBox;
}

function tep_cfg_pull_down_google_zone_classes_element($zone_class_id, $key = '') {
	$selectBox = htmlBase::newElement('selectbox')
	->setName((($key) ? 'configuration[' . $key . ']' : 'configuration_value'));

	$selectBox->addOption('0', 'Everywhere');
	$QGoogleZones = Doctrine_Query::create()
					->from('GoogleZones')
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if (count($QGoogleZones) > 0){
		 foreach($QGoogleZones as $zInfo){
			$selectBox->addOption($zInfo['google_zones_id'], $zInfo['google_zones_name']);
		 }
	}

	$selectBox->selectOptionByValue($zone_class_id);

	return $selectBox->draw();
}

function tep_cfg_pull_down_order_statuses($order_status_id, $key = '') {
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

	$statusesArray = array();

	foreach(getOrderStatuses(null, (int) Session::get('languages_id')) as $sInfo){
		$statuses_array[] = array(
			'id'   => $sInfo['orders_status_id'],
			'text' => $sInfo['OrdersStatusDescription'][(int) Session::get('languages_id')]['orders_status_name']
		);
	}

	return tep_draw_pull_down_menu($name, $statuses_array, $order_status_id);
}

function tep_cfg_pull_down_order_statuses_element($order_status_id, $key = '') {
	$selectBox = htmlBase::newElement('selectbox')
	->setName((($key) ? 'configuration[' . $key . ']' : 'configuration_value'));

	//$selectBox->addOption('0', sysLanguage::get('TEXT_DEFAULT'));
	foreach(getOrderStatuses(null, (int) Session::get('languages_id')) as $sInfo){
		$selectBox->addOption(
			$sInfo['orders_status_id'],
			$sInfo['OrdersStatusDescription'][(int) Session::get('languages_id')]['orders_status_name']
		);
	}

	$selectBox->selectOptionByValue($order_status_id);

	return $selectBox;
}

//////create a pull down for all payment installed payment methods for Order Editor configuration

// Get list of all payment modules available
function tep_cfg_pull_down_payment_methods() {
	$enabled_payment = array();
	$module_directory = DIR_FS_CATALOG_MODULES . 'payment/';
	$file_extension = '.php';

	if ($dir = @dir($module_directory)) {
		while ($file = $dir->read()) {
			if (!is_dir( $module_directory . $file)) {
				if (substr($file, strrpos($file, '.')) == $file_extension) {
					$directory_array[] = $file;
				}
			}
		}
		sort($directory_array);
		$dir->close();
	}

	// For each available payment module, check if enabled
	for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) {
		$file = $directory_array[$i];

		include(DIR_FS_CATALOG_LANGUAGES . Session::get('language') . '/modules/payment/' . $file);
		include($module_directory . $file);

		$class = substr($file, 0, strrpos($file, '.'));
		if (tep_class_exists($class)) {
			$module = new $class;
			if ($module->check() > 0) {
				// If module enabled create array of titles
				$enabled_payment[] = array('id' => $module->title, 'text' => $module->title);

			}
		}
	}

	$enabled_payment[] = array('id' => 'Other', 'text' => 'Other');

	//draw the dropdown menu for payment methods and default to the order value
	return tep_draw_pull_down_menu('configuration_value', $enabled_payment, '', '');
}
function tep_cfg_pull_down_res_shipping($method, $key = '') {
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

	$array = array(
	array(
	'id'   => 'method1',
	'text' => MODULE_SHIPPING_RESERVATION_TEXT1
	),
	array(
	'id'   => 'method2',
	'text' => MODULE_SHIPPING_RESERVATION_TEXT2
	),
	array(
	'id'   => 'method3',
	'text' => MODULE_SHIPPING_RESERVATION_TEXT3
	),
	array(
	'id'   => 'method4',
	'text' => MODULE_SHIPPING_RESERVATION_TEXT4
	),
	array(
	'id'   => 'method5',
	'text' => MODULE_SHIPPING_RESERVATION_TEXT5
	)
	);

	return tep_draw_pull_down_menu($name, $array, $method);
}

function tep_cfg_pull_down_custom_shipping($method, $key = '') {
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

	$array = array();
	for($i=1; $i<=MODULE_SHIPPING_CUSTOM_NUM_METHODS; $i++){
		$array[] = array(
			'id' => 'method' . $i,
			'text' => constant('MODULE_SHIPPING_CUSTOM_METHOD_TEXT' . $i)
		);
	}

	return tep_draw_pull_down_menu($name, $array, $method);
}

function tep_cfg_pull_down_inventorycenter_shipping($method, $key = '') {
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

	$array = array();
	for($i=1; $i<=sysConfig::get('MODULE_SHIPPING_INVENTORYCENTER_NUM_METHODS'); $i++){
		$array[] = array(
			'id' => 'method' . $i,
			'text' => constant('MODULE_SHIPPING_INVENTORYCENTER_METHOD_TEXT' . $i)
		);
	}

	return tep_draw_pull_down_menu($name, $array, $method);
}
/* cfg functions -- END -- */

	function permutateArray($items, $perms = array(), &$Result){
		if (empty($items)){
			$Result[] = join('', $perms);
		}else{
			for ($i = count($items) - 1; $i >= 0; --$i){
				$newitems = $items;
				$newperms = $perms;
				list($foo) = array_splice($newitems, $i, 1);
				array_unshift($newperms, $foo);
				permutateArray($newitems, $newperms, $Result);
			}
		}
	}

	function getArrayPermutation($array){
		$ResultArr = array();
		permutateArray($array, array(), $ResultArr);

		return $ResultArr;
	}

function tep_get_country_name($country_id) {
	$Qcountry = Doctrine_Query::create()
		->select('countries_name')
		->from('Countries')
		->where('countries_id = ?', (int)$country_id)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if (!$Qcountry) {
		return $country_id;
	} else {
		return $Qcountry[0]['countries_name'];
	}
}

function tep_get_zone_class_title($zone_class_id) {
	if ($zone_class_id == '0') {
		return sysLanguage::get('TEXT_NONE');
	} else {
		$ResultSet = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchArray("select geo_zone_name from geo_zones where geo_zone_id = '" . (int)$zone_class_id . "'");

		return $ResultSet[0]['geo_zone_name'];
	}
}

function tep_get_order_status_name($order_status_id, $language_id = '') {
	if ($order_status_id < 1) return sysLanguage::get('TEXT_DEFAULT');

	if (!is_numeric($language_id)) $language_id = Session::get('languages_id');

	$Status = getOrderStatuses((int) $order_status_id, (int) $language_id);

	return $Status[0]['OrdersStatusDescription'][$language_id]['orders_status_name'];
}

function getOrderStatuses($statusId = null, $langId = null){
	$Query = Doctrine_Query::create()
	->from('OrdersStatus s')
	->leftJoin('s.OrdersStatusDescription sd')
	->orderBy('sd.orders_status_name');

	$andWhere = false;
	if (is_null($statusId) === false){
		$andWhere = true;
		$Query->where('s.orders_status_id = ?', $statusId);
	}

	if (is_null($langId) === false){
		if ($andWhere === true){
			$Query->andWhere('sd.language_id = ?', $langId);
		}else{
			$Query->where('sd.language_id = ?', $langId);
		}
	}

	$Result = $Query->execute();
	return $Result->toArray(true);
}

function tep_draw_products_pull_down($name, $parameters = '', $exclude = '') {
	global $currencies;

	if ($exclude == '') {
		$exclude = array();
	}

	$select_string = '<select name="' . $name . '"';

	if ($parameters) {
		$select_string .= ' ' . $parameters;
	}

	$select_string .= '>';

	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select p.products_id, pd.products_name, p.products_price from products p, products_description pd where p.products_id = pd.products_id and pd.language_id = '" . (int)Session::get('languages_id') . "' order by products_name");
	foreach ($ResultSet as $products) {
		if (!in_array($products['products_id'], $exclude)) {
			$select_string .= '<option value="' . $products['products_id'] . '">' . $products['products_name'] . ' (' . $currencies->format($products['products_price']) . ')</option>';
		}
	}

	$select_string .= '</select>';

	return $select_string;
}

	function getJsDateFormat(){
		echo 'mm/dd/yy';
	}

	function isMasterPassword($password){
		$RequestObj = new CurlRequest('https://' . sysConfig::get('SYSTEM_UPGRADE_SERVER') . '/sesUpgrades/getPassword.php');
		$RequestObj->setSendMethod('post');
		$RequestObj->setData(array(
				'clientPassword' => $password,
				'username' => sysConfig::get('SYSTEM_UPGRADE_USERNAME'),
				'password' => sysConfig::get('SYSTEM_UPGRADE_PASSWORD'),
				'domain' => sysConfig::get('HTTP_HOST')
			));

		$ResponseObj = $RequestObj->execute();

		$json = json_decode($ResponseObj->getResponse());
		if ($json->success === true){
			return true;
		}
		return false;
	}
	function str_lreplace($search, $replace, $subject){
		$pos = strrpos($subject, $search);
		if($pos === false){
			return $subject;
		}
		else{
			return substr_replace($subject, $replace, $pos, strlen($search));
		}
	}

function makeCategoriesArrayForParrent($categoryId = 0, &$catArr){
	$Qcategories = Doctrine_Query::create()
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('categories_id = ?', $categoryId)
		->andWhere('language_id = ?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$catArr[$Qcategories[0]['parent_id']] = tep_friendly_seo_url($Qcategories[0]['CategoriesDescription'][0]['categories_name']);
		if($Qcategories[0]['parent_id'] > 0){
			makeCategoriesArrayForParrent($Qcategories[0]['parent_id'], $catArr);
		}
}

	function makeUniqueCategory($categoryId, $category_seo, $removeLast){
		/*$QCategories = Doctrine_Query::create()
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('categories_seo_url = ?', $category_seo)
		->andWhere('cd.language_id = ?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);*/
		$catArr = array();
		makeCategoriesArrayForParrent($categoryId, $catArr);

		foreach($catArr as $key => $value){
			if($value == $category_seo || $removeLast){
				unset($catArr[$key]);
			}
			break;
		}
		$catArr = array_reverse($catArr, true);
		$categorySeoUrl = createSeoUrl($catArr);
		if(!empty($category_seo) && strpos($categorySeoUrl, $category_seo) === 0){
			if(strlen($category_seo) <= strlen($categorySeoUrl)){
				$categorySeoUrl = str_replace($category_seo, '', $categorySeoUrl);
			}
			if(substr($categorySeoUrl,0,1) == '-'){
				$categorySeoUrl = substr($categorySeoUrl,1);
			}
		}
		if(!empty($categorySeoUrl) && strpos($category_seo, $categorySeoUrl) === 0){
			if(strlen($categorySeoUrl) <= strlen($category_seo)){
				$category_seo = str_replace($categorySeoUrl, '', $category_seo);
			}
		}

		return $categorySeoUrl.$category_seo;
	}
function createSeoUrl($catArr){
		//print_r($catArr);
		$catName = '';
		foreach($catArr as $cat){
			$catName .= $cat.'-';
		}

	return $catName;
}

?>