<?php
class InfoBoxPayPerRental extends InfoBoxAbstract {
	
	public function __construct(){
		global $App;
		$this->init('payPerRental', __DIR__);

		$this->enabled = ((sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_SELECTION') != 'Using calendar after browsing products and clicking Reserve') ? true:false);
		if (isset($_GET['app']) && $_GET['app'] == 'checkout'){
			$this->enabled = false;
		}
		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_PAYPERRENTAL'));
		$this->buildStylesheetMultiple = false;
		$this->buildJavascriptMultiple = true;
	}

	public function addPPrChildren($child, $currentPath, &$ulElement){
		global $current_category_id;
		//$currentPath .= '_' . $child['categories_id'];

		$childLinkEl = htmlBase::newElement('a')
				->addClass('ui-widget ui-widget-content ui-corner-all cats')
				->css('border-color', 'transparent')
				->html('<span class="ui-icon ui-icon-triangle-1-e ui-icon-categories-bullet" style="vertical-align:middle;"></span><span style="display:inline-block;vertical-align:middle;">' . $child['CategoriesDescription'][Session::get('languages_id')]['categories_name'] . '</span>')
				->attr('rel', $child['CategoriesDescription'][Session::get('languages_id')]['categories_seo_url']);
		//->setHref(itw_app_link('cPath=' . $currentPath, 'index', 'default'));

		if ($child['categories_id'] == $current_category_id){
			$childLinkEl->addClass('selected');
		}

		$Qchildren = Doctrine_Query::create()
		->select('c.categories_id, cd.categories_name, c.parent_id, c.ppr_show_in_menu')
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('c.parent_id = ?', $child['categories_id'])
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
				if ($childInfo['ppr_show_in_menu'] == 1){
					$this->addPPRChildren($childInfo, $currentPath, &$children);
				}
			}
		}

		$liElement = htmlBase::newElement('li')
		->append($childLinkEl);
		if ($children){
			$liElement->append($children);
		}
		$ulElement->addItemObj($liElement);
	}

	public function getPprForm($hasUpdateButton = true, $hasHeaders = false, $hasGeographic = true, $showCategories = false, $showSubmit = false, $showShipping = false, $showTimes = false, $showQty = false, $showPickup = false, $showDropoff = false){
		global $appExtension, $userAccount, $currencies, $cPath, $cPath_array, $tree, $categoriesString, $current_category_id, $App;

		$getv = '';
		if (isset($_GET['cPath'])){
			$getv = "&cPath=" . $_GET['cPath'];
		}
		$pprform = htmlBase::newElement('form')
		->attr('name', 'selectPPR')
		->attr('class', 'sd')
		->attr('action', itw_app_link('appExt=payPerRentals&action=setBefore' . $getv, 'build_reservation', 'default'))
		->attr('method', 'post');

		$pprform->append(ReservationUtilities::inventoryCenterAddon($hasHeaders, $hasGeographic, $showPickup, $showDropoff));


		$separator2 = htmlBase::newElement('div');
		if ($hasHeaders === true){
			$separator2->addClass('ui-my-header');
		}
		$separatort2 = htmlBase::newElement('div');
		if ($hasHeaders === true){
			$separatort2->addClass('ui-my-header-text');
			$separatort2->html(sysLanguage::get('INFOBOX_PAYPERRENTAL_SELECT_DATES'));
		}
		$separator2->append($separatort2);
		$container_dates = htmlBase::newElement('div');
		if ($hasHeaders === true){
			$container_dates->addClass('ui-my-content');
		}
		$starttime = (int) sysConfig::get('EXTENSION_PAY_PER_RENTALS_START_TIME');
		$endtime = (int) sysConfig::get('EXTENSION_PAY_PER_RENTALS_END_TIME');

		$dst = htmlBase::newElement('span')
		->addClass('start_text')
		->html(sysLanguage::get('TEXT_ENTRY_PICKUP_DATE'));

		$dateStart = htmlBase::newElement('input')
		->addClass('picker dstart')
		->setName('dstart');

		$est = htmlBase::newElement('span')
		->addClass('end_text')
		->html(sysLanguage::get('TEXT_ENTRY_RETURN_DATE'));

		$dateEnd = htmlBase::newElement('input')
		->addClass('picker dend')
		->setName('dend');

		if (Session::exists('isppr_date_start') && (Session::get('isppr_date_start') != '') && Session::get('isppr_selected') == true){
			$dd = explode(' ', Session::get('isppr_date_start'));
			$dateStart->val(date(sysLanguage::getDateFormat(), strtotime($dd[0])));
		}

		if (Session::exists('isppr_date_end') && (Session::get('isppr_date_end') != '') && Session::get('isppr_selected') == true){
			$dd = explode(' ', Session::get('isppr_date_end'));
			$dateEnd->val(date(sysLanguage::getDateFormat(), strtotime($dd[0])));
		}
		$hst = htmlBase::newElement('span')
		->addClass('start_time_text')
		->html(sysLanguage::get('TEXT_ENTRY_PICKUP_TIME'));

		$hourStart = htmlBase::newElement('selectbox')
		->setName('hstart')
		->addClass('pickers hstart')
		->addClass('myf2');

		if (Session::exists('isppr_hour_starts') && (Session::get('isppr_hour_starts') != '') && Session::get('isppr_selected') == true){
			$hourStart->selectOptionByValue(Session::get('isppr_hour_starts'));
		}
		$hen = htmlBase::newElement('span')
		->addClass('end_time_text')
		->html(sysLanguage::get('TEXT_ENTRY_RETURN_TIME'));

		$hourEnd = htmlBase::newElement('selectbox')
		->setName('hend')
		->addClass('pickers hend')
		->addClass('myf2');

		if (Session::exists('isppr_hour_ends') && (Session::get('isppr_hour_ends') != '') && Session::get('isppr_selected') == true){
			$hourEnd->selectOptionByValue(Session::get('isppr_hour_ends'));
		}
		$pageURL = 'http';
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on"){
			$pageURL .= "s";
		}
		$pageURL .= "://";
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		$htmlPageUrl = htmlBase::newElement('input')
		->setType('hidden')
		->setName('url')
		->setValue($pageURL);

		$submitb = htmlBase::newElement('a')
		->addClass('rentbbut')
		->setName('submitb');

		if ($showSubmit){
			if (Session::exists('isppr_date_start') && (Session::get('isppr_date_start') != '') && Session::exists('isppr_date_end') && (Session::get('isppr_date_end') != '')){
				$submitb->html(sysLanguage::get('TEXT_INFOBOX_PAY_PER_RENTAL_BUTTON_UPDATE'));
				Session::set('button_text', sysLanguage::get('TEXT_INFOBOX_PAY_PER_RENTAL_BUTTON_UPDATE'));
				$pprform->append($htmlPageUrl);
			}else{
				$submitb->html(sysLanguage::get('TEXT_BUTTON_SUBMIT'));
				Session::set('button_text', sysLanguage::get('TEXT_BUTTON_SUBMIT'));
			}
		}

