<?php
/*
  $Id: general.php,v 1.231 2003/07/09 01:15:48 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

////
// Stop from parsing any further PHP code
  function tep_exit() {
   Session::stop();
   exit();
  }
  
  function itwExit() {
   Session::stop();
   exit();
  }

function unparse_url($parsed_url) {
	$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
	$host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
	$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
	$user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
	$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
	$pass     = ($user || $pass) ? "$pass@" : '';
	$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
	$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
	$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
	return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
}

function getLayout($app, $page, $ext){
	$thisApp = $app;
	$thisTemplate = 'codeGeneration';
	$thisExtension = $ext;
	$thisAppPage = $page;

	$Qpages = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select layout_id from template_pages where extension = "' . $thisExtension . '" and application = "' . $thisApp . '" and page = "' . $thisAppPage . '"');

	$Page = $Qpages[0];
	//$pageLayouts = $Page['layout_id'];

	$QtemplateId = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select template_id from template_manager_templates_configuration where configuration_key = "DIRECTORY" and configuration_value = "' . $thisTemplate . '"');
	$TemplateId = $QtemplateId[0];

	$Page['layout_id'] = implode(',',array_filter(explode(',',$Page['layout_id'])));
	if(isset($Page['layout_id']) && !empty($Page['layout_id'])){
		$QpageLayout = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select layout_id from template_manager_layouts where template_id = "' . $TemplateId['template_id'] . '" and layout_id IN(' . $Page['layout_id'] . ')');
	}
	if(isset($QpageLayout) && sizeof($QpageLayout) > 0){
		$PageLayoutId = $QpageLayout[0];
		$layout_id = $PageLayoutId['layout_id'];
	}else{
		$layout_id = '';
	}
	return $layout_id;

}

function getAssoc($app, $page, $ext){
	$thisApp = $app;
	$thisExtension = $ext;
	$thisAppPage = $page;

	//echo $app.'-'.$page;
	$Qpages = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select associative_url from template_pages where extension = "' . $thisExtension . '" and application = "' . $thisApp . '" and page = "' . $thisAppPage . '"');
	$Page = $Qpages[0];
	$pageAssocUrl = explode(',',$Page['associative_url']);
	$i = count($pageAssocUrl) - 1;
	while($pageAssocUrl[$i] == '' && $i >=0){
		$i--;
	}
	return $pageAssocUrl[$i];


}

function tep_redirect($url) {

    if ( (strstr($url, "\n") != false) || (strstr($url, "\r") != false) ) { 
      tep_redirect(itw_app_link(null, 'index', 'default', 'NONSSL', false));
    }

	if ((sysConfig::get('ENABLE_SSL') == true) && (getenv('HTTPS') == 'on')){ // We are loading an SSL page
		if (substr($url, 0, strlen(sysConfig::get('HTTP_SERVER'))) == sysConfig::get('HTTP_SERVER')){ // NONSSL url
			$url = sysConfig::get('HTTPS_SERVER') . substr($url, strlen(sysConfig::get('HTTP_SERVER'))); // Change it to SSL
      }
    }
	$parsedUrl = parse_url($url);
	if(isset($_GET['tplDir']) && $_GET['tplDir'] == 'codeGeneration'){
		$urlElements = explode('/', $parsedUrl['path']);
		if(count($urlElements) == 3){
			$encUrl = getAssoc($urlElements[1],$urlElements[2], $urlElements[0]);
		}else{
			$encUrl = getAssoc($urlElements[0],$urlElements[1], '');
		}
		if($encUrl != ''){
			$parsedUrl['query'] .= '&redirectUrl='.urlencode($encUrl);
		}
	}

    header('Location: ' . unparse_url($parsedUrl));

    tep_exit();
  }
/*
////
function tep_redirect($url) {
	global $SID;
  //$test = explode(tep_session_name,$url);
  	//if(count($test)<2)
//echo '<pre>';print_r($_SERVER);echo '</pre>';
//echo $url;
	if(strstr($url,tep_session_name())===FALSE)
  	{
		if(strstr($url,'?')!=false)
			$url .= '&'.tep_session_name().'='.session_id();
		else
			$url .= '?'.tep_session_name().'='.session_id();
	}

  if ( (strstr($url, "\n") != false) || (strstr($url, "\r") != false) ) {
    tep_redirect(itw_app_link(null, 'index', 'default', 'NONSSL', false));
  }
  header('Location: ' . $url);

  tep_exit();
}
*/
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
    $string = ereg_replace(' +', ' ', trim($string));

    return preg_replace("/[<>]/", '_', $string);
  }

////
// Return a random row from a database query
  function tep_random_select($query) {
    $random_product = '';

	$Random = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc($query);

    if (sizeof($Random) > 0) {
      $random_row = tep_rand(0, (sizeof($Random) - 1));
      $random_product = $Random[$random_row];
    }

    return $random_product;
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

////
// Return a product's name
// TABLES: products
  function tep_get_products_name($product_id, $language = '') {
    if (empty($language)) $language = Session::get('languages_id');

	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select products_name from products_description where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language . "'");

	return $ResultSet[0]['products_name'];
  }

////
// Break a word in a string if it is longer than a specified length ($len)
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

////
// Return all HTTP GET variables, except those passed as a parameter
function tep_get_all_get_params($exclude_array = '') {
	if (!is_array($exclude_array)) $exclude_array = array();

	$get_url = '';
	if (is_array($_GET) && (sizeof($_GET) > 0)) {
		reset($_GET);
		while (list($key, $value) = each($_GET)) {
			if (is_array($value)){
				foreach($value as $k => $v){
					if (!in_array($key . '[' . $k . ']', $exclude_array)){
						$get_url .= $key . '[' . $k . ']=' . rawurlencode(stripslashes($v)) . '&';
					}
				}
			}else{
				if ( (strlen($value) > 0) && ($key != Session::getSessionName()) && ($key != 'error') && (!in_array($key, $exclude_array)) && ($key != 'x') && ($key != 'y') ) {
					$get_url .= $key . '=' . rawurlencode(stripslashes($value)) . '&';
				}
			}
		}
	}

	return $get_url;
}

