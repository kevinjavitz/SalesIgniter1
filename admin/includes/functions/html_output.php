<?php
/*
	SalesIgniter E-Commerce System Version 1
	
	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	function tep_href_link($page = '', $parameters = '', $connection = 'NONSSL') {
		if ($connection == 'NONSSL') {
			$link = sysConfig::get('HTTP_SERVER') . sysConfig::get('DIR_WS_ADMIN');
		} elseif ($connection == 'SSL') {
			if (sysConfig::get('ENABLE_SSL_CATALOG') == 'true') {
				$link = sysConfig::get('HTTPS_SERVER') . sysConfig::get('DIR_WS_ADMIN');
			} else {
				$link = sysConfig::get('HTTP_SERVER') . sysConfig::get('DIR_WS_ADMIN');
			}
		}

		if ($page == 'application.php' || $page == null){
			parse_str($parameters, $params);
			if (array_key_exists('appExt', $params)){
				$link .= $params['appExt'] . '/';
				unset($params['appExt']);
			}
			$link = $link . $params['app'] . '/' . $params['appPage'] . '.php';
			unset($params['app']);unset($params['appPage']);
			if (sizeof($params) > 0){
				$link .= '?' . http_build_query($params) . '&' . SID;
			}else{
				$link .= '?' . SID;
			}
		}else{
			if ($parameters == '') {
				$link = $link . $page . '?' . SID;
			} else {
				$link = $link . $page . '?' . $parameters . '&' . SID;
			}
		}

		while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);
		return $link;
	}

	function buildAppLink($o){
		$envDir = ($o['env'] == 'catalog' ? sysConfig::getDirWsCatalog() : sysConfig::getDirWsAdmin());

		if ($o['connection'] == 'NONSSL') {
			$link = sysConfig::get('HTTP_SERVER') . $envDir;
		} elseif ($o['connection'] == 'SSL') {
			if (sysConfig::get('ENABLE_SSL_CATALOG') == 'true') {
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
			if (array_key_exists('appExt', $vars)){
				$link .= $vars['appExt'] . '/';
			}
		}

		$link .= $o['app'] . '/' . $o['page'] . '.php';

		if ($paramsParsed === true){
			if (array_key_exists('app', $vars)) unset($vars['app']);
			if (array_key_exists('appPage', $vars)) unset($vars['appPage']);
			if (array_key_exists('appExt', $vars)) unset($vars['appExt']);

			if (sizeof($vars) > 0){
				$link .= '?' . urldecode(http_build_query($vars)) . '&' . SID;
			}else{
				$link .= '?' . SID;
			}
		}

		while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);
		return $link;
	}

	function itw_app_link($params=null, $appName=null, $appPage=null, $connection='SSL'){
		$appExt = null;
		if (!is_null($params)){
			parse_str($params, $vars);
			$paramsParsed = true;
			if (array_key_exists('appExt', $vars)){
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

	function itw_catalog_app_link($params=null, $appName=null, $appPage=null, $connection='SSL'){
		$appExt = null;
		if (!is_null($params)){
			parse_str($params, $vars);
			$paramsParsed = true;
			if (array_key_exists('appExt', $vars)){
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

	function tep_catalog_href_link($page = '', $parameters = '', $connection = 'NONSSL') {
		if ($connection == 'NONSSL') {
			$link = sysConfig::get('HTTP_CATALOG_SERVER') . sysConfig::get('DIR_WS_CATALOG');
		} elseif ($connection == 'SSL') {
			if (sysConfig::get('ENABLE_SSL_CATALOG') == 'true') {
				$link = sysConfig::get('HTTPS_CATALOG_SERVER') . sysConfig::get('DIR_WS_CATALOG');
			} else {
				$link = sysConfig::get('HTTP_CATALOG_SERVER') . sysConfig::get('DIR_WS_CATALOG');
			}
		} else {
			die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine connection method on a link!<br><br>Known methods: NONSSL SSL<br><br>Function used:<br><br>tep_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
		}
		if ($parameters == '') {
			$link .= $page;
		} else {
			$link .= $page . '?' . $parameters;
		}

		while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

		return $link;
	}

	////
	// The HTML image wrapper function
	function tep_image($src, $alt = '', $width = '', $height = '', $params = '') {
		$image = '<img src="' . $src . '" border="0" alt="' . $alt . '"';
		if ($alt) {
			$image .= ' title=" ' . $alt . ' "';
		}
		if ($width) {
			$image .= ' width="' . $width . '"';
		}
		if ($height) {
			$image .= ' height="' . $height . '"';
		}
		if ($params) {
			$image .= ' ' . $params;
		}
		$image .= '>';

		return $image;
	}

	////
	// Draw a 1 pixel black line
	function tep_black_line() {
		return tep_image(DIR_WS_IMAGES . 'pixel_black.gif', '', '100%', '1');
	}

	////
	// Output a separator either through whitespace, or with an image
	function tep_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') {
		return tep_image(DIR_WS_IMAGES . $image, '', $width, $height);
	}
	////
	// Output a form
	function tep_draw_form($name, $action, $parameters = '', $method = 'post', $params = '') {
		$form = '<form name="' . tep_output_string($name) . '" action="';
		if (tep_not_null($parameters)) {
			$form .= tep_href_link($action, $parameters);
		} else {
			$form .= tep_href_link($action);
		}
		$form .= '" method="' . tep_output_string($method) . '"';
		if (tep_not_null($params)) {
			$form .= ' ' . $params;
		}
		$form .= '>';

		return $form;
	}

	////
	// Output a form input field
	function tep_draw_input_field($name, $value = '', $parameters = '', $required = false, $type = 'text', $reinsert_value = true) {
		$field = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';

		if (isset($GLOBALS[$name]) && ($reinsert_value == true) && is_string($GLOBALS[$name])) {
			$field .= ' value="' . tep_output_string(stripslashes($GLOBALS[$name])) . '"';
		} elseif (tep_not_null($value)) {
			$field .= ' value="' . tep_output_string($value) . '"';
		}

		if (tep_not_null($parameters)) $field .= ' ' . $parameters;

		$field .= '>';

		if ($required == true) $field .= sysLanguage::get('TEXT_FIELD_REQUIRED');

		return $field;
	}

	///
	// Output a form password field
	function tep_draw_password_field($name, $value = '', $required = false) {
		$field = tep_draw_input_field($name, $value, 'maxlength="40"', $required, 'password', false);

		return $field;
	}

	////
	// Output a form filefield
	function tep_draw_file_field($name, $required = false) {
		$field = tep_draw_input_field($name, '', '', $required, 'file');

		return $field;
	}

	function tep_draw_selection_field($name, $type, $value = '', $checked = false, $compare = '', $parameter = '') {
		$selection = '<input type="' . $type . '" name="' . $name . '"';
		if ($value != '') {
			$selection .= ' value="' . $value . '"';
		}
		if ( ($checked == true) || ($value && ($value == $compare)) ) {
			$selection .= ' CHECKED';
		}
		if ($parameter != '') {
			$selection .= ' ' . $parameter;
		}
		$selection .= '>';

		return $selection;
	}

	////
	// Output a form checkbox field
	function tep_draw_checkbox_field($name, $value = '', $checked = false, $compare = '', $parameter = '') {
		return tep_draw_selection_field($name, 'checkbox', $value, $checked, $compare, $parameter);
	}

	////
	// Output a form radio field
	function tep_draw_radio_field($name, $value = '', $checked = false, $compare = '', $parameter = '') {
		return tep_draw_selection_field($name, 'radio', $value, $checked, $compare, $parameter);
	}
	//Admin end
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
	// Output a form textarea field w/ fckeditor
	function tep_draw_fckeditor($name, $width, $height, $text) {
		global $Config;

		//	$Config['UserFilesPath'] = DIR_WS_CATALOG.'/userfiles/';
		$oFCKeditor = new FCKeditor($name);
		$oFCKeditor -> Width  = $width;
		$oFCKeditor -> Height = $height;
		$oFCKeditor -> BasePath	= 'rentalwysiwyg/';
		$oFCKeditor -> Value = $text;

		$field = $oFCKeditor->Create($name);

		return $field;
	}

	////
	// Output a form hidden field
	function tep_draw_hidden_field($name, $value = '', $parameters = '') {
		$field = '<input type="hidden" name="' . tep_output_string($name) . '"';

		if (tep_not_null($value)) {
			$field .= ' value="' . tep_output_string($value) . '"';
		} elseif (isset($GLOBALS[$name]) && is_string($GLOBALS[$name])) {
			$field .= ' value="' . tep_output_string(stripslashes($GLOBALS[$name])) . '"';
		}

		if (tep_not_null($parameters)) $field .= ' ' . $parameters;

		$field .= '>';

		return $field;
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

	//Package Tracking Plus BEGIN
	////
	// Output a form textbox field
	function tep_draw_textbox_field($name, $size, $numchar, $value = '', $params = '', $reinsert_value = true) {
		$field = '<input type="text" name="' . $name . '" size="' . $size . '" maxlength="' . $numchar . '" value="';
		if ($params) $field .= '' . $params;
		$field .= '';
		if (isset($GLOBALS[$name])){
			if ($reinsert_value){
				$field .= $GLOBALS[$name];
			}
		} elseif ($value != '') {
			$field .= trim($value);
		}
		$field .= '">';

		return $field;
	}
	//Package Tracking Plus END


	function itw_template_button($settings){
		$buttonHtml = '<button';

		if (isset($settings['id'])){
			$buttonHtml .= ' id="' . $settings['id'] . '"';
		}

		if (isset($settings['name'])){
			$buttonHtml .= ' name="' . $settings['name'] . '"';
		}

		$buttonHtml .= ' class="ui-button ui-state-default ui-corner-all';
		if (isset($settings['hidden']) && $settings['hidden'] === true){
			$buttonHtml .= ' ui-helper-hidden';
		}
		if (isset($settings['disabled']) && $settings['disabled'] === true){
			$buttonHtml .= ' ui-state-disabled';
		}

		$buttonHtml .= '" type="button"><span>' . $settings['text'] . '</span></button>';
		return $buttonHtml;
	}
	/*
	function itw_template_button($text, $parameters = '', $hidden = false){
	return '<button class="' . ($hidden === true ? 'ui-helper-hidden ' : '') . 'ui-button ui-state-default ui-corner-all" type="button"' . ($parameters != '' ? ' ' . $parameters : '') . '><span>' . $text . '</span></button>';
	}
	*/
	function itw_template_submit_button($settings){
		$buttonHtml = '<button';

		if (isset($settings['id'])){
			$buttonHtml .= ' id="' . $settings['id'] . '"';
		}

		if (isset($settings['name'])){
			$buttonHtml .= ' name="' . $settings['name'] . '"';
		}

		$buttonHtml .= ' class="ui-button ui-state-default ui-corner-all';
		if (isset($settings['hidden']) && $settings['hidden'] === true){
			$buttonHtml .= ' ui-helper-hidden';
		}
		if (isset($settings['disabled']) && $settings['disabled'] === true){
			$buttonHtml .= ' ui-state-disabled';
		}

		$buttonHtml .= '" type="submit"><span>' . $settings['text'] . '</span></button>';
		return $buttonHtml;
	}

	function tep_get_category_tree_list($parent_id = '0', $checked = false, $include_itself = true) {
		$catList = '';
		if (tep_childs_in_category_count($parent_id) > 0){
			if (!is_array($checked)){
				$checked = array();
			}
			$catList = '<ul class="catListingUL">';

			if ($parent_id == '0'){
				$category = Doctrine_Manager::getInstance()
					->getCurrentConnection()
					->fetchAssoc("select cd.categories_name from categories_description cd where cd.language_id = '" . (int)Session::get('languages_id') . "' and cd.categories_id = '" . (int)$parent_id . "'");
				if (sizeof($category) > 0){
					$catList .= '<li>' . tep_draw_checkbox_field('categories[]', $parent_id, (in_array($parent_id, $checked)), 'id="catCheckbox_' . $parent_id . '"') . '<label for="catCheckbox_' . $parent_id . '">' . $category[0]['categories_name'] . '</label></li>';
				}
			}

			$QCategories = Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->fetchAssoc("select c.categories_id, cd.categories_name, c.parent_id from categories c, categories_description cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)Session::get('languages_id') . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
			if (sizeof($QCategories) > 0){
				foreach ($QCategories as $categories) {
					$catList .= '<li>' . tep_draw_checkbox_field('categories[]', $categories['categories_id'], (in_array($categories['categories_id'], $checked)), 'id="catCheckbox_' . $categories['categories_id'] . '"') . '<label for="catCheckbox_' . $categories['categories_id'] . '">' . $categories['categories_name'] . '</label></li>';
					if (tep_childs_in_category_count($categories['categories_id']) > 0){
						$catList .= '<li class="subCatContainer">' . tep_get_category_tree_list($categories['categories_id'], $checked, false) . '</li>';
					}
				}
			}
			$catList .= '</ul>';
		}

		return $catList;
	}
?>