		if($showCategories){

			$Qcategories = Doctrine_Query::create()
			->select('c.categories_id, cd.categories_name, c.categories_seo_url, c.parent_id, c.ppr_show_in_menu')
			->from('Categories c')
			->leftJoin('c.CategoriesDescription cd')
			->where('c.parent_id = ?', '0')
			->andWhere('(c.categories_menu = "infobox" or c.categories_menu = "both")')
			->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
			->orderBy('c.sort_order, cd.categories_name');

			EventManager::notify('CategoryQueryBeforeExecute', $Qcategories);

			$Result = $Qcategories->execute(array(), Doctrine::HYDRATE_ARRAY);

			$headerMenuContainer = htmlBase::newElement('div')
			->addClass('categoriesPPRBoxMenu');//at some point these 2 will have to be made as classes

			$headMenuContainer = htmlBase::newElement('div')
			->html(sysLanguage::get('INFOBOX_PAYPERRENTAL_SELECT_CATEGORY'))
			->addClass('ui-widget-header headPPRBoxMenu');

			$headerMenuContainer->append($headMenuContainer);

			if ($Result){
				foreach($Result as $idx => $cInfo){
					$categoryId = $cInfo['categories_id'];
					$catId = $cInfo['CategoriesDescription'][0]['categories_seo_url'];
					$parentId = $cInfo['parent_id'];
					$categoryName = $cInfo['CategoriesDescription'][0]['categories_name'];
					if ($cInfo['ppr_show_in_menu'] == 1){
						$headerEl = htmlBase::newElement('h3');
						if ($current_category_id == $categoryId){
							$headerEl->addClass('currentCategory');
						}
						$headerEl->html($categoryName);

						$Qchildren = Doctrine_Query::create()
						->select('c.categories_id, cd.categories_name, c.parent_id, c.ppr_show_in_menu')
						->from('Categories c')
						->leftJoin('c.CategoriesDescription cd')
						->where('c.parent_id = ?', $categoryId)
						->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
						->orderBy('c.sort_order, cd.categories_name');

						EventManager::notify('CategoryQueryBeforeExecute', &$Qchildren);
						$currentChildren = $Qchildren->execute();

						$flyoutContainer = htmlBase::newElement('div');
						$ulElement = htmlBase::newElement('list');
						if ($currentChildren->count() > 0){
							foreach($currentChildren->toArray() as $child){
								if ($child['ppr_show_in_menu'] == 1){
									$this->addPPRChildren($child, $categoryId, &$ulElement);
								}
							}
						}else{
							$childLinkEl = htmlBase::newElement('a')
									->addClass('ui-widget ui-widget-content ui-corner-all cats')
									->css('border-color', 'transparent')
									->html('<span class="ui-icon ui-icon-triangle-1-e ui-icon-categories-bullet" style="vertical-align:middle;"></span><span class="ui-categories-text" style="vertical-align:middle;">'.sysLanguage::get('INFOBOX_CATEGORIES_VIEW_PRODUCTS').'</span>')
									->attr('rel', $catId);

							$liElement = htmlBase::newElement('li')
									->append($childLinkEl);
							$ulElement->addItemObj($liElement);
						}
						$flyoutContainer->append($ulElement);

						$headerMenuContainer->append($headerEl)->append($flyoutContainer);
					}
				}
			}
		}

