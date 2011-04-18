////
//Return files stored in box that can be accessed by user
function tep_admin_files_boxes($filename, $sub_box_name) {
	$sub_boxes = false;

	if (is_array($filename)){
		$link = tep_href_link($filename[0], $filename[1]);
	}else{
		$link = tep_href_link($filename);
	}

	if (tep_admin_file_access_allowed($filename)){
		$sub_boxes = '<a href="' . $link . '" class="adminMenuLink">' . $sub_box_name . '</a>';
	}
	return $sub_boxes;
}

function tep_customers_name($customers_id) {
	$customers = tep_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customers_id . "'");
	$customers_values = tep_db_fetch_array($customers);

	return $customers_values['customers_firstname'] . ' ' . $customers_values['customers_lastname'];
}

function tep_options_name($options_id) {
	$options = tep_db_query("select products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . (int)$options_id . "' and language_id = '" . (int)Session::get('languages_id') . "'");
	$options_values = tep_db_fetch_array($options);

	return $options_values['products_options_name'];
}

function tep_values_name($values_id) {
	$values = tep_db_query("select products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . (int)$values_id . "' and language_id = '" . (int)Session::get('languages_id') . "'");
	$values_values = tep_db_fetch_array($values);

	return $values_values['products_options_values_name'];
}

function tep_browser_detect($component) {
	return stristr($_SERVER['HTTP_USER_AGENT'], $component);
}

function tep_get_category_name($category_id, $language_id) {
	$category_query = tep_db_query("select categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
	$category = tep_db_fetch_array($category_query);

	return $category['categories_name'];
}

function tep_get_category_description($category_id, $language_id){
	$category_query = tep_db_query("select categories_description from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
	$category = tep_db_fetch_array($category_query);

	return $category['categories_description'];
}

function tep_get_products_description($product_id, $language_id) {
	$product_query = tep_db_query("select products_description from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = tep_db_fetch_array($product_query);

	return $product['products_description'];
}

function tep_get_products_url($product_id, $language_id) {
	$product_query = tep_db_query("select products_url from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = tep_db_fetch_array($product_query);

	return $product['products_url'];
}

function tep_get_manufacturer_url($manufacturer_id, $language_id) {
	$manufacturer_query = tep_db_query("select manufacturers_url from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
	$manufacturer = tep_db_fetch_array($manufacturer_query);

	return $manufacturer['manufacturers_url'];
}

////
// Sets the status of a product on special
function tep_set_specials_status($specials_id, $status) {
	if ($status == '1') {
		return tep_db_query("update " . TABLE_SPECIALS . " set status = '1', expires_date = NULL, date_status_change = NULL where specials_id = '" . (int)$specials_id . "'");
	} elseif ($status == '0') {
		return tep_db_query("update " . TABLE_SPECIALS . " set status = '0', date_status_change = now() where specials_id = '" . (int)$specials_id . "'");
	} else {
		return -1;
	}
}

function tep_remove_category($category_id) {
	$category_image_query = tep_db_query("select categories_image from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$category_id . "'");
	$category_image = tep_db_fetch_array($category_image_query);

	$duplicate_image_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where categories_image = '" . tep_db_input($category_image['categories_image']) . "'");
	$duplicate_image = tep_db_fetch_array($duplicate_image_query);

	if ($duplicate_image['total'] < 2) {
		if (file_exists(DIR_FS_CATALOG_IMAGES . $category_image['categories_image'])) {
			@unlink(DIR_FS_CATALOG_IMAGES . $category_image['categories_image']);
		}
	}

	tep_db_query("delete from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$category_id . "'");
	tep_db_query("delete from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "'");
	tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . (int)$category_id . "'");

	if (USE_CACHE == 'true') {
		tep_reset_cache_block('categories');
		tep_reset_cache_block('also_purchased');
	}
}

function tep_remove_product($product_id) {
	$product_image_query = tep_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
	$product_image = tep_db_fetch_array($product_image_query);

	$duplicate_image_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " where products_image = '" . tep_db_input($product_image['products_image']) . "'");
	$duplicate_image = tep_db_fetch_array($duplicate_image_query);

	if ($duplicate_image['total'] < 2) {
		if (file_exists(DIR_FS_CATALOG_IMAGES . $product_image['products_image'])) {
			@unlink(DIR_FS_CATALOG_IMAGES . $product_image['products_image']);
		}
	}

	tep_db_query("delete from " . TABLE_SPECIALS . " where products_id = '" . (int)$product_id . "'");
	tep_db_query("delete from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
	tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$product_id . "'");
	tep_db_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "'");
	tep_db_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$product_id . "'");
	tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where products_id = '" . (int)$product_id . "' or products_id like '" . (int)$product_id . "{%'");
	tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where products_id = '" . (int)$product_id . "' or products_id like '" . (int)$product_id . "{%'");

	tep_db_query("delete from " . TABLE_WISHLIST . " where products_id = '" . (int)$product_id . "'");
	tep_db_query("delete from " . TABLE_WISHLIST_ATTRIBUTES . " where products_id = '" . (int)$product_id . "'");

	tep_db_query('delete from ' . TABLE_RENTAL_QUEUE . ' where products_id = "' . (int)$product_id . '"');

	$product_reviews_query = tep_db_query("select reviews_id from " . TABLE_REVIEWS . " where products_id = '" . (int)$product_id . "'");
	while ($product_reviews = tep_db_fetch_array($product_reviews_query)) {
		tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . (int)$product_reviews['reviews_id'] . "'");
	}
	tep_db_query("delete from " . TABLE_REVIEWS . " where products_id = '" . (int)$product_id . "'");

	if (USE_CACHE == 'true') {
		tep_reset_cache_block('categories');
		tep_reset_cache_block('also_purchased');
	}
}

function tep_get_file_permissions($mode) {
	// determine type
	if ( ($mode & 0xC000) == 0xC000) { // unix domain socket
		$type = 's';
	} elseif ( ($mode & 0x4000) == 0x4000) { // directory
		$type = 'd';
	} elseif ( ($mode & 0xA000) == 0xA000) { // symbolic link
		$type = 'l';
	} elseif ( ($mode & 0x8000) == 0x8000) { // regular file
		$type = '-';
	} elseif ( ($mode & 0x6000) == 0x6000) { //bBlock special file
		$type = 'b';
	} elseif ( ($mode & 0x2000) == 0x2000) { // character special file
		$type = 'c';
	} elseif ( ($mode & 0x1000) == 0x1000) { // named pipe
		$type = 'p';
	} else { // unknown
		$type = '?';
	}

	// determine permissions
	$owner['read']    = ($mode & 00400) ? 'r' : '-';
	$owner['write']   = ($mode & 00200) ? 'w' : '-';
	$owner['execute'] = ($mode & 00100) ? 'x' : '-';
	$group['read']    = ($mode & 00040) ? 'r' : '-';
	$group['write']   = ($mode & 00020) ? 'w' : '-';
	$group['execute'] = ($mode & 00010) ? 'x' : '-';
	$world['read']    = ($mode & 00004) ? 'r' : '-';
	$world['write']   = ($mode & 00002) ? 'w' : '-';
	$world['execute'] = ($mode & 00001) ? 'x' : '-';

	// adjust for SUID, SGID and sticky bit
	if ($mode & 0x800 ) $owner['execute'] = ($owner['execute'] == 'x') ? 's' : 'S';
	if ($mode & 0x400 ) $group['execute'] = ($group['execute'] == 'x') ? 's' : 'S';
	if ($mode & 0x200 ) $world['execute'] = ($world['execute'] == 'x') ? 't' : 'T';

	return $type .
	$owner['read'] . $owner['write'] . $owner['execute'] .
	$group['read'] . $group['write'] . $group['execute'] .
	$world['read'] . $world['write'] . $world['execute'];
}

function tep_get_zone_class_title($zone_class_id) {
	if ($zone_class_id == '0') {
		return sysLanguage::get('TEXT_NONE');
	} else {
		$classes_query = tep_db_query("select geo_zone_name from " . TABLE_GEO_ZONES . " where geo_zone_id = '" . (int)$zone_class_id . "'");
		$classes = tep_db_fetch_array($classes_query);

		return $classes['geo_zone_name'];
	}
}

function tep_get_order_status_name($order_status_id, $language_id = '') {
	if ($order_status_id < 1) return sysLanguage::get('TEXT_DEFAULT');

	if (!is_numeric($language_id)) $language_id = Session::get('languages_id');

	$status_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . (int)$order_status_id . "' and language_id = '" . (int)$language_id . "'");
	$status = tep_db_fetch_array($status_query);

	return $status['orders_status_name'];
}

// rmh referral
function tep_get_sources_name($source_id, $customers_id) {

	if ($source_id == '9999') {
		$sources_query = tep_db_query("select sources_other_name as sources_name from " . TABLE_SOURCES_OTHER . " where customers_id = '" . (int)$customers_id . "'");
	} else {
		$sources_query = tep_db_query("select sources_name from " . TABLE_SOURCES . " where sources_id = '" . (int)$source_id . "'");
	}

	if (!tep_db_num_rows($sources_query)) {
		if ($source_id == '9999') {
			return sysLanguage::get('TEXT_OTHER');
		} else {
			return sysLanguage::get('TEXT_NONE');
		}
	} else {
		$sources = tep_db_fetch_array($sources_query);
		return $sources['sources_name'];
	}
}

function tep_get_category_htc_title($category_id, $language_id) {
	$category_query = tep_db_query("select categories_htc_title_tag from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
	$category = tep_db_fetch_array($category_query);

	return $category['categories_htc_title_tag'];
}

function tep_get_category_htc_desc($category_id, $language_id) {
	$category_query = tep_db_query("select categories_htc_desc_tag from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
	$category = tep_db_fetch_array($category_query);

	return $category['categories_htc_desc_tag'];
}

function tep_get_category_htc_keywords($category_id, $language_id) {
	$category_query = tep_db_query("select categories_htc_keywords_tag from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
	$category = tep_db_fetch_array($category_query);

	return $category['categories_htc_keywords_tag'];
}

function tep_get_category_htc_description($category_id, $language_id) {
	$category_query = tep_db_query("select categories_htc_description from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
	$category = tep_db_fetch_array($category_query);

	return $category['categories_htc_description'];
}

function tep_get_products_head_title_tag($product_id, $language_id) {
	$product_query = tep_db_query("select products_head_title_tag from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = tep_db_fetch_array($product_query);

	return $product['products_head_title_tag'];
}

function tep_get_products_head_desc_tag($product_id, $language_id) {
	$product_query = tep_db_query("select products_head_desc_tag from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = tep_db_fetch_array($product_query);

	return $product['products_head_desc_tag'];
}

function tep_get_products_head_keywords_tag($product_id, $language_id) {
	$product_query = tep_db_query("select products_head_keywords_tag from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = tep_db_fetch_array($product_query);

	return $product['products_head_keywords_tag'];
}
function tep_get_manufacturer_htc_title($manufacturer_id, $language_id) {
	$manufacturer_query = tep_db_query("select manufacturers_htc_title_tag from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
	$manufacturer = tep_db_fetch_array($manufacturer_query);

	return $manufacturer['manufacturers_htc_title_tag'];
}

function tep_get_manufacturer_htc_desc($manufacturer_id, $language_id) {
	$manufacturer_query = tep_db_query("select manufacturers_htc_desc_tag from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
	$manufacturer = tep_db_fetch_array($manufacturer_query);

	return $manufacturer['manufacturers_htc_desc_tag'];
}

function tep_get_manufacturer_htc_keywords($manufacturer_id, $language_id) {
	$manufacturer_query = tep_db_query("select manufacturers_htc_keywords_tag from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
	$manufacturer = tep_db_fetch_array($manufacturer_query);

	return $manufacturer['manufacturers_htc_keywords_tag'];
}

function tep_get_manufacturer_htc_description($manufacturer_id, $language_id) {
	$manufacturer_query = tep_db_query("select manufacturers_htc_description from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
	$manufacturer = tep_db_fetch_array($manufacturer_query);

	return $manufacturer['manufacturers_htc_description'];
}

function tep_reset_cache_data_seo_urls($action){
	switch ($action){
		case 'reset':
			tep_db_query("DELETE FROM cache WHERE cache_name LIKE '%seo_urls%'");
			tep_db_query("UPDATE configuration SET configuration_value='false' WHERE configuration_key='SEO_URLS_CACHE_RESET'");
			break;
		default:
			break;
	}
	# The return value is used to set the value upon viewing
	# It's NOT returining a false to indicate failure!!
	return 'false';
}

function tep_get_category_seo_url($category_id, $language_id) {
	$category_query = tep_db_query("select categories_seo_url from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
	$category = tep_db_fetch_array($category_query);

	return $category['categories_seo_url'];
}


function tep_get_products_seo_url($product_id, $language_id = 0) {
	if ($language_id == 0) $language_id = Session::get('languages_id');
	$product_query = tep_db_query("select products_seo_url from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = tep_db_fetch_array($product_query);

	return $product['products_seo_url'];
}

function tep_create_sort_heading($sortby, $colnum, $heading) {
	$sort_prefix = '';
	$sort_suffix = '';

	if ($sortby) {
		$sort_prefix = '<a href="' . tep_href_link(basename($_SERVER['PHP_SELF']), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=' . $colnum . ($sortby == $colnum . 'a' ? 'd' : 'a')) . '" title="' . tep_output_string(TEXT_SORT_PRODUCTS . ($sortby == $colnum . 'd' || substr($sortby, 0, 1) != $colnum ? sysLanguage::get('TEXT_ASCENDINGLY') : sysLanguage::get('TEXT_DESCENDINGLY')) . TEXT_BY . $heading) . '" class="productListing-heading">' ;
		$sort_suffix = (substr($sortby, 0, 1) == $colnum ? (substr($sortby, 1, 1) == 'a' ? '+' : '-') : '') . '</a>';
	}

	return $sort_prefix . $heading . $sort_suffix;
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

function tep_remove_order($order_id, $restock = false) {
	if ($restock == 'on') {
		$order_query = tep_db_query("select products_id, products_quantity from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$order_id . "'");
		while ($order = tep_db_fetch_array($order_query)) {
			tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = products_quantity + " . $order['products_quantity'] . ", products_ordered = products_ordered - " . $order['products_quantity'] . " where products_id = '" . (int)$order['products_id'] . "'");
		}
	}

	tep_db_query("delete from " . TABLE_ORDERS . " where orders_id = '" . (int)$order_id . "'");
	tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$order_id . "'");
	tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . (int)$order_id . "'");
	tep_db_query("delete from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . (int)$order_id . "'");
	tep_db_query("delete from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$order_id . "'");

	tep_db_query("delete from rental_bookings where orders_id = '" . (int)$order_id . "'");
}

function tep_array_unique($somearray)
{
	if ($somearray)
	{
		$tmparr = array_unique($somearray);
		$i = 0;

		foreach ($tmparr as $v)
		{
			$newarr[$i] = $v;
			$i++;
		}
		return $newarr;
	}
}

function update_status_rental($products_id = false){
	$priorities = array();
	$QmaxPriority = tep_db_query('select priority, ratio from ratio_priority order by priority');
	while($maxPriority = tep_db_fetch_array($QmaxPriority)){
		$priorities[(int)$maxPriority['priority']] = $maxPriority['ratio'];
	}

	$queryRaw = 'select products_id, products_type from ' . TABLE_PRODUCTS;
	if ($products_id !== false){
		$queryRaw .= ' where products_id = "' . $products_id . '"';
	}

	$brokenProds = array();
	$pInfo = array();
	$customers = array();

	$query = tep_db_query($queryRaw);
	while($record = tep_db_fetch_array($query)){
		$productsID = (int)$record['products_id'];

		$QlastPurchase = tep_db_query('select o.date_purchased from orders_products op inner join orders o on op.orders_id = o.orders_id where op.products_id = "' . $productsID . '" group by op.products_id order by o.date_purchased desc limit 1');
		$lastPurchase = tep_db_fetch_array($QlastPurchase);

		$Qstock = tep_db_query('select count(products_id) as products_quantity from ' . TABLE_PRODUCTS_BARCODE . ' where products_id = "' . $productsID . '" and products_type = "rental" and products_broken = "0" group by products_id');
		$stock = tep_db_fetch_array($Qstock);

		$Qbroken = tep_db_query('select count(products_id) as broken from ' . TABLE_PRODUCTS_BARCODE . ' where products_id = "' . $productsID . '" and products_broken = "1" group by products_id');
		$broken = tep_db_fetch_array($Qbroken);

		$pInfo[$productsID] = array(
		'inQueue' => 0,
		'broken'  => (int)$broken['broken'],
		'ratio'   => 0,
		'qty'     => (int)$stock['products_quantity'],
		'Oqty'    => (int)$oneTimeStock['products_quantity'],
		'lastP'   => $lastPurchase['date_purchased']
		);

		$QcustomerQueue = tep_db_query('select products_id, customers_id, priority from rental_queue where products_id = "' . $productsID . '"');
		while($customerQueue = tep_db_fetch_array($QcustomerQueue)){
			if (!isset($priorities[(int)$customerQueue['priority']])){
				$priorities[(int)$customerQueue['priority']] = .1;
			}

			if (!isset($customers[$customerQueue['customers_id']])){
				$Qcustomer = tep_db_query('select activate from ' . TABLE_CUSTOMERS . ' where customers_id = "' . $customerQueue['customers_id'] . '"');
				$customer = tep_db_fetch_array($Qcustomer);

				$customers[$customerQueue['customers_id']] = ($customer['activate'] == 'Y' ? true : false);
			}

			if ($customers[$customerQueue['customers_id']] === true){
				$pInfo[$productsID]['inQueue']++;
				$pInfo[$productsID]['ratio'] += $priorities[(int)$customerQueue['priority']];
			}
		}

		$pInfo[$productsID]['ratio'] = ($pInfo[$productsID]['ratio'] - $pInfo[$productsID]['qty']);
	}
	if (empty($pInfo)){
		return false;
	}

	foreach($pInfo as $pID => $prod){
		tep_db_query('update ' . TABLE_PRODUCTS .
		' set ' .
		'products_broken = "' . $prod['broken'] . '", ' .
		'products_ratio = "' . $prod['ratio'] . '", ' .
		'products_in_queue = "' . $prod['inQueue'] . '", ' .
		'products_last_sold = "' . $prod['lastP'] . '"' .
		' where ' .
		'products_id = "' . $pID . '"');
	}
}
