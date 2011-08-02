<?php
/*
$Id: html_output.php,v 1.56 2003/07/09 01:15:48 hpdl Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2003 osCommerce

Released under the GNU General Public License
*/

////
// Ultimate SEO URLs v2.1
// The HTML href link wrapper function
function tep_href_link($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
	global $seo_urls;
	if ($page == 'checkout.php'){
		return itw_app_link($parameters, 'checkout', 'default', $connection);
	}
	if ( !is_object($seo_urls) ){
		if ( !class_exists('SEO_URL') ){
			include_once(DIR_WS_CLASSES . 'seo.class.php');
		}
		$seo_urls = new SEO_URL(Session::get('languages_id'));
	}
	return $seo_urls->href_link($page, $parameters, $connection, $add_session_id);
}

function seoUrlClean($val){
	return urlencode(str_replace(array(' ', ')', '(', '/', '\\', '.'), '-', $val));
}

function buildAppLink($o){
	static $productNameResults;
	$envDir = ($o['env'] == 'catalog' ? sysConfig::getDirWsCatalog() : sysConfig::getDirWsAdmin());
	
	if ($o['connection'] == 'NONSSL') {
		$link = sysConfig::get('HTTP_SERVER') . $envDir;
	} elseif ($o['connection'] == 'SSL') {
		if (sysConfig::get('ENABLE_SSL') == 'true') {
			$link = sysConfig::get('HTTPS_SERVER') . $envDir;
		} else {
			$link = sysConfig::get('HTTP_SERVER') . $envDir;
		}
	}

	if (is_null($o['app'])){
		$o['app'] = $_GET['app'];
	}

	if (is_null($o['page'])){
		$o['page'] = $_GET['appPage'];
	}
	
	$paramsParsed = false;
	if (!is_null($o['params'])){
		parse_str($o['params'], $vars);
		$paramsParsed = true;
		if ($o['app'] == 'product' && isset($vars['products_id'])){
			if (!isset($productNameResults[(int)$vars['products_id']])){
				$Qproduct = Doctrine_Query::create()
				->select('products_name')
				->from('ProductsDescription')
				->where('products_id = ?', (int)$vars['products_id'])
				->andWhere('language_id = ?', Session::get('languages_id'))
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			
				$productName = seoUrlClean($Qproduct[0]['products_name']);
				$productNameResults[(int)$vars['products_id']]['products_name'] = $productName;
			}else{
				$productName = $productNameResults[(int)$vars['products_id']]['products_name'];
			}
			
			if (!empty($productName)){
				$link .= $o['app'] . '/' . $o['page'] . '/';
				$o['app'] = (int)$vars['products_id'];
				$o['page'] = $productName;
				unset($vars['products_id']);
				if (sizeof($vars) <= 0){
					$paramsParsed = false;
				}
			}
		}else{
			if (isset($vars['appExt'])){
				$link .= $vars['appExt'] . '/';
			}
		}
	}
	
	$link .= $o['app'] . '/' . $o['page'] . '.php';
	
	if ($paramsParsed === true){
		if (isset($vars['app'])) unset($vars['app']);
		if (isset($vars['appPage'])) unset($vars['appPage']);
		if (isset($vars['appExt'])) unset($vars['appExt']);
	}
		
	if (isset($vars) && sizeof($vars) > 0){
		$link .= '?' . urldecode(http_build_query($vars)) . '&' . SID;
		if ($o['connection'] == 'SSL'){
			$link .= '&' . Session::getSessionName() . '=' . Session::getSessionId();
		}
	}else{
		if ($o['connection'] == 'SSL'){
			$link .= '?' . Session::getSessionName() . '=' . Session::getSessionId();
		}
	}

	while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);
	return $link;
}

function itw_app_link($params=null, $appName=null, $appPage=null, $connection='NONSSL'){
	global $request_type;
	$appExt = null;
	if (!is_null($params)){
		parse_str($params, $vars);
		$paramsParsed = true;
		if (isset($vars['appExt'])){
			$appExt = $vars['appExt'];
		}

		if (isset($vars['rType'])){
			if ($vars['rType'] == 'ajax' && $connection == 'NONSSL' && $request_type == 'SSL'){
				$connection = 'SSL';
			}
		}
	}
	return buildAppLink(array(
		'connection' => $connection,
		'params'     => $params,
		'app'        => $appName,
		'ext'        => $appExt,
		'page'       => $appPage,
		'env'        => 'catalog'
	));
}

function itw_catalog_app_link($params=null, $appName=null, $appPage=null, $connection='NONSSL'){
	$appExt = null;
	if (!is_null($params)){
		parse_str($params, $vars);
		$paramsParsed = true;
		if (isset($vars['appExt'])){
			$appExt = $vars['appExt'];
		}
	}
	return buildAppLink(array(
		'connection' => $connection,
		'params'     => $params,
		'app'        => $appName,
		'ext'        => $appExt,
		'page'       => $appPage,
		'env'        => 'catalog'
	));
}