		$starttime = explode(":", $starttime);
		$i = $starttime[0];
		if (isset($starttime[1])){
			$min = $starttime[1];
		}else{
			$min = 0;
		}
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_TIME_INCREMENT') == '1/2'){
			$time_increment = 30;
		}else{
			$time_increment = 60;
		}

		$endtime = explode(":", $endtime);

		$et = $endtime[0];

		if (isset($endtime[1])){
			$etm = $endtime[1];
		}else{
			$etm = 0;
		}
		if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_FILTER_TIME_INFOBOX') == 'True'){
			$timezone = str_replace('GMT ','',sysConfig::get('EXTENSION_PAY_PER_RENTALS_GMT'));
			$offset=(int)$timezone*60*60;
			$curHour = (int)gmdate('G', time()+$offset);
			$min1 = 0;
			Session::set('isppr_curDate', gmdate('m/d/Y',time() + $offset));
			Session::set('isppr_nextDay','0');
			if ($time_increment < (int) date('i')) {
				if($curHour + 1 < 24){
					$curHour += 1;
					$min1 = 0;
				}else{
					Session::set('isppr_nextDay','1');
				}
			} else {
				if($time_increment == 60){
					if($curHour + 1 < 24){
						$curHour += 1;
						$min1 = 0;
					}else{
						Session::set('isppr_nextDay','1');
					}
				}else{
					//Session::set('isppr_curMin','30');
					$min1 = 30;
				}
			}

			if($curHour > $et){
				Session::set('isppr_nextDay','1');
			}

			$endtime1 = mktime($et, $etm, 0, date("m"), date("d"), date("Y"));
			$next_date1 = mktime($curHour, $min, 0, date("m"), date("d"), date("Y"));
			$j = $curHour;
			$issafe = true;
			$hourCurDays = '';
			$hourCurDaye = '';
			while($issafe){

				if ($next_date1 >= $endtime1)	break;

				$mt1 = date("g:i A", $next_date1);
				if (Session::exists('isppr_hour_starts') && (Session::get('isppr_hour_starts') == $j) && Session::get('isppr_selected') == true){
					$hourCurDays .= '<option selected="selected" value="'.$j.'">'.$mt1.'</option>';
				}else{
					$hourCurDays .= '<option value="'.$j.'">'.$mt1.'</option>';
				}
				if (Session::exists('isppr_hour_ends') && (Session::get('isppr_hour_ends') == $j) && Session::get('isppr_selected') == true){
					$hourCurDaye .= '<option selected="selected" value="'.$j.'">'.$mt1.'</option>';
				}else{
					$hourCurDaye .= '<option value="'.$j.'">'.$mt1.'</option>';
				}
				//$hourStart->addOption($j, $mt);
				//$hourEnd->addOption($j, $mt);
				$j++;
				$min1 = $min1 + $time_increment;
				$next_date1 = mktime($curHour, $min1, 0, date("m"), date("d"), date("Y"));
			}
			Session::set('isppr_selectOptionscurdays', $hourCurDays);
			Session::set('isppr_selectOptionscurdaye', $hourCurDaye);
		}

		$endtime = mktime($et, $etm, 0, date("m"), date("d"), date("Y"));
		$next_date = mktime($i, $min, 0, date("m"), date("d"), date("Y"));

		$j = $i;
		$issafe = true;
		$hourCurDays = '';
		$hourCurDaye = '';
		while($issafe){

			if ($next_date >= $endtime)	break;

			$mt = date("g:i A", $next_date);
			if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_FILTER_TIME_INFOBOX') == 'True'){
				if(strtotime($dateStart->val()) == strtotime(Session::get('isppr_curDate')) && $j >= $curHour || (strtotime($dateStart->val()) > strtotime(Session::get('isppr_curDate'))) || $dateStart->val() == ''){
					$hourStart->addOption($j, $mt);
				}
				if(strtotime($dateEnd->val()) == strtotime(date('m/d/Y', strtotime($timezone.' hours'))) && $j >= $curHour || (strtotime($dateEnd->val()) > strtotime(date('m/d/Y', strtotime($timezone.' hours')))) || $dateEnd->val() == ''){
					$hourEnd->addOption($j, $mt);
				}
			}else{
				$hourStart->addOption($j, $mt);
				$hourEnd->addOption($j, $mt);
			}
			if (Session::exists('isppr_hour_starts') && (Session::get('isppr_hour_starts') == $j) && Session::get('isppr_selected') == true){
				$hourCurDays .= '<option selected="selected" value="'.$j.'">'.$mt.'</option>';
			}else{
				$hourCurDays .= '<option value="'.$j.'">'.$mt.'</option>';
			}
			if (Session::exists('isppr_hour_ends') && (Session::get('isppr_hour_ends') == $j) && Session::get('isppr_selected') == true){
				$hourCurDaye .= '<option selected="selected" value="'.$j.'">'.$mt.'</option>';
			}else{
				$hourCurDaye .= '<option value="'.$j.'">'.$mt.'</option>';
			}
			$j++;
			$min = $min + $time_increment;
			$next_date = mktime($i, $min, 0, date("m"), date("d"), date("Y"));
		}

		Session::set('isppr_selectOptionsnormaldays', $hourCurDays);
		Session::set('isppr_selectOptionsnormaldaye', $hourCurDaye);

		if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_FILTER_TIME_INFOBOX') == 'False'){
			Session::set('isppr_curDate', date('m/d/Y'));
			Session::set('isppr_selectOptionscurdays', $hourCurDays);
			Session::set('isppr_selectOptionscurdaye', $hourCurDaye);
		}
		$shipt = htmlBase::newElement('p')
		->html(sysLanguage::get('INFOBOX_PAYPERRENTAL_SELECT_LEVEL_OF_SERVICE').'<a style="float:right;margin-right:10px;"href="' . itw_app_link('appExt=infoPages', 'show_page', 'help_level_service') . '" onclick="popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'help_level_service', 'SSL') . '\',\'400\',\'300\');return false;"><span class="helpicon"></span></a><br style="clear:both;"/>')
		->addClass('shipp');

		$br = htmlBase::newElement('br');

		$shipUps = htmlBase::newElement('input')
		->setName('zipCode')
		->addClass('zipf');



		$shipUpsBut = htmlBase::newElement('button')
		->setName('changeZip')
		->setText('Get Quote')
		->addClass('changeZip');
		$shipUpsMethods = htmlBase::newElement('selectbox')
		->setName('ship_method')
		->addClass('shipf shipz shipups');



		$shipb = htmlBase::newElement('selectbox')
		->setName('ship_method')
		->addClass('shipf shipz');

		$firstShippingMethod = 0;
		if (Session::exists('isppr_shipping_method') && (Session::get('isppr_shipping_method') != '') && Session::get('isppr_selected') == true){
			$shipb->selectOptionByValue(Session::get('isppr_shipping_method'));
			$firstShippingMethod = Session::get('isppr_shipping_method');
		}

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'False'){
			$Module = OrderShippingModules::getModule('zonereservation');
		} else{
			if(Session::exists('isppr_shipping_zip') && Session::get('isppr_shipping_zip') != ''){
				$postcode = Session::get('isppr_shipping_zip');
				$shipUps->setValue(Session::get('isppr_shipping_zip'));
				/*$shippingAddressArray = array(
					'entry_street_address' => (isset($_POST['street_address']) && !empty($_POST['street_address']))
						? $_POST['street_address'] : '',
					'entry_postcode' => $postcode,
					'entry_city' => (isset($_POST['city']) && !empty($_POST['city'])) ? $_POST['city'] : '',
					'entry_state' => (isset($_POST['state']) && ($_POST['state'] != 'undefined')) ? $_POST['state']
						: '',
					'entry_country_id' => (isset($_POST['country']) && !empty($_POST['country'])) ? $_POST['country']
						: sysConfig::get('STORE_COUNTRY'),
					'entry_zone_id' => (isset($_POST['state']) && ($_POST['state'] != 'undefined')) ? $_POST['state']
						: ''
				);
				$addressBook =& $userAccount->plugins['addressBook'];
				$addressBook->addAddressEntry('delivery', $shippingAddressArray);*/
			}
			$Module = OrderShippingModules::getModule('upsreservation');
		}
		$quotes = $Module->quote();
		$min_days = 1000;
		for($i = 0, $n = sizeof($quotes['methods']); $i < $n; $i++){
			if ((int) $quotes['methods'][$i]['days_before'] < $min_days){
				$min_days = (int) $quotes['methods'][$i]['days_before'];
			}
		}

		$eventt = htmlBase::newElement('p')
		->html(sysLanguage::get('INFOBOX_PAYPERRENTAL_SELECT_EVENT'))
		->addClass('eventp');

		$gatet = htmlBase::newElement('p')
		->html(sysLanguage::get('INFOBOX_PAYPERRENTAL_SELECT_GATE'))
		->addClass('gatep');

		$br = htmlBase::newElement('br');
		$eventb = htmlBase::newElement('selectbox')
		->setName('event')
		->addClass('eventf');
		$eventb->addOption('0', sysLanguage::get('INFOBOX_SELECT_EVENT'));

		$firstEvent = 0;
		if (Session::exists('isppr_event') && tep_not_null(Session::get('isppr_event')) && Session::get('isppr_selected') == true){
			$eventb->selectOptionByValue(Session::get('isppr_event'));
			$firstEvent = Session::get('isppr_event');
		}


		$gateb = htmlBase::newElement('selectbox')
			->setName('gate')
			->addClass('gatef');
		$gateb->addOption('0', sysLanguage::get('INFOBOX_SELECT_GATE'));

		$firstGate = 0;
		if (Session::exists('isppr_gate') && tep_not_null(Session::get('isppr_gate')) && Session::get('isppr_selected') == true){
			$gateb->selectOptionByValue(Session::get('isppr_gate'));
			$firstGate = Session::get('isppr_gate');
		}

		$shipb->addOption('0',sysLanguage::get('INFOBOX_SELECT_LEVEL_OF_SERVICE'));

		$min_date =  date("Y-m-d h:i:s", mktime(date("h"),date("i"),date("s"),date("m"),date("d")/*+$min_days*/,date("Y")));
		$Qevent = Doctrine_Query::create()
		->from('PayPerRentalEvents');
		//if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'False'){
			$Qevent = $Qevent->where('DATE_ADD(events_date,INTERVAL events_days DAY) > ?', $min_date);
		//}
		$Qevent = $Qevent->orderBy('events_date')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$gatesArr = array();
		if($Qevent){
			foreach($Qevent as $eInfo){
				if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'False'){
					$shippingArrA = explode(',', $eInfo['shipping']);
					$start_dateA = strtotime($eInfo['events_date']);
					$starting_dateA = date("Y-m-d h:i:s", mktime(date("h",$start_dateA),date("i",$start_dateA), date("s",$start_dateA), date("m",$start_dateA), date("d",$start_dateA), date("Y",$start_dateA)));
					for($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++){
						$days = $quotes['methods'][$i]['days_before'];
						$next_day = mktime(date("h"),date("i"),date("s"),date("m"),date("d")+$days,date("Y"));
						if(/*$next_day < strtotime($starting_dateA) &&*/ in_array($quotes['methods'][$i]['id'], $shippingArrA)){
							if($firstEvent == $eInfo['events_id'] || $firstEvent == 0){
								$firstEvent = $eInfo['events_id'];
								$shippingArr = explode(',', $eInfo['shipping']);

								$start_date = strtotime($eInfo['events_date']);
								$starting_date = date("Y-m-d h:i:s", mktime(date("h",$start_date),date("i",$start_date), date("s",$start_date), date("m",$start_date), date("d",$start_date), date("Y",$start_date)));
							}
							$eventb->addOption($eInfo['events_id'],$eInfo['events_name']);
							break;
						}
					}
				}else{
					if($firstEvent == $eInfo['events_id'] || $firstEvent == 0){
						$gatesArr = explode(',', $eInfo['gates']);
					}

					$myDate = date('M d',strtotime($eInfo['events_date'])).'-'.date('M d Y', strtotime('+'.$eInfo['events_days'].' DAY', strtotime($eInfo['events_date'])));

					$eventb->addOption($eInfo['events_id'],$eInfo['events_name'].'_'.$myDate);
				}
			}
		}
		if(!isset($starting_date)){
			$starting_date =  date("Y-m-d h:i:s");
		}
		for($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++){
			$days = $quotes['methods'][$i]['days_before'];
			$next_day = mktime(date("h"),date("i"),date("s"),date("m"),date("d")+$days,date("Y"));
			if($next_day < strtotime($starting_date) && in_array($quotes['methods'][$i]['id'], $shippingArr)){
				if($firstShippingMethod == 0){
					$firstShippingMethod = $quotes['methods'][$i]['id'];
				}
				$shipb->addOption($quotes['methods'][$i]['id'], $quotes['methods'][$i]['title'] . ' (' . $currencies->format($quotes['methods'][$i]['cost']) . ')');
			}
		}

		$QGate = Doctrine_Query::create()
		->from('PayPerRentalGates')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		foreach($QGate as $iGate){
			if(in_array($iGate['gates_id'], $gatesArr)){
				$gateb->addOption($iGate['gates_id'], $iGate['gate_name']);
			}
		}

		$separator1ev = htmlBase::newElement('div');
		$separatortev = htmlBase::newElement('div');
		$container_destev = htmlBase::newElement('div');
		$container_destgate = htmlBase::newElement('div');
		$separator1ev->append($separatortev);
		$evText = htmlBase::newElement('a')
		->text('More Info')
		->addClass('myev1')
		->attr('href',(($firstEvent != 0)?itw_app_link('appExt=payPerRentals&ev_id='.$firstEvent,'show_event','default'):itw_app_link('appExt=payPerRentals','show_event','list')));

		$ev2Text = htmlBase::newElement('a')
		->text('Click for info')
		->addClass('myev2')
		->attr('href',(($firstEvent != 0)?itw_app_link('appExt=payPerRentals&dialog=true&ev_id='.$firstEvent,'show_event','default'):itw_app_link('appExt=infoPages','show_page','event_info')));

		$gateText = htmlBase::newElement('a')
		->text('Click for info')
		->addClass('myga1')
		->attr('href',(($firstEvent != 0)?itw_app_link('appExt=payPerRentals&isgate=1&dialog=true&ev_id='.$firstEvent,'show_event','default'):itw_app_link('appExt=infoPages','show_page','gate_info')));

		$separator1sh = htmlBase::newElement('div');
		$separatortsh = htmlBase::newElement('div');
		$container_destsh = htmlBase::newElement('div');
		$separator1sh->append($separatortsh);

		$shText = htmlBase::newElement('a')
		->text('More Info')
		->addClass('mysh1')
		->attr('href',itw_app_link('appExt=payPerRentals&sh_id='.$firstShippingMethod,'show_shipping','default'));

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
			$container_destev->append($eventt)
			->append($eventb)
			->append($evText)
			->append($ev2Text);
			$pprform->append($separator1ev)
			->append($container_destev);
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
				$container_destgate->append($gatet)
				->append($gateb)
				->append($gateText);
				$pprform->append($separator1ev)
				->append($container_destgate);
			}
		}else{
			$container_destshB = htmlBase::newElement('div')
			->addClass('destB');
			$container_destshBD = htmlBase::newElement('div')
			->addClass('destBD');
			$container_destshBD->append($dst)
			->append($dateStart);
			$container_destshBT = htmlBase::newElement('div')
				->addClass('destBT');
			if ($showTimes){
				$container_destshBT->append($hst);
				$container_destshBT->append($hourStart);
			}
			$brClear = htmlBase::newElement('br')
			->addClass('brClear');

			$container_destshB->append($container_destshBD)
			->append($container_destshBT)
			->append($brClear);
			$container_dates->append($container_destshB);
			$container_rethB = htmlBase::newElement('div')
			->addClass('retB');
			$container_rethBD = htmlBase::newElement('div')
			->addClass('retBD');
			$brClear1 = htmlBase::newElement('br')
			->addClass('brClear1');

			$container_rethBD->append($est)
			->append($dateEnd);

			$container_rethBT = htmlBase::newElement('div')
			->addClass('retBT');

			if ($showTimes){
				$container_rethBT->append($hen);
				$container_rethBT->append($hourEnd);
			}

			$container_rethB->append($container_rethBD)
			->append($container_rethBT)
			->append($brClear1);

			$container_dates->append($container_rethB);

		}

		$container_qty = htmlBase::newElement('div')
		->addClass('qtyContainer');

		$qtyText = htmlBase::newElement('span')
		->addClass('qty_text')
		->html(sysLanguage::get('TEXT_QTY'));

		$qtyInput = htmlBase::newElement('input')
		->addClass('qtypicker')
		->setName('qty')
		->attr('size','3');

		if(!$showQty){
			$qtyInput->css(array(
				'display'   => 'none'
			));
			$qtyText->css(array(
				'display'   => 'none'
			));
		}

		if(Session::exists('isppr_product_qty')){
			$qtyInput->setValue(Session::get('isppr_product_qty'));
		}  else{
			$qtyInput->setValue('1');
		}

		$container_qty->append($qtyText)->append($qtyInput);
		$container_dates->append($container_qty);

		$pprform->append($separator2)
		->append($container_dates);

		//here is for the level of service or reservation shipping methods
		if ($showShipping){
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'False'){
				$container_destsh->append($shipt)->append($shipb)->append($shText);
			}else{
				$container_destsh->append($shipt)->append($shipUps)->append($shipUpsMethods)->append($shText)->append($shipUpsBut);
			}
			$pprform->append($separator1sh)->append($container_destsh);
		}
		if ($showSubmit){
			$pprform->append($submitb);
		}else{
			//if (Session::exists('isppr_selected') && Session::get('isppr_selected') == true && (isset($_GET['cPath']) || strpos($App->getAppLocation('relative'), 'categoriesPages') > 0) && $hasButton){
			if($hasUpdateButton && (Session::exists('isppr_date_start') && (Session::get('isppr_date_start') != '') && Session::exists('isppr_date_end') && (Session::get('isppr_date_end') != ''))){
				$submitb->html(sysLanguage::get('TEXT_INFOBOX_PAY_PER_RENTAL_BUTTON_UPDATE'));
				Session::set('button_text', sysLanguage::get('TEXT_INFOBOX_PAY_PER_RENTAL_BUTTON_UPDATE'));
				$pprform->append($submitb);
			}

			if($showCategories){
					$pprform->append($headerMenuContainer);
			}

			$lngViewAll = sysLanguage::get('INFOBOX_CATEGORIES_ALL_PRODUCTS');

			$viewAllHtml = htmlBase::newElement('span')
			->html('<div style="text-align:center;font-size:.8em;font-weight:bold;margin:.5em;" ><a class="catsh" rel="-1" href="' . itw_app_link(null, 'products', 'all', 'NONSSL') . '">' . $lngViewAll . '</a></div>');
				 //if ($hasHeaders == false && $hasButton == false){
			$pprform->append($viewAllHtml);
		 //}
		}
		return $pprform->draw();
	}
	
	
	public function show(){
		$WidgetProperties = $this->getWidgetProperties();
		if ($this->enabled === false) return;
		$extraContent = sysConfig::get('EXTENSION_PAY_PER_RENTALS_INFOBOX_CONTENT');
		$this->setBoxContent('<div id="'.$WidgetProperties->boxID.'">'.$this->getPprForm($WidgetProperties->hasButton, $WidgetProperties->hasHeader, $WidgetProperties->hasGeographic, $WidgetProperties->showCategories, $WidgetProperties->showSubmit, $WidgetProperties->showShipping, $WidgetProperties->showTimes, $WidgetProperties->showQty, $WidgetProperties->showPickup, $WidgetProperties->showDropoff). $extraContent. '</div>');

		return $this->draw();
	}

	/*
	.helpicon{
		width:16px;
		height:16px;
		background-image:url(help-icon.png);
		display:block;
	}
	.icon2{
		width:16px;
		height:16px;
		background-image:url(icon_2.png);
		margin-right:4px;
		display:block;
		float:left;
	}
	.icon3{
		width:16px;
		height:16px;
		background-image:url(icon_3.png);
		margin-right:4px;
		display:block;
		float:left;
	}
	.icon1{
		width:16px;
		height:16px;
		background-image:url(icon_1.png);
		margin-right:4px;
		display:block;
		float:left;
	}
	 */

	public function buildStylesheet(){
		ob_start();
			?>

	.myf1{
		margin-top:10px;
		margin-bottom:10px;
		font-weight:bold;
	}
	.myf2{
		margin-top:10px;
		margin-bottom:10px;
	}
	.myg1{
		margin-top:10px;
		margin-bottom:10px;
		font-weight:bold;
	}
	.picker{
	width:95px;
		margin-top:10px;
		margin-right:10px;
	}
	.pickers{
	width:90px;

	}
	.pickp{
		margin-bottom:0;
	}
	.myev1{
		font-size:10px;
	}
	.mysh1{
		font-size:10px;
	}
	.eventf{
		width:170px;
		display:block;
		margin-top:7px;
		position:relative;
	}
	.shipf{
		width:170px;
		display:block;
		margin-top:5px;
		position:relative;
	}
	select.expand {
		width: auto;
	}

	.eventp{
		font-weight:bold;
		margin-bottom:2px;
	}

	.shipp{
		font-weight:bold;
		margin-bottom:2px;
	}
	<?php

	$css = '/* PPR Infobox --BEGIN-- */' . "\n" .
		ob_get_contents();
		ob_end_clean();
	$css .= '' . "\n" .
		'.categoriesPPRBoxMenu.ui-infobox { ' .
			'padding:0;' .
			'background: transparent;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-infobox-header { ' .
			'margin:0;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-infobox-content { ' .
			'padding:0;' .
			'margin:0;' .
			'border-top:none;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-widget-content { ' .
			'padding:0;' .
			'background: #eeeeee;' .
			'font-size:.9em;' .
			'font-family:Arial;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-widget-content .ui-widget-content { ' .
			'font-size:1em;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-accordion-header { ' .
			'color:#ffffff;' .
			'font-weight:bold;' .
			'margin:0;' .
			'padding: .5em;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-accordion-header.ui-state-hover { ' .
			'background-color: #d70e0e;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-accordion-header.ui-state-active { ' .
			'border-color: transparent;' .
			'background-color: #ae0303;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-accordion-header .ui-icon { ' .
			'right: .5em;' .
			'background-image: url(/ext/jQuery/themes/icons/ui-icons_ffffff_256x240.png);' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-accordion-header.ui-corner-all { ' .
			'border-top: none;' .
			'border-left: none;' .
			'border-right: none;' .
			'border-color: #ffffff;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-accordion-content { ' .
			'padding: 0;' .
			'margin: 0;' .
			'border:none;' .
			'background: transparent;' .
			'overflow:visible;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-accordion-content ul { ' .
			'list-style: none;' .
			'padding: 0;' .
			'margin: 0;' .
			'margin: .1em;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-accordion-content li { ' .
			'font-size: 1em;' .
			'padding: .1em 0;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-accordion-content li ul { ' .
			'width: 150px;' .
			'padding: .2em;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-accordion-content li a { ' .
			'text-decoration: none;' .
			'display:block;' .
			'padding: .1em;' .
			'margin-left: auto;' .
			'margin-right: auto;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-accordion-content li .ui-icon { ' .
			'margin-right: .3em;' .
		' }' . "\n" .
		'.categoriesPPRBoxMenu .ui-accordion-content li a:hover, ' .
		'.categoriesPPRBoxMenu .ui-accordion .ui-accordion-content li a.selected { ' .
			'background: #e6e6e6;' .
		' }' . "\n" .
		'.headPPRBoxMenu { ' .
			'height:30px;' .
			'padding-left:10px;' .
			'line-height:25px;' .
			'color:#ffffff;' .
		' }' . "\n" .
		'' . "\n" .
		'/* PPR Infobox --END-- */' . "\n";
	return $css;
	}

	public function buildJavascript() {
			$WidgetProperties = $this->getWidgetProperties();

			ob_start();
			?>
			<?php
		$datePadding = sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_PADDING');
		if(Session::exists('isppr_nextDay') && Session::get('isppr_nextDay') == '1'){
			$datePadding += 1;
		}
	?>
	function nobeforeDays(date){
		today = new Date();
		if(today.getTime() <= date.getTime() - (1000 * 60 * 60 * 24 * <?php echo $datePadding;?> - (24 - date.getHours()) * 1000 * 60 * 60)){
			return [true,''];
		}else{
			return [false,''];
		}
	}

	function makeDatePicker(pickerID){
	var minRentalDays = <?php
        if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GLOBAL_MIN_RENTAL_DAYS') == 'True'){
			echo (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_MIN_RENTAL_DAYS');
			$minDays = (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_MIN_RENTAL_DAYS');
		}else{
			$minDays = 0;
			echo '0';
		}
		if(Session::exists('button_text')){
			$butText = Session::get('button_text');
		}else{
			$butText = 'Update';
		}
		?>;
		var selectedDateId = null;
		var startSelectedDate;

		var dates = $(pickerID+' .dstart,'+pickerID+' .dend').datepicker({
		dateFormat: '<?php echo getJsDateFormat(); ?>',
		changeMonth: true,
		beforeShowDay: nobeforeDays,
		onSelect: function(selectedDate) {

		var option = this.id == "dstart" ? "minDate" : "maxDate";
		if($(this).hasClass('dstart')){
		myid = "dstart";
		option = "minDate";
		}else{
		myid = "dend";
		option = "maxDate";
		}
		var instance = $(this).data("datepicker");
		var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);

		var dateC = new Date('<?php echo Session::get('isppr_curDate');?>');
		if(date.getTime() == dateC.getTime()){
		if(myid == "dstart"){
		$(this).closest('form').find('.hstart').html('<?php echo Session::get('isppr_selectOptionscurdays');?>');
		}else{
		$(this).closest('form').find('.hend').html('<?php echo Session::get('isppr_selectOptionscurdaye');?>');
		}
		}else{
		if(myid == "dstart"){
		$(this).closest('form').find('.hstart').html('<?php echo Session::get('isppr_selectOptionsnormaldays');?>');
		}else{
		$(this).closest('form').find('.hend').html('<?php echo Session::get('isppr_selectOptionsnormaldaye');?>');
		}
		}


		if(myid == "dstart"){
		var days = "0";
		if ($(this).closest('form').find('select.pickupz option:selected').attr('days')){
		days = $(this).closest('form').find('select.pickupz option:selected').attr('days');
		}
		//startSelectedDate = new Date(selectedDate);
		dateFut = new Date(date.setDate(date.getDate() + parseInt(days)));
		dates.not(this).datepicker("option", option, dateFut);
		}
		f = true;
		if(myid == "dend"){
		datest = new Date(selectedDate);
		if ($(this).closest('form').find('.dstart').val() != ''){
		startSelectedDate = new Date($(this).closest('form').find('.dstart').val());
		if (datest.getTime() - startSelectedDate.getTime() < minRentalDays *24*60*60*1000){
		alert('<?php echo sprintf(sysLanguage::get('EXTENSION_PAY_PER_RENTALS_ERROR_MIN_DAYS'), $minDays);?>');
		$(this).val('');
		f = false;
		}
		}else{
		f = false;
		}
		}

		if (selectedDateId != this.id && selectedDateId != null && f){
		selectedDateId = null;
		}
		if (f){
		selectedDateId = this.id;
		}

		}
		});
	}

	$(document).ready(function (){

     var butText = '<?php echo $butText;?>';
     $('<?php echo '#'.$WidgetProperties->boxID;?> .rentbbut').text(butText);
    if ($.browser.msie) $('.eventf')
        .bind('focus mouseover', function() { $(this).addClass('expand').removeClass('clicked'); })
        .bind('click', function() { $(this).toggleClass('clicked'); })
        .bind('mouseout', function() { if (!$(this).hasClass('clicked')) { $(this).removeClass('expand'); }})
        .bind('blur', function() { $(this).removeClass('expand clicked'); });

	$('<?php echo '#'.$WidgetProperties->boxID;?> .categoriesPPRBoxMenu').accordion({
		header: 'h3',
		collapsible: true,
		autoHeight: false,
		active: $('.currentCategory', $(this)),
		icons: {
			header: 'ui-icon-circle-triangle-s',
			headerSelected: 'ui-icon-circle-triangle-n'
		}
	});

	$('a', $('<?php echo '#'.$WidgetProperties->boxID;?> .categoriesPPRBoxMenu')).each(function (){
		var $link = $(this);
		$($link.parent()).hover(function (){
			$link.css('cursor', 'pointer').addClass('ui-state-hover');

			var linkOffset = $link.parent().offset();
			var boxOffset = $(this).offset();
			if ($('ul', $(this)).size() > 0){
				var $menuList = $('ul:first', $(this));
				$menuList.css({
					position: 'absolute',
					top: $link.parent().position().top,
					left: $link.parent().position().left + $link.parent().innerWidth() - 5,
					backgroundColor: '#FFFFFF',
					zIndex: 9999
				}).show();
			}
		}, function (){
			$link.css({cursor: 'default'}).removeClass('ui-state-hover');

			if ($('ul', this).size() > 0){
				$('ul:first', this).hide();
			}
		}).click(function (){
			document.location = $('a:first', this).attr('href');
		});
	});

	$('<?php echo '#'.$WidgetProperties->boxID;?> .cats').click(function(){
		var inp = "<input type='hidden' name='cPath' value='"+$(this).attr('rel')+"'>";
		$(this).parents('form[name$="selectPPR"]').append(inp);
		$(this).parents('form[name$="selectPPR"]').submit();
		return false;
	});


	$('button[name="no_dates_selected"]').each(function(){$(this).click(function(){
	    $( '<div id="dialog-mesage" title="Choose Dates"><input class="tField" name="tField" ><div class="destBD"><span class="start_text">Start: </span><input class="picker dstart" name="dstart" ></div><div class="destBD"><span class="end_text">End: </span><input class="picker dend" name="dend" ></div><?php echo sysConfig::get('EXTENSION_PAY_PER_RENTALS_INFOBOX_CONTENT');?></div>' ).dialog({
			modal: false,
			autoOpen: true,
			open: function (e, ui){
				makeDatePicker('#dialog-mesage');
				$(this).find('.tField').hide();
			},
			buttons: {
				Submit: function() {

		            $('<?php echo '#'.$WidgetProperties->boxID;?> .dstart').val($(this).find('.dstart').val());
					$('<?php echo '#'.$WidgetProperties->boxID;?> .dend').val($(this).find('.dend').val());
					$('<?php echo '#'.$WidgetProperties->boxID;?> .rentbbut').trigger('click');
					$('form[name$="selectPPR"]').submit();

					$(this).dialog( "close" );
				}
			}
		});

		return false;
	})});
	$('button[name="no_inventory"]').each(function(){$(this).click(function(){

		$( '<div id="dialog-mesage" title="No Inventory"><span style="color:red;font-size:18px;"><?php echo sysLanguage::get('EXTENSION_PAY_PER_RENTALS_ERROR_NO_INVENTORY_FOR_SELECTED_DATES');?></span></div>' ).dialog({
			modal: true,
			buttons: {
				Ok: function() {
					$( this ).dialog( "close" );
				}
			}
		});

		return false;
	})});
	$('button[name="double_dates_selected"]').each(function(){$(this).click(function(){alert('<?php echo sysLanguage::get('TEXT_CHOOSE_INVENTORY');?>');return false;})});
		makeDatePicker('<?php echo '#'.$WidgetProperties->boxID;?>');
		$('<?php echo '#'.$WidgetProperties->boxID;?> .myev2').attr('href',"#");
		$('<?php echo '#'.$WidgetProperties->boxID;?> .myev2').click(function(){
								if($('.eventf').val() != '0'){
									link = js_app_link('appExt=payPerRentals&app=show_event&appPage=default&dialog=true&ev_id='+$('.eventf').val());
								}else{
									link = js_app_link('appExt=infoPages&app=show_page&appPage=event_info&dialog=true');
								}
								popupWindow(link,'400','300');
								return false;
		});
        $('.eventf').change(function(){
						if($(this).val() != '0'){
							link = js_app_link('appExt=payPerRentals&app=show_event&appPage=default&ev_id='+$(this).val());
						}else{
							link = js_app_link('appExt=payPerRentals&app=show_event&appPage=list');
						}
						$('.myev1').attr('href',link);
						myel = $(this).parent();
						showAjaxLoader(myel,'xlarge');
						var self = $(this);
                        $.ajax({
							 type: "post",
							 url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=setBefore'),
							 data: "rType=ajax&event="+self.val()+'&ship_method='+$(this).closest('form').find('.shipz').val(),
							 success: function(data) {
		                        hideAjaxLoader(myel);
								if(typeof data.calendar != ''){
									$('.myCalendar').remove();
									$('.myTextCalendar').remove();
									$('.calDone').remove();
									$('.closeCal').remove();

									self.parent().append(data.calendar);
									$('.calDone').css('color','#ffffff');
									$('.calDone').css('cursor','pointer');
									$('.calDone').click(function(){
										if($('.myCalendar').is(':visible')){
											$('.myCalendar').hide();
											$(this).html('Choose Dates');
											$(this).css('position','relative');
											$(this).css('top', '-14px');
											$(this).css('z-index', '10005');
											$(this).css('left', '110px');
											$(this).css('color', '#ffffff');
											$('.myTextCalendar').hide();
											$('.allCalendar').hide();
											$('.closeCal').hide();
											if(myInitialDates != $('.mydates').html() && myInitialDates != null){
												$( '<div id="dialog-mesage1" title="Date Selection Changed"><span style="color:red;font-size:16px;">Date selection changed. Do you want us to update this page with your new dates?</span></div>' ).dialog({
													modal: true,
													buttons: {
														No: function() {
															$( this ).dialog( "close" );
														},
														Yes: function() {
															$('.rentbbut').trigger('click');
														}
													}
												});
											}
										}else{
											$('.myTextCalendar').show();
											$('.myCalendar').show();
											$(this).css('position','relative');
											$(this).css('top', '290px');
											$(this).css('color', '#000000');
											$(this).css('z-index', '10005');
											$(this).css('left', '30px');
											$('.closeCal').css('position','relative');
											$('.closeCal').css('top', '-30px');
											$('.closeCal').css('z-index', '10005');
											$('.closeCal').css('left', '230px');
											$('.closeCal').css('cursor', 'pointer');
											$('.closeCal').css('width', '15px');
											$('.allCalendar').show();
											$('.closeCal').show();
											$(this).html('<div class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary"><span class="ui-button-text">Done Selecting Dates</span></div>');
										}
									});
									$('.closeCal').click(function(){
										$('.calDone').trigger('click');
									});
									$('.allCalendar').css('position','absolute');
									$('.allCalendar').css('top', '0px');
									$('.allCalendar').css('padding', '10px');
									$('.allCalendar').css('padding-bottom', '55px');
									$('.allCalendar').css('background-color','#ffffff');
									$('.allCalendar').css('z-index', '1005');
									$('.allCalendar').css('left', '10px');
		                            var myInitialDates = $('.mydates').html();
									var goodDates = jQuery.makeArray(data.goodDates);
		                            $('.myCalendar').datepick({
										useThemeRoller: true,
										dateFormat: '<?php echo getJsDateFormat();?>',
										multiSelect: 999,
										multiSeparator: ',',
										defaultDate: data.events_date,
										changeMonth: false,
										firstDay:0,
										changeYear: false,
										numberOfMonths: 1,
										onSelect: function (value, date, inst) {
											var dates = value.split(',');
		                                    html = '<div class="mydates">';
											for(p=0;p<dates.length;p++){
												html += '<input type="hidden" name="multiple_dates[]" value="'+dates[p]+'">';
											}
											html +='</div>';
											$('.mydates').remove();
		                                    self.parent().append(html);
										},

										beforeShowDay: function (dateObj) {
											dateObj.setHours(0, 0, 0, 0);
											var dateFormatted = $.datepick.formatDate('yy-m-d', dateObj);
											today = new Date();
		                                    if ($.inArray(dateFormatted, goodDates) > -1 && (today.getTime() <= dateObj.getTime() - (1000 * 60 * 60 * 24 - (24 - dateObj.getHours()) * 1000 * 60 * 60))){
												return [true, '', 'Available'];
											}

											return [false, '', 'Outside event days'];
										}
									});
									$('.myCalendar').hide();
									$('.myTextCalendar').hide();

									if(data.selectedDates != ''){
										var selDates =  jQuery.makeArray(data.selectedDates);
										//var selDates = '08/21/2011,08/23/2011'.split(',');
										$('.myCalendar').datepick('setDate', selDates);
										$('.calDone').trigger('click');
										$('.calDone').trigger('click');
									}else{

										$('.calDone').trigger('click');
										//$('.calDone').trigger('click');
									}
								}

								if(typeof data.data != undefined){
									<?php if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'False'){?>
										self.parent().parent().find('.shipf').html(data.data);
										self.parent().parent().find('.shipf').change();
									if ($.browser.msie) $('.eventf').removeClass('expand clicked');
									if (data.nr == 0){
										alert('<?php echo sysLanguage::get('EXTENSION_PAY_PER_RENTALS_DELIVERY_PASSED');?>');
									}
								<?php }else{
			                    ?>
								self.parent().parent().find('.gatef').html(data.data);
								self.parent().parent().find('.gatef').change();
								if ($.browser.msie) $('.eventf').removeClass('expand clicked');
								if (data.nr == 0){
									alert('<?php echo sysLanguage::get('EXTENSION_PAY_PER_RENTALS_NO_GATES');?>');
								}
								<?php
									}
								?>
								}


							}});

        });
		$('.shipups').hide();
		$('.changeZip').click(function(){
		    if($(this).find('.ui-button-text').html() == 'Get Quote'){
				var myel = $(this).parent();
				$ellem = $(this).closest('form');
				showAjaxLoader(myel,'xlarge');
							var self = $(this);
							$.ajax({
								 type: "post",
								 url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=setBefore'),
								 data: "rType=ajax&isZip=true&zipCode="+$(this).closest('form').find('.zipf').val()+'&ship_method='+$(this).closest('form').find('.shipz').val(),
								 success: function(data) {
									hideAjaxLoader(myel);
									if(data.nr != 0){
										$('.shipups').html(data.data);
										$('.shipups').show();
										$('.zipf').hide();
										$('.changeZip').find('.ui-button-text').html('Change Zip');
									}
								}
							});
			}else{
				$('.zipf').show();
				$('.changeZip').find('.ui-button-text').html('Get Quote');
				$('.shipups').hide();
			}
		});

		if($('.zipf').val() != ''){
			$('.changeZip').trigger('click');
		}

		$('.gatef').change(function(){

						myel = $(this).parent();
						showAjaxLoader(myel,'xlarge');
						var self = $(this);
                        $.ajax({
							 type: "post",
							 url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=setBefore'),
							 data: "rType=ajax&event="+$(this).closest('form').find('.eventf').val()+'&gate='+$(this).closest('form').find('.gatef').val(),
							 success: function(data) {
								$('.selectbox').remove();
								$('.selectbox-wrapper').remove();
		                        $('#mainppr select').selectbox();
								hideAjaxLoader(myel);

							}});

        });

		$('<?php echo '#'.$WidgetProperties->boxID;?> .mysh1').attr('href',"#");
		$('<?php echo '#'.$WidgetProperties->boxID;?> .mysh1').click(function(){
				link = js_app_link('appExt=payPerRentals&app=show_shipping&appPage=default&dialog=true&sh_id='+$(this).prev('.shipz').val());
				popupWindow(link,'400','300');
				return false;
		});

		$('<?php echo '#'.$WidgetProperties->boxID;?> .myga1').attr('href',"#");
		$('<?php echo '#'.$WidgetProperties->boxID;?> .myga1').click(function(){
				link = js_app_link('appExt=infoPages&app=show_page&appPage=gate_info&dialog=true');
				if($('.eventf').val() != '0'){
					link = js_app_link('appExt=payPerRentals&app=show_event&isgate=1&appPage=default&dialog=true&ev_id='+$('.eventf').val());
				}else{
					link = js_app_link('appExt=infoPages&app=show_page&appPage=gate_info&dialog=true');
				}
				popupWindow(link,'400','300');
				return false;
		});

		$('.catsh').click(function(){
		 	var inp = "<input type='hidden' name='cPath' value='"+$(this).attr('rel')+"'>";
		 	$('<?php echo '#'.$WidgetProperties->boxID;?> .sd').append(inp);
		 	$('<?php echo '#'.$WidgetProperties->boxID;?> .sd').submit();
			return false;
		});

        $('.shipf').change(function(){
					myel1 = $(this).parent();
					showAjaxLoader(myel1,'xlarge');
                        $.ajax({
							 type: "post",
							 url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=setBefore'),
							 data: "rType=ajax&ship_method="+$(this).val()+'&event='+$(this).closest('form').find('.eventf').val(),
							 success: function() {
								hideAjaxLoader(myel1);
							 }
						});
        });
		$('<?php echo '#'.$WidgetProperties->boxID;?> .rentbbut').button();
		$('<?php echo '#'.$WidgetProperties->boxID;?> .rentbbut').click(function(event){
			$(this).closest('form').submit();
			return false;
		});

		$('<?php echo '#'.$WidgetProperties->boxID;?> .changer').live('change',function(){

				if($(this).hasClass('pickupz')){
					pick = true;
				}else{
					pick = false;
				}
		        continent = 0;
				if($(this).hasClass('continent')){
					continent = 1;
				}
				if($(this).hasClass('country')){
					continent = 2;
				}
				if($(this).hasClass('state')){
					continent = 3;
				}
				if($(this).hasClass('city')){
					continent = 4;
				}

					$ellem = $(this).closest('form');
					showAjaxLoader($ellem,'xlarge');
							$.ajax({
								type: "post",
								url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=setBefore'),
								data: $ellem.serialize()+"&rType=ajax&pick="+pick+'&isContinent='+continent,
								success: function(data) {
									$ellem.find('.invCenter').replaceWith(data.data);
									$ellem.find('.rentbbut').button();
									$ellem.find('.rentbbut').click(function(){
										$ellem.submit();
										return false;
									});
			                        $ellem.trigger('EventAfterChanger');

									/*move into extension with trigger and bind*/
									$ellem1 = $('.main_list');

									if($ellem1){
										 showAjaxLoader($ellem1, 'xlarge');
										 $.ajax({
											 type: "post",
											 url: js_app_link('appExt=inventoryCenters&app=show_inventory&appPage=list&action=getAjaxList'),
											 data: $ellem.serialize() + "",
											 success: function(data) {
												 hideAjaxLoader($ellem1);
												 $ellem1.replaceWith(data.data);
												 return false;

											 }
										 });
									}
									hideAjaxLoader($ellem);
							 	}
							});

		});


		$('<?php echo '#'.$WidgetProperties->boxID;?> .myf').change(function(){
			link = js_app_link('appExt=inventoryCenters&app=show_inventory&appPage=default&inv_id='+$(this).val());
			$('<?php echo '#'.$WidgetProperties->boxID;?> .myf1').attr('href',link);

		});

		$('<?php echo '#'.$WidgetProperties->boxID;?> .myg').change(function(){
			if($(this).val()){
				link = js_app_link('appExt=inventoryCenters&app=show_inventory&appPage=default&inv_id='+$(this).val());
				$('<?php echo '#'.$WidgetProperties->boxID;?> .myg1').attr('href',link);
			}

		});

		$('.eventf').trigger('change');
		$('#ui-datepicker-div').css('z-index','10000');

	});


		<?php
			 $javascript = '/* Navigation Menu --BEGIN-- */' . "\n" .
				ob_get_contents();
			'/* Navigation Menu --END-- */' . "\n";
			ob_end_clean();

			return $javascript;
		}

}
?>