// Returns an array with countries
// TABLES: countries

  function tep_get_countries($countries_id = '', $with_iso_codes = false)
{
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

////
// Alias function to tep_get_countries, which also returns the countries iso codes
  function tep_get_countries_with_iso_codes($countries_id) {
    return tep_get_countries($countries_id, true);
  }

////
// Generate a path to categories
  function tep_get_path($current_category_id = '') {
    global $cPath_array;

    if (tep_not_null($current_category_id)) {
      $cp_size = sizeof($cPath_array);
      if ($cp_size == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';
			$Categories = Doctrine_Core::getTable('Categories')->getRecordInstance();

			$lastParent = $Categories->getParentId((int) $cPath_array[(sizeof($cPath_array)-1)]);
			$currentParent = $Categories->getParentId((int) $current_category_id);

        if ($lastParent == $currentParent) {
          for ($i=0; $i<($cp_size-1); $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        } else {
          for ($i=0; $i<$cp_size; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        }
        $cPath_new .= '_' . $current_category_id;

        if (substr($cPath_new, 0, 1) == '_') {
          $cPath_new = substr($cPath_new, 1);
        }
      }
    } else {
      $cPath_new = implode('_', $cPath_array);
    }

    return 'cPath=' . $cPath_new;
  }

////
// Returns the clients browser
  function tep_browser_detect($component) {
    return stristr($_SERVER['HTTP_USER_AGENT'], $component);
  }

////
// Alias function to tep_get_countries()
  function tep_get_country_name($country_id) {
    $country_array = tep_get_countries($country_id);
    return $country_array[0]['countries_name'];
  }

////
// Returns the zone (State/Province) name
// TABLES: zones
function tep_get_zone_name($country_id, $zone_id, $default_zone) {
	$Zones = Doctrine_Core::getTable('Zones')->getRecordInstance();

	$zoneName = $Zones->getZoneName((int)$zone_id, (int)$country_id);
	if ($zoneName){
		return $zoneName;
	}
	else {
		return $default_zone;
	}
}

////
// Returns the zone (State/Province) code
// TABLES: zones
function tep_get_zone_code($country_id, $zone_id, $default_zone) {
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchArray("select zone_code from zones where zone_country_id = '" . (int)$country_id . "' and zone_id = '" . (int)$zone_id . "'");

	if (sizeof($ResultSet) <= 0){
		$state_prov_code = $default_zone;
	}
	else {
		$state_prov_code = $ResultSet[0]['zone_code'];
	}

	return $state_prov_code;
}

////
// Wrapper function for round()
  function tep_round($number, $precision) {
    if (strpos($number, '.') && (strlen(substr($number, strpos($number, '.')+1)) > $precision)) {
      $number = substr($number, 0, strpos($number, '.') + 1 + $precision + 1);

      if (substr($number, -1) >= 5) {
        if ($precision > 1) {
          $number = substr($number, 0, -1) + ('0.' . str_repeat(0, $precision-1) . '1');
        } elseif ($precision == 1) {
          $number = substr($number, 0, -1) + 0.1;
        } else {
          $number = substr($number, 0, -1) + 1;
        }
      } else {
        $number = substr($number, 0, -1);
      }
    }

    return $number;
  }

////
// Returns the tax rate for a zone / class
// TABLES: tax_rates, zones_to_geo_zones
  function tep_get_tax_rate($class_id, $country_id = -1, $zone_id = -1) {
    global $userAccount;
    if ( ($country_id == -1) && ($zone_id == -1) ) {
      if ($userAccount->isLoggedIn() === false) {
        $country_id = sysConfig::get('STORE_COUNTRY');
        $zone_id = sysConfig::get('STORE_ZONE');
      } else {
        $cInfo = $userAccount->getCustomerInfo();
        $country_id = $cInfo['countryId'];
        $zone_id = $cInfo['zoneId'];
      }
    }

    //$tax_query = tep_db_query("select sum(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . (int)$country_id . "') and (za.zone_id is null or za.zone_id < '1' or za.zone_id = '" . (int)$zone_id . "') and tr.tax_class_id = '" . (int)$class_id . "' group by tr.tax_priority");
	  $QtaxRates = Doctrine_Query::create()
	->select('r.tax_rates_id, z.geo_zone_id, z.geo_zone_name, z.geo_zone_description, tc.tax_class_title, tc.tax_class_id, r.tax_priority, r.tax_rate, r.tax_description, r.date_added, r.last_modified')
	->from('TaxRates r')
	->leftJoin('r.TaxClass tc')
	->leftJoin('r.GeoZones z')
	->leftJoin('z.ZonesToGeoZones zt')
	->where('zt.zone_country_id is null or zt.zone_country_id = "0" or zt.zone_country_id = "' . (int)$country_id . '"')
	->andWhere('r.tax_class_id = ?',(int)$class_id)
	->andWhere('(zt.zone_id= "' . $zone_id . '" OR zt.zone_id < 1)')
	->orderBy('r.tax_priority')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	 $taxes = 0;
	 if (count($QtaxRates) > 0) {
	    foreach($QtaxRates as $trInfo) {
    		$taxes += $trInfo['tax_rate'];
	 	}
		return $taxes;
	 }
	 return $taxes;
  }

////
// Return the tax description for a zone / class
// TABLES: tax_rates;
  function tep_get_tax_description($class_id, $country_id, $zone_id) {	  
	$QtaxRates = Doctrine_Query::create()
	->select('r.tax_rates_id, z.geo_zone_id, z.geo_zone_name, z.geo_zone_description, tc.tax_class_title, tc.tax_class_id, r.tax_priority, r.tax_rate, r.tax_description, r.date_added, r.last_modified')
	->from('TaxRates r')
	->leftJoin('r.TaxClass tc')
	->leftJoin('r.GeoZones z')
	->leftJoin('z.ZonesToGeoZones zt')
	->where('zt.zone_country_id is null or zt.zone_country_id = "0" or zt.zone_country_id = "' . (int)$country_id . '"')
	->andWhere('r.tax_class_id = ?',(int)$class_id)
	->andWhere('(zt.zone_id="' . $zone_id . '" OR zt.zone_id < 1)')
	->orderBy('r.tax_priority')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

    if (count($QtaxRates) > 0) {
      $tax_description = '';
      foreach($QtaxRates as $trInfo) {
          if (empty($trInfo['tax_description'])){
              $desc = $trInfo['GeoZones']['geo_zone_description'] . ' tax';
          }else{
              $desc = $trInfo['tax_description'];
          }

        $tax_description .= $desc . ' + ';
      }
      $tax_description = substr($tax_description, 0, -3);

      return $tax_description;
    } else {
      return sysLanguage::get('TEXT_UNKNOWN_TAX_RATE');
    }
  }

////
// Add tax to a products price
function tep_add_tax($price, $tax) {
	global $currencies;
	//die(DISPLAY_PRICE_WITH_TAX);

	if ((sysConfig::get('DISPLAY_PRICE_WITH_TAX') == 'true') && ($tax > 0)){
		return tep_round($price, $currencies->currencies[Session::get('currency')]['decimal_places']) + tep_calculate_tax($price, $tax);
	}
	else {
		return tep_round($price, $currencies->currencies[Session::get('currency')]['decimal_places']);
	}
}

// Calculates Tax rounding the result
function tep_calculate_tax($price, $tax) {
	global $currencies;

	return tep_round($price * $tax / 100, $currencies->currencies[Session::get('currency')]['decimal_places']);
}

////
// Return the number of products in a category
// TABLES: products, products_to_categories, categories
function tep_count_products_in_category($category_id, $include_inactive = false) {
	$products_count = 0;
	if ($include_inactive == true){
		$query = "select count(*) as total from products p, products_to_categories p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$category_id . "'";
	}
	else {
		$query = "select count(*) as total from products p, products_to_categories p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '" . (int)$category_id . "'";
	}
	$Products = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchArray($query);
	$products_count += $Products[0]['total'];

	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select categories_id from categories where parent_id = '" . (int)$category_id . "'");
	if (sizeof($ResultSet) > 0){
		foreach($ResultSet as $child_categories){
			$products_count += tep_count_products_in_category($child_categories['categories_id'], $include_inactive);
		}
	}

	return $products_count;
}

////
// Return true if the category has subcategories
// TABLES: categories
function tep_has_category_subcategories($category_id) {
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchArray("select count(*) as count from categories where parent_id = '" . (int)$category_id . "'");

	if ($ResultSet[0]['count'] > 0){
		return true;
	}
	else {
		return false;
	}
}

////
// Returns the address_format_id for the given country
// TABLES: countries;
function tep_get_address_format_id($country_id) {
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchArray("select address_format_id as format_id from countries where countries_id = '" . (int)$country_id . "'");
	if (sizeof($ResultSet) > 0){
		return $ResultSet[0]['format_id'];
	}
	else {
		return '1';
	}
}

//
//Return a formatted address
// TABLES: address_format
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
		if (isset($address['entry_firstname']) && tep_not_null($address['entry_firstname'])) {
			$firstname = $address['entry_firstname'];
			$lastname = $address['entry_lastname'];
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
	    $country = '';
	    $abbrstate = '';
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

/////////////

// Return a formatted address
// TABLES: customers, address_book
function tep_address_label($customers_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n") {
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchArray("select entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customers_id . "' and address_book_id = '" . (int)$address_id . "'");

	$format_id = tep_get_address_format_id($ResultSet[0]['country_id']);

    return tep_address_format($format_id, $address, $html, $boln, $eoln);
  }

// Return a formatted address
// TABLES: customers, address_book
function tep_center_address_label($address_id = 1, $html = false, $boln = '', $eoln = "\n") {
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchArray("select inventory_center_address from products_inventory_centers where inventory_center_id = '" . (int)$address_id . "'");

	return nl2br($ResultSet[0]['inventory_center_address']);
}

function tep_row_number_format($number) {
	if (($number < 10) && (substr($number, 0, 1) != '0')) {
		$number = '0' . $number;
	}

    return $number;
  }

function tep_get_categories($categories_array = '', $parent_id = '0', $indent = '') {
	if (!is_array($categories_array)) {
		$categories_array = array();
	}

	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select c.categories_id, cd.categories_name from categories c, categories_description cd where parent_id = '" . (int)$parent_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)Session::get('languages_id') . "' order by sort_order, cd.categories_name");
	foreach($ResultSet as $categories){
		$categories_array[] = array('id'   => $categories['categories_id'],
		                            'text' => $indent . $categories['categories_name']);

		if ($categories['categories_id'] != $parent_id){
			$categories_array = tep_get_categories($categories_array, $categories['categories_id'], $indent . '&nbsp;&nbsp;');
		}
	}

	return $categories_array;
}

////
// Return all subcategory IDs
// TABLES: categories
function tep_get_subcategories(&$subcategories_array, $parent_id = 0) {
	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select categories_id from categories where parent_id = '" . (int)$parent_id . "'");
	foreach($ResultSet as $subcategories){
		$subcategories_array[sizeof($subcategories_array)] = $subcategories['categories_id'];
		if ($subcategories['categories_id'] != $parent_id){
			tep_get_subcategories($subcategories_array, $subcategories['categories_id']);
		}
	}
}

// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
function tep_date_long($raw_date) {
	if (($raw_date == '0000-00-00 00:00:00') || ($raw_date == '')) {
		return false;
	}

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
	if (($raw_date == '0000-00-00 00:00:00') || empty($raw_date)) {
		return false;
	}

	$year = substr($raw_date, 0, 4);
	$month = (int)substr($raw_date, 5, 2);
	$day = (int)substr($raw_date, 8, 2);
	$hour = (int)substr($raw_date, 11, 2);
	$minute = (int)substr($raw_date, 14, 2);
	$second = (int)substr($raw_date, 17, 2);

	if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year){
		return date(sysLanguage::getDateFormat(), mktime($hour, $minute, $second, $month, $day, $year));
	}
	else {
		return ereg_replace('2037' . '$', $year, date(sysLanguage::getDateFormat(), mktime($hour, $minute, $second, $month, $day, 2037)));
	}
}

////
// Parse search string into indivual objects
  function tep_parse_search_string($search_str = '', &$objects) {
    $search_str = trim(strtolower($search_str));

// Break up $search_str on whitespace; quoted string will be reconstructed later
    $pieces = split('[[:space:]]+', $search_str);
    $objects = array();
    $tmpstring = '';
    $flag = '';

    for ($k=0; $k<count($pieces); $k++) {
      while (substr($pieces[$k], 0, 1) == '(') {
        $objects[] = '(';
        if (strlen($pieces[$k]) > 1) {
          $pieces[$k] = substr($pieces[$k], 1);
        } else {
          $pieces[$k] = '';
        }
      }

      $post_objects = array();

      while (substr($pieces[$k], -1) == ')')  {
        $post_objects[] = ')';
        if (strlen($pieces[$k]) > 1) {
          $pieces[$k] = substr($pieces[$k], 0, -1);
        } else {
          $pieces[$k] = '';
        }
      }

// Check individual words

      if ( (substr($pieces[$k], -1) != '"') && (substr($pieces[$k], 0, 1) != '"') ) {
        $objects[] = trim($pieces[$k]);

        for ($j=0; $j<count($post_objects); $j++) {
          $objects[] = $post_objects[$j];
        }
      } else {
/* This means that the $piece is either the beginning or the end of a string.
   So, we'll slurp up the $pieces and stick them together until we get to the
   end of the string or run out of pieces.
*/

// Add this word to the $tmpstring, starting the $tmpstring
        $tmpstring = trim(ereg_replace('"', ' ', $pieces[$k]));

// Check for one possible exception to the rule. That there is a single quoted word.
        if (substr($pieces[$k], -1 ) == '"') {
// Turn the flag off for future iterations
          $flag = 'off';

          $objects[] = trim($pieces[$k]);

          for ($j=0; $j<count($post_objects); $j++) {
            $objects[] = $post_objects[$j];
          }

          unset($tmpstring);

// Stop looking for the end of the string and move onto the next word.
          continue;
        }

// Otherwise, turn on the flag to indicate no quotes have been found attached to this word in the string.
        $flag = 'on';

// Move on to the next word
        $k++;

// Keep reading until the end of the string as long as the $flag is on

        while ( ($flag == 'on') && ($k < count($pieces)) ) {
          while (substr($pieces[$k], -1) == ')') {
            $post_objects[] = ')';
            if (strlen($pieces[$k]) > 1) {
              $pieces[$k] = substr($pieces[$k], 0, -1);
            } else {
              $pieces[$k] = '';
            }
          }

// If the word doesn't end in double quotes, append it to the $tmpstring.
          if (substr($pieces[$k], -1) != '"') {
// Tack this word onto the current string entity
            $tmpstring .= ' ' . $pieces[$k];

// Move on to the next word
            $k++;
            continue;
          } else {
/* If the $piece ends in double quotes, strip the double quotes, tack the
   $piece onto the tail of the string, push the $tmpstring onto the $haves,
   kill the $tmpstring, turn the $flag "off", and return.
*/
            $tmpstring .= ' ' . trim(ereg_replace('"', ' ', $pieces[$k]));

// Push the $tmpstring onto the array of stuff to search for
            $objects[] = trim($tmpstring);

            for ($j=0; $j<count($post_objects); $j++) {
              $objects[] = $post_objects[$j];
            }

            unset($tmpstring);

// Turn off the flag to exit the loop
            $flag = 'off';
          }
        }
      }
    }

// add default logical operators if needed
    $temp = array();
    for($i=0; $i<(count($objects)-1); $i++) {
      $temp[] = $objects[$i];
      if ( ($objects[$i] != 'and') &&
           ($objects[$i] != 'or') &&
           ($objects[$i] != '(') &&
           ($objects[$i+1] != 'and') &&
           ($objects[$i+1] != 'or') &&
           ($objects[$i+1] != ')') ) {
        $temp[] = ADVANCED_SEARCH_DEFAULT_OPERATOR;
      }
    }
    $temp[] = $objects[$i];
    $objects = $temp;

    $keyword_count = 0;
    $operator_count = 0;
    $balance = 0;
    for($i=0; $i<count($objects); $i++) {
      if ($objects[$i] == '(') $balance --;
      if ($objects[$i] == ')') $balance ++;
      if ( ($objects[$i] == 'and') || ($objects[$i] == 'or') ) {
        $operator_count ++;
      } elseif ( ($objects[$i]) && ($objects[$i] != '(') && ($objects[$i] != ')') ) {
        $keyword_count ++;
      }
    }

    if ( ($operator_count < $keyword_count) && ($balance == 0) ) {
      return true;
    } else {
      return false;
    }
  }

////
// Check date
  function tep_checkdate($date_to_check, $format_string, &$date_array) {
    $separator_idx = -1;

    $separators = array('-', ' ', '/', '.');
    $month_abbr = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
    $no_of_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    $format_string = strtolower($format_string);

    if (strlen($date_to_check) != strlen($format_string)) {
      return false;
    }

    $size = sizeof($separators);
    for ($i=0; $i<$size; $i++) {
      $pos_separator = strpos($date_to_check, $separators[$i]);
      if ($pos_separator != false) {
        $date_separator_idx = $i;
        break;
      }
    }

    for ($i=0; $i<$size; $i++) {
      $pos_separator = strpos($format_string, $separators[$i]);
      if ($pos_separator != false) {
        $format_separator_idx = $i;
        break;
      }
    }

    if ($date_separator_idx != $format_separator_idx) {
      return false;
    }

    if ($date_separator_idx != -1) {
      $format_string_array = explode( $separators[$date_separator_idx], $format_string );
      if (sizeof($format_string_array) != 3) {
        return false;
      }

      $date_to_check_array = explode( $separators[$date_separator_idx], $date_to_check );
      if (sizeof($date_to_check_array) != 3) {
        return false;
      }

      $size = sizeof($format_string_array);
      for ($i=0; $i<$size; $i++) {
        if ($format_string_array[$i] == 'mm' || $format_string_array[$i] == 'mmm') $month = $date_to_check_array[$i];
        if ($format_string_array[$i] == 'dd') $day = $date_to_check_array[$i];
        if ( ($format_string_array[$i] == 'yyyy') || ($format_string_array[$i] == 'aaaa') ) $year = $date_to_check_array[$i];
      }
    } else {
      if (strlen($format_string) == 8 || strlen($format_string) == 9) {
        $pos_month = strpos($format_string, 'mmm');
        if ($pos_month != false) {
          $month = substr( $date_to_check, $pos_month, 3 );
          $size = sizeof($month_abbr);
          for ($i=0; $i<$size; $i++) {
            if ($month == $month_abbr[$i]) {
              $month = $i;
              break;
            }
          }
        } else {
          $month = substr($date_to_check, strpos($format_string, 'mm'), 2);
        }
      } else {
        return false;
      }

      $day = substr($date_to_check, strpos($format_string, 'dd'), 2);
      $year = substr($date_to_check, strpos($format_string, 'yyyy'), 4);
    }

    if (strlen($year) != 4) {
      return false;
    }

    if (!settype($year, 'integer') || !settype($month, 'integer') || !settype($day, 'integer')) {
      return false;
    }

    if ($month > 12 || $month < 1) {
      return false;
    }

    if ($day < 1) {
      return false;
    }

    if (tep_is_leap_year($year)) {
      $no_of_days[1] = 29;
    }

    if ($day > $no_of_days[$month - 1]) {
      return false;
    }

    $date_array = array($year, $month, $day);

    return true;
  }

////
// Check if year is a leap year
  function tep_is_leap_year($year) {
    if ($year % 100 == 0) {
      if ($year % 400 == 0) return true;
    } else {
      if (($year % 4) == 0) return true;
    }

    return false;
  }

////
// Return table heading with sorting capabilities
  function tep_create_sort_heading($sortby, $colnum, $heading) {
    $sort_prefix = '';
    $sort_suffix = '';

    if ($sortby) {
      $sort_prefix = '<a href="' . itw_app_link(tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=' . $colnum . ($sortby == $colnum . 'a' ? 'd' : 'a')) . '" title="' . tep_output_string(TEXT_SORT_PRODUCTS . ($sortby == $colnum . 'd' || substr($sortby, 0, 1) != $colnum ? sysLanguage::get('TEXT_ASCENDINGLY') : sysLanguage::get('TEXT_DESCENDINGLY')) . TEXT_BY . $heading) . '" class="productListing-heading">' ;
      $sort_suffix = (substr($sortby, 0, 1) == $colnum ? (substr($sortby, 1, 1) == 'a' ? '+' : '-') : '') . '</a>';
    }

    return $sort_prefix . $heading . $sort_suffix;
  }

////
// Recursively go through the categories and retreive all parent categories IDs
// TABLES: categories
function tep_get_parent_categories(&$categories, $categories_id) {
	$Parents = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select parent_id from categories where categories_id = '" . (int)$categories_id . "'");
	foreach($Parents as $parent_categories){
		if ($parent_categories['parent_id'] == 0) {
			return true;
		}
		$categories[sizeof($categories)] = $parent_categories['parent_id'];
		if ($parent_categories['parent_id'] != $categories_id){
			tep_get_parent_categories($categories, $parent_categories['parent_id']);
		}
	}
}

////
// Construct a category path to the product
// TABLES: products_to_categories
function tep_get_product_path($products_id) {
	$cPath = '';

	$Category = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select p2c.categories_id from products p, products_to_categories p2c where p.products_id = '" . (int)$products_id . "' and p.products_status = '1' and p.products_id = p2c.products_id limit 1");
	if (sizeof($Category) > 0){
		$categories = array();
		tep_get_parent_categories($categories, $Category[0]['categories_id']);

		$categories = array_reverse($categories);

		$cPath = implode('_', $categories);

		if (tep_not_null($cPath)) {
			$cPath .= '_';
		}
		$cPath .= $Category[0]['categories_id'];
	}

	return $cPath;
}

////
// Return a customer greeting
  function tep_customer_greeting() {
    global $userAccount;
    if ($userAccount->isLoggedIn() === true) {
      $greeting_string = sprintf(sysLanguage::get('TEXT_GREETING_PERSONAL'), tep_output_string_protected($userAccount->getFirstName()), itw_app_link(null, 'products', 'new'));
    } else {
      $greeting_string = sprintf(sysLanguage::get('TEXT_GREETING_GUEST'), itw_app_link(null, 'account', 'login', 'SSL'), itw_app_link(null, 'account', 'create', 'SSL'));
    }

    return $greeting_string;
  }

////
//! Send email (text/html) using MIME
// This is the central mail function. The SMTP Server should be configured
// correct in php.ini
// Parameters:
// $to_name           The name of the recipient, e.g. "Jan Wildeboer"
// $to_email_address  The eMail address of the recipient,
//                    e.g. jan.wildeboer@gmx.de
// $email_subject     The subject of the eMail
// $email_text        The text of the eMail, may contain HTML entities
// $from_email_name   The name of the sender, e.g. Shop Administration
// $from_email_adress The eMail address of the sender,
//                    e.g. info@mytepshop.com

function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address, $attachments = '') {
	if (sysConfig::get('SEND_EMAILS') != 'true') {
		return false;
	}

    // Instantiate a new mail object
    $message = new email(array('X-Mailer: osCommerce Mailer'));

    // Build the text version
    $text = strip_tags($email_text);
    if (sysConfig::get('EMAIL_USE_HTML') == 'true') {
      $message->add_html($email_text, $text);
    } else {
      $message->add_text($text);
    }
	if(!empty($attachments)){
		//check for application extensions zip, pdf etc
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

////
// Get the number of times a word/character is present in a string
  function tep_word_count($string, $needle) {
    $temp_array = split($needle, $string);

    return sizeof($temp_array);
  }

function tep_create_random_value($length, $type = 'mixed') {
	if (($type != 'mixed') && ($type != 'chars') && ($type != 'digits')) {
		return false;
	}

	$rand_value = '';
	while(strlen($rand_value) < $length){
		if ($type == 'digits'){
			$char = tep_rand(0, 9);
		}
		else {
			$char = chr(tep_rand(0, 255));
		}
		if ($type == 'mixed'){
			if (preg_match('/^[a-z0-9]$/i', $char)) {
				$rand_value .= $char;
			}
		}
		elseif ($type == 'chars') {
			if (preg_match('/^[a-z]$/i', $char)) {
				$rand_value .= $char;
			}
		}
		elseif ($type == 'digits') {
			if (preg_match('/^[0-9]$/', $char)) {
				$rand_value .= $char;
			}
		}
	}

	return $rand_value;
}

function tep_array_to_string($array, $exclude = '', $equals = '=', $separator = '&') {
	if (!is_array($exclude)) {
		$exclude = array();
	}

	$get_string = '';
	if (sizeof($array) > 0){
		while(list($key, $value) = each($array)){
			if ((!in_array($key, $exclude)) && ($key != 'x') && ($key != 'y')){
				$get_string .= $key . $equals . $value . $separator;
			}
		}
		$remove_chars = strlen($separator);
		$get_string = substr($get_string, 0, -$remove_chars);
	}

	return $get_string;
}

function tep_not_null($value) {
	if (is_array($value)){
		if (sizeof($value) > 0){
			return true;
		}
		else {
			return false;
		}
	}
	else {
		if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)){
			return true;
		}
		else {
			return false;
		}
	}
}

////
// Output the tax percentage with optional padded decimals
function tep_display_tax_value($value, $padding = false) {
	if ($padding === false){
		$padding = sysConfig::get('TAX_DECIMAL_PLACES');
	}
	
	if (strpos($value, '.')){
		$loop = true;
		while($loop){
			if (substr($value, -1) == '0'){
				$value = substr($value, 0, -1);
			}
			else {
				$loop = false;
				if (substr($value, -1) == '.'){
					$value = substr($value, 0, -1);
				}
			}
		}
	}

	if ($padding > 0){
		if ($decimal_pos = strpos($value, '.')){
			$decimals = strlen(substr($value, ($decimal_pos + 1)));
			for($i = $decimals; $i < $padding; $i++){
				$value .= '0';
			}
		}
		else {
			$value .= '.';
			for($i = 0; $i < $padding; $i++){
				$value .= '0';
			}
		}
	}

	return $value;
}

function hex2bin($h) {
	if (!is_string($h)) {
		return null;
	}
	$r = '';
	for($a = 0; $a < strlen($h); $a += 2){
		$r .= chr(hexdec($h{$a} . $h{($a + 1)}));
	}
	return $r;
}

////
// Checks to see if the currency code exists as a currency
// TABLES: currencies
function tep_currency_exists($code) {
	$code = addslashes($code);

	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select currencies_id from currencies where code = '" . $code . "'");
	if (sizeof($ResultSet) > 0){
		return $code;
	}
	else {
		return false;
	}
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

////
// Return a random value
  function tep_rand($min = null, $max = null) {
    static $seeded;

    if (!isset($seeded)) {
      mt_srand((double)microtime()*1000000);
      $seeded = true;
    }

    if (isset($min) && isset($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
  }

  function tep_setcookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = 0) {
    setcookie($name, $value, $expire, $path, (tep_not_null($domain) ? $domain : ''), $secure);
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

  function tep_count_customer_orders($id = '', $check_session = true) {
    global $userAccount;

    if (is_numeric($id) == false) {
      if ($userAccount->isLoggedIn() === true) {
        $id = $userAccount->getCustomerId();
      } else {
        return 0;
      }
    }

    if ($check_session == true) {
      if ( ($userAccount->isLoggedIn() === false) || ($id != $userAccount->getCustomerId()) ) {
        return 0;
      }
    }

	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select count(*) as total from orders where customers_id = '" . (int)$id . "'");

	return $ResultSet[0]['total'];
}

  function tep_count_customer_address_book_entries($id = '', $check_session = true) {
    global $userAccount;

    if (is_numeric($id) == false) {
      if ($userAccount->isLoggedIn() === true) {
        $id = $userAccount->getCustomerId();
      } else {
        return 0;
      }
    }

    if ($check_session == true) {
      if ( ($userAccount->isLoggedIn() === false) || ($id != $userAccount->getCustomerId()) ) {
        return 0;
      }
    }

	$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select count(*) as total from address_book where customers_id = '" . (int)$id . "'");

	return $ResultSet[0]['total'];
}

// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)
  function tep_convert_linefeeds($from, $to, $string) {
    if ((PHP_VERSION < "4.0.5") && is_array($from)) {
      return ereg_replace('(' . implode('|', $from) . ')', $to, $string);
    } else {
      return str_replace($from, $to, $string);
    }
  }


	function addChildren($child, $currentPath, &$ulElement, $catArrExcl = array()) {
		global $current_category_id;
		//$currentPath .= '_' . $child['categories_id'];

		$childLinkEl = htmlBase::newElement('a')
		->addClass('ui-widget ui-widget-content ui-corner-all')
		->css('border-color', 'transparent')
		->html('<span class="ui-icon ui-icon-triangle-1-e ui-icon-categories-bullet" style="vertical-align:middle;"></span><span style="display:inline-block;vertical-align:middle;">' . $child['CategoriesDescription'][Session::get('languages_id')]['categories_name'] . '</span>')
		->setHref(itw_app_link(null, 'index', $child['CategoriesDescription'][Session::get('languages_id')]['categories_seo_url']));
			
		if ($child['categories_id'] == $current_category_id){
			$childLinkEl->addClass('selected');
		}
		
		$Qchildren = Doctrine_Query::create()
		->select('c.categories_id, cd.categories_name,cd.categories_seo_url, c.parent_id')
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('c.parent_id = ?', $child['categories_id'])
		->andWhereNotIn('c.categories_id', $catArrExcl)
		->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
		->orderBy('c.sort_order, cd.categories_name');
							
		EventManager::notify('CategoryQueryBeforeExecute', $Qchildren);

		$currentParentChildren = $Qchildren->execute()->toArray(true);

		$children = false;
		if ($currentParentChildren){
			$childLinkEl
			->html(
			'<span style="float:right;" class="ui-icon ui-icon-triangle-1-e"></span>' . 
			'<span style="line-height:1.5em;">' . 
				'<span class="ui-icon ui-icon-triangle-1-e ui-icon-categories-bullet" style="vertical-align:middle;"></span>' .
				'<span style="vertical-align:middle;">' . 
					$child['CategoriesDescription'][Session::get('languages_id')]['categories_name'] . 
				'</span>' . 
			'</span>');
			
			$children = htmlBase::newElement('list')
			->addClass('ui-widget ui-widget-content ui-corner-all ui-menu-flyout')
			->css('display', 'none');
			foreach($currentParentChildren as $childInfo){
				addChildren($childInfo, $currentPath, &$children);
			}
		}
		
		$liElement = htmlBase::newElement('li')
		->append($childLinkEl);
		if ($children){
			$liElement->append($children);
		}
		if ($ulElement->hasListItems()){
			/*$liElement->css(array(
				'border-top' => '1px solid #313332'
			));*/
		}
		$ulElement->addItemObj($liElement);
	}  
	
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
	function getJsDateFormat(){
		echo 'mm/dd/yy';
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

function rating_bar($name, $product_id) {
	global $userAccount;
	if (sysConfig::get('EXTENSION_REVIEWS_ENABLED') != 'True') return '';

	$units = 5;
	$voted = false;
	$current_rating = 0;

	if ($userAccount->isLoggedIn()){
		$QcustomerRating = Doctrine_Query::create()
			->select('reviews_rating')
			->from('Reviews')
			->where('products_id = ?', $product_id)
			->andWhere('customers_id = ?', $userAccount->getCustomerId())
			->execute();
		if ($QcustomerRating !== false){
			$current_rating = number_format($QcustomerRating[0]->reviews_rating, 1);
		}
	}else{
		$Qtotals = Doctrine_Query::create()
			->select('avg(reviews_rating) as total')
			->from('Reviews')
			->where('products_id = ?', $product_id)
			->groupBy('products_id')
			->execute();
		if ($Qtotals->count()){
			$current_rating = number_format($Qtotals[0]->total, 1);
		}
	}

	$inputFields = '';
	for($i=1; $i<11; $i++){
		$inputFields .= tep_draw_radio_field('star_rating_' . $product_id, $i, ($current_rating == ($i/2)), 'id="star_rating_' . $product_id . '_' . $i . '" style="display:none;"');
	}
	return '<br /><table style="margin:0 auto;"><tr><td align="left"><div class="starRating starRating_' . $product_id . '" products_id="' . $product_id . '" style="width:' . ($userAccount->isLoggedIn() === true ? (17*6) : (16*5)) . 'px;">
        ' . $inputFields . '
      </div><div style="clear:both;"></div></td></tr></table>';
}
function request_uri(){
	if (isset($_SERVER['REQUEST_URI'])){
		$uri = $_SERVER['REQUEST_URI'];
	}else{
		if (isset($_SERVER['argv'])){
			$uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
		}else{
			$uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
		}
	}
	return $uri;
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

function tep_update_whos_online() {
	// WOL 1.6 - Need access to spider_flag and user_agent and moved some assignments up here from below
	global $spider_flag, $user_agent, $userAccount;

	if (
		basename($_SERVER['PHP_SELF']) == 'stylesheet.php' ||
		basename($_SERVER['PHP_SELF']) == 'javascript.php' ||
		isset($_GET['action'])
	){
		return;
	}

	$wo_ip_address = tep_get_ip_address();
	$wo_last_page_url = request_uri();
	$current_time = time();
	$xx_mins_ago = ($current_time - 900);
	$wo_session_id = Session::getSessionId();
	$user_agent = getenv('HTTP_USER_AGENT');
	$wo_user_agent = $user_agent;
	// WOL 1.6 EOF

	if ($userAccount->getCustomerId() > 0){
		$wo_customer_id = $userAccount->getCustomerId();
		$wo_full_name = $userAccount->getFullName();
	}else{
		if ($spider_flag || strpos($user_agent, "Googlebot") > 0){
			// Bots are customerID = -1
			$wo_customer_id = -1;

			// The Bots name is extracted from the User Agent in the WOE Admin screen
			$wo_full_name = $user_agent;

			// Session IDs are the WOE primary key.  If a Bot doesn't have a session (normally shouldn't),
			//   use the IP Address as unique identifier, otherwise, use the session ID
			if ($wo_session_id == ""){
				$wo_session_id = $wo_ip_address;
			}
		}else{
			// Must be a Guest
			$wo_full_name = 'Guest';
			$wo_customer_id = 0;
		}
		// WOL 1.6 EOF
	}

	// remove entries that have expired
	Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->exec('delete from whos_online where time_last_click < "' . $xx_mins_ago . '"');

	/**
	 * @TODO: Fix This
	 */

	$WhosOnline = Doctrine_Core::getTable('WhosOnline');
	$Entry = $WhosOnline->findOneBySessionId($wo_session_id);
	if (!$Entry){
		$Entry = $WhosOnline->create();
		$Entry->session_id = $wo_session_id;
	}
	$Entry->customer_id = (int)$wo_customer_id;
	$Entry->full_name = $wo_full_name;
	$Entry->ip_address = $wo_ip_address;
	$Entry->time_entry = $current_time;
	$Entry->time_last_click = $current_time;
	$Entry->last_page_url = $wo_last_page_url;
	$Entry->http_referer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
	$Entry->user_agent = $user_agent;
	$Entry->save();
}


function tep_validate_email($email) {
	$valid_address = true;

	$mail_pat = '^(.+)@(.+)$';
	$valid_chars = "[^] \(\)<>@,;:\.\\\"\[]";
	$atom = "$valid_chars+";
	$quoted_user='(\"[^\"]*\")';
	$word = "($atom|$quoted_user)";
	$user_pat = "^$word(\.$word)*$";
	$ip_domain_pat='^\[([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\]$';
	$domain_pat = "^$atom(\.$atom)*$";

	if (eregi($mail_pat, $email, $components)) {
		$user = $components[1];
		$domain = $components[2];
		// validate user
		if (eregi($user_pat, $user)) {
			// validate domain
			if (eregi($ip_domain_pat, $domain, $ip_components)) {
				// this is an IP address
				for ($i=1;$i<=4;$i++) {
					if ($ip_components[$i] > 255) {
						$valid_address = false;
						break;
					}
				}
			}
			else {
				// Domain is a name, not an IP
				if (eregi($domain_pat, $domain)) {
					/* domain name seems valid, but now make sure that it ends in a valid TLD or ccTLD
								   and that there's a hostname preceding the domain or country. */
					$domain_components = explode(".", $domain);
					// Make sure there's a host name preceding the domain.
					if (sizeof($domain_components) < 2) {
						$valid_address = false;
					} else {
						$top_level_domain = strtolower($domain_components[sizeof($domain_components)-1]);
						// Allow all 2-letter TLDs (ccTLDs)
						if (eregi('^[a-z][a-z]$', $top_level_domain) != 1) {
							$tld_pattern = '';
							// Get authorized TLDs from text file
							$tlds = file(DIR_WS_INCLUDES . 'tld.txt');
							while (list(,$line) = each($tlds)) {
								// Get rid of comments
								$words = explode('#', $line);
								$tld = trim($words[0]);
								// TLDs should be 3 letters or more
								if (eregi('^[a-z]{3,}$', $tld) == 1) {
									$tld_pattern .= '^' . $tld . '$|';
								}
							}
							// Remove last '|'
							$tld_pattern = substr($tld_pattern, 0, -1);
							if (eregi("$tld_pattern", $top_level_domain) == 0) {
								$valid_address = false;
							}
						}
					}
				}
				else {
					$valid_address = false;
				}
			}
		}
		else {
			$valid_address = false;
		}
	}
	else {
		$valid_address = false;
	}
	if ($valid_address && ENTRY_EMAIL_ADDRESS_CHECK == 'true') {
		if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
			$valid_address = false;
		}
	}
	return $valid_address;
}

function tep_validate_password($plain, $encrypted) {
	if (tep_not_null($plain) && tep_not_null($encrypted)) {
		// split apart the hash / salt
		$stack = explode(':', $encrypted);

		if (sizeof($stack) != 2) return false;

		if (md5($stack[1] . $plain) == $stack[0]) {
			return true;
		}
	}

	return false;
}

////
// This function makes a new password from a plaintext password.
function tep_encrypt_password($plain) {
	$password = '';

	for ($i=0; $i<10; $i++) {
		$password .= tep_rand();
	}

	$salt = substr(md5($password), 0, 2);

	$password = md5($salt . $plain) . ':' . $salt;

	return $password;
}

?>