function itw_admin_app_link($params=null, $appName=null, $appPage=null, $connection='NONSSL'){
	$appExt = null;
	if (!is_null($params)){
		parse_str($params, $vars);
		$paramsParsed = true;
		if (isset($vars['appExt'])){
			$appExt = $vars['appExt'];
		}
	}
	return buildAppLink(array(
		'connection' => $connection,
		'params'     => $params,
		'app'        => $appName,
		'ext'        => $appExt,
		'page'       => $appPage,
		'env'        => 'admin'
	));
}

////
// The HTML image wrapper function
// "On the Fly" Auto Thumbnailer using GD Library, servercaching and browsercaching
// Scales product images dynamically, resulting in smaller file sizes, and keeps
// proper image ratio. Used in conjunction with product_thumb.php t/n generator.
function tep_image($src, $alt = '', $width = '', $height = '', $params = '') {
	if (empty($src) || $src == DIR_WS_IMAGES || !file_exists(DIR_FS_CATALOG . $src)) return;
	// Set default image variable and code
	$image = '<img src="' . $src . '"';

	// Don't calculate if the image is set to a "%" width
	if (strstr($width,'%') == false || strstr($height,'%') == false) {
		$dont_calculate = 0;
	} else {
		$dont_calculate = 1;
	}

	// Dont calculate if a pixel image is being passed (hope you dont have pixels for sale)
	if (!strstr($image, 'pixel')) {
		$dont_calculate = 0;
	} else {
		$dont_calculate = 1;
	}

	// Do we calculate the image size?
	if (CONFIG_CALCULATE_IMAGE_SIZE && !$dont_calculate) {

		// Get the image's information
		if ($image_size = @getimagesize($src)) {

			$ratio = $image_size[1] / $image_size[0];

			// Set the width and height to the proper ratio
			if (!$width && $height) {
				$ratio = $height / $image_size[1];
				$width = intval($image_size[0] * $ratio);
			} elseif ($width && !$height) {
				$ratio = $width / $image_size[0];
				$height = intval($image_size[1] * $ratio);
			} elseif (!$width && !$height) {
				$width = $image_size[0];
				$height = $image_size[1];
			}

			// Scale the image if not the original size
			if ($image_size[0] != $width || $image_size[1] != $height) {
				$rx = $image_size[0] / $width;
				$ry = $image_size[1] / $height;

				if ($rx < $ry) {
					$width = intval($height / $ratio);
				} else {
					$height = intval($width * $ratio);
				}

				$image = '<img src="product_thumb.php?img='.$src.'&amp;w='.
				tep_output_string($width).'&amp;h='.tep_output_string($height).'"';
			}

		} elseif (IMAGE_REQUIRED == 'false') {
			return '';
		}
	}

	// Add remaining image parameters if they exist
	if ($width) {
		$image .= ' width="' . tep_output_string($width) . '"';
	}

	if ($height) {
		$image .= ' height="' . tep_output_string($height) . '"';
	}

	if (tep_not_null($params)) $image .= ' ' . $params;

	$image .= ' alt="' . tep_output_string($alt) . '"';

	if (tep_not_null($alt)) {
		$image .= ' title="' . tep_output_string($alt) . '"';
	}

	$image .= ' />';

	return $image;
}

////
// Output a separator either through whitespace, or with an image
function tep_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') {
	return tep_image(DIR_WS_IMAGES . $image, '', $width, $height);
}

////
// Output a form
function tep_draw_form($name, $action, $method = 'post', $parameters = '') {
	
	$form = '<form name="' . tep_output_string($name) . '" action="' . tep_output_string($action) . '" method="' . tep_output_string($method) . '"';

	if (tep_not_null($parameters)) $form .= ' ' . $parameters;

	$form .= '>';

	return $form;
}

////
// Output a form input field
function tep_draw_input_field($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true) {
	$field = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';

	if ( (isset($GLOBALS[$name])) && ($reinsert_value == true) ) {
		$field .= ' value="' . tep_output_string(stripslashes($GLOBALS[$name])) . '"';
	} elseif (tep_not_null($value)) {
		$field .= ' value="' . tep_output_string($value) . '"';
	}

	if (tep_not_null($parameters)) $field .= ' ' . $parameters;

	$field .= ' />';

	return $field;
}

////
// Output a form password field
function tep_draw_password_field($name, $value = '', $parameters = 'maxlength="40"') {
	return tep_draw_input_field($name, $value, $parameters, 'password', false);
}

