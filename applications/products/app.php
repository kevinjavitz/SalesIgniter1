<?php
	$appContent = $App->getAppContentFile();
	if(sysConfig::get('TOOLTIP_DESCRIPTION_ENABLED') == 'true'){
		$App->addStylesheetFile('ext/jQuery/external/mopTip/mopTip-2.2.css');
		$App->addJavascriptFile('ext/jQuery/external/mopTip/mopTip-2.2.js');
		$App->addJavascriptFile('applications/products/javascript/common.js');
	}

	$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_PRODUCTS'));
	switch($App->getPageName()){
		case 'all':
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_ALL'), itw_app_link(null, 'products', 'all'));
			break;
		case 'best_sellers':
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_BEST_SELLERS'), itw_app_link(null, 'products', 'best_sellers'));
			break;
		case 'upcoming':
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_UPCOMING'), itw_app_link(null, 'products', 'upcoming'));
			break;
		case 'featured':
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_FEATURED'), itw_app_link(null, 'products', 'featured'));
			break;
		case 'new':
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_NEW'), itw_app_link(null, 'products', 'new'));
			break;
		case 'search':
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_SEARCH'), itw_app_link(null, 'products', 'search'));
			break;
		case 'search_result':
			// Search enhancement mod start
			if(isset($_GET['keywords']) && $_GET['keywords'] != ''){
				if(!isset($_GET['s'])){
					$pwstr_check = strtolower(substr($_GET['keywords'], strlen($_GET['keywords'])-1, strlen($_GET['keywords'])));
					if($pwstr_check == 's'){
						$pwstr_replace = substr($_GET['keywords'], 0, strlen($_GET['keywords'])-1);
						header('location: ' . itw_app_link('search_in_keywords=1&plural=1&s=1&keywords=' . urlencode($pwstr_replace), 'products', 'search_result'));
						exit;
					}
				}

				$pw_keywords = explode(' ',stripslashes(strtolower($_GET['keywords'])));
				$pw_replacement_words = $pw_keywords;
				$pw_boldwords = $pw_keywords;
				$sql_words = tep_db_query("SELECT * FROM searchword_swap");
				$pw_replacement = '';
				$pw_link_text = '';
				while ($sql_words_result = tep_db_fetch_array($sql_words)) {
					if(stripslashes(strtolower($_GET['keywords'])) == stripslashes(strtolower($sql_words_result['sws_word']))){
						$pw_replacement = stripslashes($sql_words_result['sws_replacement']);
						$pw_link_text = '<b><i>' . stripslashes($sql_words_result['sws_replacement']) . '</i></b>';
						$pw_phrase = 1;
						$pw_mispell = 1;
						break;
					}
					for($i=0; $i<sizeof($pw_keywords); $i++){
						if($pw_keywords[$i]  == stripslashes(strtolower($sql_words_result['sws_word']))){
							$pw_replacement_words[$i] = stripslashes($sql_words_result['sws_replacement']);
							$pw_boldwords[$i] = '<b><i>' . stripslashes($sql_words_result['sws_replacement']) . '</i></b>';
							$pw_mispell = 1;
							break;
						}
					}
				}
				if(!isset($pw_phrase)){
					for($i=0; $i<sizeof($pw_keywords); $i++){
						$pw_replacement .= $pw_replacement_words[$i] . ' ';
						$pw_link_text   .= $pw_boldwords[$i]. ' ';
					}
				}

				$pw_replacement = trim($pw_replacement);
				$pw_link_text   = trim($pw_link_text);
				$pw_string      = '<br><span class="main"><font color="red">' . sysLanguage::get('TEXT_REPLACEMENT_SUGGESTION') . '</font><a href="' . itw_app_link(tep_get_all_get_params(array('keywords')) . 'keywords=' . urlencode($pw_replacement), 'products', 'search_result') . '">' . $pw_link_text . '</a></span><br><br>';

			}
			// Search enhancement mod end

			$errors = 0;

			$validSearchKeys = array('keywords', 'dfrom', 'dto', 'pfrom', 'pto');
			EventManager::notify('ProductSearchAddValidKeys', &$validSearchKeys);

			foreach($validSearchKeys as $key){
				$validated = false;
				if (isset($_GET[$key]) && !empty($_GET[$key])){
					$error = false;

					switch($key){
						case 'keywords':
							if (empty($_GET[$key])) $errors++;
							break;
						case 'dto':
						case 'dfrom':
							if ($_GET[$key] == DOB_FORMAT_STRING) $errors++;
							break;
						case 'pfrom':
						case 'pto':
							if (is_array($_GET[$key])){
								foreach($_GET[$key] as $k => $v){
									if (!is_numeric($v)) $errors++;
								}
							}else{
								if (!is_numeric($_GET[$key])) $errors++;
							}
							break;
						default:
							EventManager::notify('ProductSearchValidateKey', &$key, &$errors);
							break;
					}
				}
			}

			$error = false;
			if ($errors >= sizeof($validSearchKeys)){
				$error = true;
				$messageStack->add_session('search', sysLanguage::get('ERROR_AT_LEAST_ONE_INPUT'));
			} else {
				$dfrom = '';
				$dto = '';
				$pfrom = '';
				$pto = '';
				$keywords = '';

				if (isset($_GET['dfrom'])) {
					$dfrom = (($_GET['dfrom'] == DOB_FORMAT_STRING) ? '' : $_GET['dfrom']);
				}

				if (isset($_GET['dto'])) {
					$dto = (($_GET['dto'] == DOB_FORMAT_STRING) ? '' : $_GET['dto']);
				}

				if (isset($_GET['pfrom'])) {
					$pfrom = $_GET['pfrom'];
				}

				if (isset($_GET['pto'])) {
					$pto = $_GET['pto'];
				}

				if (isset($_GET['keywords'])) {
					$keywords = $_GET['keywords'];
				}

				$date_check_error = false;
				if (tep_not_null($dfrom)) {
					if (!tep_checkdate($dfrom, DOB_FORMAT_STRING, $dfrom_array)) {
						$error = true;
						$date_check_error = true;

						$messageStack->add_session('search', sysLanguage::get('ERROR_INVALID_FROM_DATE'));
					}
				}

				if (tep_not_null($dto)) {
					if (!tep_checkdate($dto, DOB_FORMAT_STRING, $dto_array)) {
						$error = true;
						$date_check_error = true;

						$messageStack->add_session('search', sysLanguage::get('ERROR_INVALID_TO_DATE'));
					}
				}

				if (($date_check_error == false) && tep_not_null($dfrom) && tep_not_null($dto)) {
					if (mktime(0, 0, 0, $dfrom_array[1], $dfrom_array[2], $dfrom_array[0]) > mktime(0, 0, 0, $dto_array[1], $dto_array[2], $dto_array[0])) {
						$error = true;

						$messageStack->add_session('search', sysLanguage::get('ERROR_TO_DATE_LESS_THAN_FROM_DATE'));
					}
				}

				$price_check_error = false;
				if (tep_not_null($pfrom)) {
					if (is_array($pfrom)){
						foreach($pfrom as $k => $v){
							if (!settype($pfrom[$k], 'double')){
								$error = true;
								$price_check_error = true;

								$messageStack->add_session('search', sysLanguage::get('ERROR_PRICE_FROM_MUST_BE_NUM'));
							}
						}
					}else{
						$price_check_error = false;
						if (!settype($pfrom, 'double')) {
							$error = true;
							$price_check_error = true;

							$messageStack->add_session('search', sysLanguage::get('ERROR_PRICE_FROM_MUST_BE_NUM'));
						}
					}
				}

				if (tep_not_null($pto)) {
					if (is_array($pto)){
						foreach($pto as $k => $v){
							if (!settype($pto[$k], 'double')){
								$error = true;
								$price_check_error = true;

								$messageStack->add_session('search', sysLanguage::get('ERROR_PRICE_TO_MUST_BE_NUM'));
							}
						}
					}else{
						if (!settype($pto, 'double')) {
							$error = true;
							$price_check_error = true;

							$messageStack->add_session('search', sysLanguage::get('ERROR_PRICE_TO_MUST_BE_NUM'));
						}
					}
				}

				if (($price_check_error == false)) {
					if (is_array($pfrom) && is_array($pto)){
						foreach($pfrom as $k => $v){
							if ($v >= $pto[$k]){
								$error = true;
								$price_check_error = true;

								$messageStack->add_session('search', sysLanguage::get('ERROR_PRICE_TO_LESS_THAN_PRICE_FROM'));
							}
						}
					}elseif (is_float($pfrom) && is_float($pto)){
						if ($pfrom >= $pto) {
							$error = true;

							$messageStack->add_session('search', sysLanguage::get('ERROR_PRICE_TO_LESS_THAN_PRICE_FROM'));
						}
					}
				}

				if (tep_not_null($keywords)) {
					if (!tep_parse_search_string($keywords, $search_keywords)) {
						$error = true;

						$messageStack->add_session('search', sysLanguage::get('ERROR_INVALID_KEYWORDS'));
					}
				}
			}

			if ($error == true) {
				tep_redirect(itw_app_link(tep_get_all_get_params(), 'products', 'search', 'NONSSL', true, false));
			}

			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_SEARCH'), itw_app_link(null, 'products', 'search'));
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_SEARCH_RESULT'), itw_app_link(tep_get_all_get_params(), 'products', 'search_result', 'NONSSL', true, false));

			// Search enhancement mod start
			if (isset($_GET['keywords'])){
				$search_enhancements_keywords = $_GET['keywords'];
				$search_enhancements_keywords = strip_tags($search_enhancements_keywords);
				$search_enhancements_keywords = addslashes($search_enhancements_keywords);

				if (Session::exists('last_search_insert') === false || $search_enhancements_keywords != Session::get('last_search_insert')) {
					$Insert = new SearchQueries();
					$Insert->search_text = $search_enhancements_keywords;
					$Insert->save();
					Session::set('last_search_insert', $search_enhancements_keywords);
				}
			}
			// Search enhancement mod end
			break;
	}
?>