////
// Output a selection field - alias function for tep_draw_checkbox_field() and tep_draw_radio_field()
function tep_draw_selection_field($name, $type, $value = '', $checked = false, $parameters = '') {
	$selection = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';

	if (tep_not_null($value)) $selection .= ' value="' . tep_output_string($value) . '"';

	if ( ($checked == true) || ( isset($GLOBALS[$name]) && is_string($GLOBALS[$name]) && ( ($GLOBALS[$name] == 'on') || (isset($value) && (stripslashes($GLOBALS[$name]) == $value)) ) ) ) {
		$selection .= ' CHECKED';
	}

	if (tep_not_null($parameters)) $selection .= ' ' . $parameters;

	$selection .= ' />';

	return $selection;
}

////
// Output a form checkbox field
function tep_draw_checkbox_field($name, $value = '', $checked = false, $parameters = '') {
	return tep_draw_selection_field($name, 'checkbox', $value, $checked, $parameters);
}

////
// Output a form radio field
function tep_draw_radio_field($name, $value = '', $checked = false, $parameters = '') {
	return tep_draw_selection_field($name, 'radio', $value, $checked, $parameters);
}

////
// Output a form textarea field
function tep_draw_textarea_field($name, $wrap, $width, $height, $text = '', $parameters = '', $reinsert_value = true) {
	$field = '<textarea name="' . tep_output_string($name) . '" wrap="' . tep_output_string($wrap) . '" cols="' . tep_output_string($width) . '" rows="' . tep_output_string($height) . '"';

	if (tep_not_null($parameters)) $field .= ' ' . $parameters;

	$field .= '>';

	if ( (isset($GLOBALS[$name])) && ($reinsert_value == true) ) {
		$field .= tep_output_string_protected(stripslashes($GLOBALS[$name]));
	} elseif (tep_not_null($text)) {
		$field .= tep_output_string_protected($text);
	}

	$field .= '</textarea>';

	return $field;
}

////
// Output a form hidden field
function tep_draw_hidden_field($name, $value = '', $parameters = '') {
	$field = '<input type="hidden" name="' . tep_output_string($name) . '"';

	if (tep_not_null($value)) {
		$field .= ' value="' . tep_output_string($value) . '"';
	} elseif (isset($GLOBALS[$name])) {
		$field .= ' value="' . tep_output_string(stripslashes($GLOBALS[$name])) . '"';
	}

	if (tep_not_null($parameters)) $field .= ' ' . $parameters;

	$field .= ' />';

	return $field;
}

////
// Hide form elements
function tep_hide_session_id() {
	global $session_started, $SID;

	if (($session_started == true) && tep_not_null($SID)) {
		return tep_draw_hidden_field(Session::getSessionName(), Session::getSessionId());
	}
}

////
// Output a form pull down menu
function tep_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {
	$field = '<select name="' . tep_output_string($name) . '"';

	if (tep_not_null($parameters)) $field .= ' ' . $parameters;

	$field .= '>';

	if (empty($default) && isset($GLOBALS[$name])) $default = stripslashes($GLOBALS[$name]);

	for ($i=0, $n=sizeof($values); $i<$n; $i++) {
		$field .= '<option value="' . tep_output_string($values[$i]['id']) . '"';
		if ($default == $values[$i]['id']) {
			$field .= ' SELECTED';
		}

		$field .= '>' . tep_output_string($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
	}
	$field .= '</select>';

	if ($required == true) $field .= sysLanguage::get('TEXT_FIELD_REQUIRED');

	return $field;
}

////
// Creates a pull-down list of countries
function tep_get_country_list($name, $selected = '', $parameters = '', $only = '')
{
	if (!$only)
	$countries_array = array(array('id' => '', 'text' => sysLanguage::get('PULL_DOWN_DEFAULT')));
	$countries = tep_get_countries($only);

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


////rmh referral
// Creates a pull-down list of sources
function tep_get_source_list($name, $show_other = false, $selected = '', $parameters = '') {
	$sources_array = array(array('id' => '', 'text' => sysLanguage::get('PULL_DOWN_DEFAULT')));
	$sources = tep_get_sources();

	for ($i=0, $n=sizeof($sources); $i<$n; $i++) {
		$sources_array[] = array('id' => $sources[$i]['sources_id'], 'text' => $sources[$i]['sources_name']);
	}

	if ($show_other == 'true') {
		$sources_array[] = array('id' => '9999', 'text' => sysLanguage::get('PULL_DOWN_OTHER'));
	}

	return tep_draw_pull_down_menu($name, $sources_array, $selected, $parameters);
}

function itw_template_button($text, $parameters = ''){
	return '<button class="ui-button ui-state-default ui-corner-all" type="button"' . ($parameters != '' ? ' ' . $parameters : '') . '><span>' . $text . '</span></button>';
}

function itw_template_submit_button($text, $parameters = ''){
	return '<button class="ui-button ui-state-default ui-corner-all" type="submit"' . ($parameters != '' ? ' ' . $parameters : '') . '><span>' . $text . '</span></button>';
}
