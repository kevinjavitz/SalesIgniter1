<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxSearch extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('search');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_SEARCH'));
		if ($App->getEnv() == 'catalog'){
			$this->setBoxHeadingLink(itw_app_link(null, 'products', 'search_result'));
		}
		$this->buildStylesheetMultiple = false;
		$this->buildJavascriptMultiple = false;
	}

	public function show(){

		$boxContent = tep_draw_form('quick_find', itw_app_link(null, 'products', 'search_result'), 'get') .
		              tep_draw_input_field('keywords', '', 'size="10" maxlength="30" style="width: ' . (BOX_WIDTH-30) . 'px"') .
		              '&nbsp;' .
		              tep_hide_session_id() .
		              htmlBase::newElement('button')->css(array('font-size' => '.8em'))->setType('submit')->setText(' Go ')->draw() .
		              '<br>' .
		              sysLanguage::get('INFOBOX_SEARCH_TEXT') .
		              '<br><a href="' . itw_app_link(null, 'products', 'search') . '"><b>' . sysLanguage::get('INFOBOX_SEARCH_ADVANCED_SEARCH') . '</b></a>' .
		              '</form><br />';
		$boxContent = tep_draw_form('quick_find', itw_app_link(null, 'products', 'search_result'), 'get') .
		              tep_hide_session_id();

		$boxWidgetProperties = $this->getWidgetProperties();
		if(isset($boxWidgetProperties->searchOptions)){
			$Qitems = (array)$boxWidgetProperties->searchOptions;
			array_map('json_decode',$Qitems);
		}

		if (isset($Qitems) && count($Qitems) > 0){
			$boxContent .= '<div class="ui-widget ui-widget-content ui-corner-all-medium"><div class="ui-widget-header ui-infobox-header guidedHeader" ><div class="ui-infobox-header-text">'.sysLanguage::get('INFOBOX_SEARCH_GUIDED_SEARCH').'</div></div><form name="guided_search" action="' . itw_app_link(null, 'products', 'search_result') . '" method="get">';
			$this->searchItemDisplay = 4;
			$prices = false;
			$pricesPPR = false;
			$boxContents = array();
			foreach($Qitems as $type){
				$type = (array)$type;
				foreach($type as $sInfo){
					$sInfo = (array)$sInfo;
					$sInfo['search_title'] = (array)$sInfo['search_title'];

					foreach($sInfo['search_title'] as $key => $search_title){
						if((int)$key == (int)Session::get('languages_id')){
							$heading = $search_title;
							break;
						}
					}

					//$boxContents[$sInfo['option_type']]['heading'] = $heading;
					//$boxContents[$sInfo['option_type']]['heading'] = $sInfo['search_title'][(int)Session::get('languages_id')];

					switch($sInfo['option_type']){
						case 'attribute':
							$boxContents[$sInfo['option_type']][$sInfo['option_id']]['heading'] = $heading;
							$this->guidedSearchAttribute(&$boxContents['attribute'][$sInfo['option_id']]['content'], &$sInfo['option_id'], &$boxContents['attribute'][$sInfo['option_id']]['count'], isset($boxWidgetProperties->dropdown->attribute));
							break;
						case 'custom_field':
							$boxContents[$sInfo['option_type']][$sInfo['option_id']]['heading'] = $heading;
							$this->guidedSearchCustomField(&$boxContents['custom_field'][$sInfo['option_id']]['content'], &$sInfo['option_id'], &$boxContents['custom_field'][$sInfo['option_id']]['count'], isset($boxWidgetProperties->dropdown->custom_field));
							break;
						case 'purchase_type':
							$boxContents['attribute'][$sInfo['option_type']]['heading'] = $heading;
							$this->guidedSearchPurchaseType(&$boxContents['purchase_type']['content'], isset($boxWidgetProperties->dropdown->purchase_type));
							break;
						case 'price':
							$boxContents['price'][$sInfo['option_type']]['heading'] = $heading;
							$prices[] = array(
								'price_start' => $sInfo['price_start'],
								'price_stop' => $sInfo['price_stop']
							);
							break;
						case 'priceppr':
							$boxContents[$sInfo['option_type']]['heading'] = $heading;
							$pricesPPR[] = array(
								'price_start' => $sInfo['price_start'],
								'price_stop' => $sInfo['price_stop']
							);
							break;
						case 'manufacturer':
							$boxContents[$sInfo['option_type']]['heading'] = $heading;
							$this->guidedSearchManufacturer(&$boxContents['manufacturer']['content'], isset($boxWidgetProperties->dropdown->manufacturer));
							break;
					}
				}
			}
			if($prices && count($prices)){
				$this->guidedSearchPrice(&$boxContents['price']['content'], $prices, isset($boxWidgetProperties->dropdown->price));
			}
			if($pricesPPR && count($pricesPPR)){
				$this->guidedSearchPricePPR(&$boxContents['priceppr']['content'], $pricesPPR, isset($boxWidgetProperties->dropdown->priceppr));
			}

			foreach($boxContents as $type => $content){
				if(is_array($content) && !isset($content['content'])){
					foreach($content as $optionID => $optionContent){
						$boxContent .= '<br /><b '.(isset($boxWidgetProperties->dropdown->{$type}) ? 'style="margin:.5em"' : '').'>' . $optionContent['heading'] . '</b><ul style="list-style:none;margin:.5em;padding:0px">';
						$boxContent .= $optionContent['content'];
						if($optionContent['count'] > $this->searchItemDisplay && !isset($boxWidgetProperties->dropdown->{$type})){
							$boxContent .= '<li class="searchShowMoreLink"><a href="#"><b>More</b></a></li>';
						}
						$boxContent .= '</ul>';
					}
				} else {
					$boxContent .= '<br /><b '.(isset($boxWidgetProperties->dropdown->{$type}) ? 'style="margin:.5em"' : '').'>' . $content['heading'] . '</b><ul style="list-style:none;margin:.5em;padding:0px">';
					$boxContent .= $content['content'];
					if($content['count'] > $this->searchItemDisplay && !isset($boxWidgetProperties->dropdown->{$type})){
						$boxContent .= '<li class="searchShowMoreLink"><a href="#"><b>More</b></a></li>';
					}
					$boxContent .= '</ul>';
				}
			}
			if(isset($boxWidgetProperties->dropdown)){
				$boxContent .= htmlBase::newElement('div')
					->css(
					array('padding-left' => '8px'))
					->append(htmlBase::newElement('button')
						         ->css(array('font-size' => '.8em'))
						         ->setType('submit')
						         ->setText(' Submit '))
					->draw();
			}
			$boxContent .= '</form></div>';
		}

		//EventManager::notify('SearchBoxAddGuidedOptions', &$boxContent);

		$this->setBoxContent($boxContent);

		return $this->draw();
	}
	
	private function guidedSearchAttribute(&$boxContent, $optionId, &$count, $dropdown){
		global $appExtension;
		$extAttributes = $appExtension->getExtension('attributes');
		if ($extAttributes){
			$extAttributes->SearchBoxAddGuidedOptions(&$boxContent, $optionId, &$count, $dropdown);
		}
	}
	
	private function guidedSearchCustomField(&$boxContent, $fieldId, &$count, $dropdown){
		global $appExtension;
		$extCustomFields = $appExtension->getExtension('customFields');
		if ($extCustomFields){
			$extCustomFields->SearchBoxAddGuidedOptions(&$boxContent, $fieldId, &$count, $dropdown);
		}
	}

	private function guidedSearchPurchaseType(&$boxContent){
		global $typeNames;
		$count = 0;
		foreach($typeNames as $k => $v){
			if($k == 'new'){
				$v = 'Buy';
			}elseif($k == 'reservation'){
				$v = 'Rent';
			}

			$QproductCount = Doctrine_Query::create()
				->select('count(*) as total')
				->from('Products')
				->where('FIND_IN_SET(?, products_type)', $k)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if($QproductCount[0]['total'] <= 0)
				continue;
			$checkIcon = '<span class="ui-icon ui-icon-check" style="display:inline-block;height:14px;background:none;"></span>';
			$link = itw_app_link(tep_get_all_get_params(array('ptype')) . 'ptype=' . $k, 'products', 'search_result');
			if (isset($_GET['ptype']) && $_GET['ptype'] == $k){
				$checkIcon = '<span class="ui-icon ui-icon-check" style="display:inline-block;height:14px;"></span>';
				$link = itw_app_link(tep_get_all_get_params(array('ptype')), 'products', 'search_result');
			}
			$icon = '<span class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;">' .
			        $checkIcon .
			        '</span>';

			$boxContent .= '<li style="padding-bottom:.3em;' . ($count > $this->searchItemDisplay ? 'display:none;' : '') . '">' .
			               ' <a href="' . $link . '" data-url_param="ptype=' . $k . '">' .
			               $icon .
			               $v .
			               '</a> (' . $QproductCount[0]['total'] . ')' .
			               '</li>';
			$count++;
		}


		if ($count > $this->searchItemDisplay){
			$boxContent .= '<li class="searchShowMoreLink"><a href="#"><b>More</b></a></li>';
		}
	}

	private function guidedSearchPrice(&$boxContent, $prices){
		global $currencies;
		$count = 0;
		foreach($prices as $pInfo){
			$QproductCount = Doctrine_Query::create()
				->select('count(*) as total')
				->from('Products')
				->where('products_price >= ?', $pInfo['price_start'])
				->andWhere('products_price <= ?', $pInfo['price_stop'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$checkIcon = '<span class="ui-icon ui-icon-check" style="display:inline-block;height:14px;background:none;"></span>';
			$link = itw_app_link(tep_get_all_get_params(array('pfrom[' . $count . ']', 'pto[' . $count . ']')) . 'pfrom[' . $count . ']=' . $pInfo['price_start'] . '&pto[' . $count . ']=' . $pInfo['price_stop'], 'products', 'search_result');
			if (isset($_GET['pfrom'][$count])){
				$checkIcon = '<span class="ui-icon ui-icon-check" style="display:inline-block;height:14px;"></span>';
				$link = itw_app_link(tep_get_all_get_params(array('pfrom[' . $count . ']', 'pto[' . $count . ']')), 'products', 'search_result');
			}
			$icon = '<span class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;">' .
			        $checkIcon .
			        '</span>';

			$boxContent .= '<li style="padding-bottom:.3em;' . ($count > $this->searchItemDisplay ? 'display:none;' : '') . '">' .
			               ' <a href="' . $link . '" data-url_param="pfrom[' . $count . ']=' . $pInfo['price_start'] . '&pto[' . $count . ']=' . $pInfo['price_stop'] . '">' .
			               $icon .
			               $currencies->format($pInfo['price_start']) . ' - ' . $currencies->format($pInfo['price_stop']) .
			               '</a>' . //' (' . $QproductCount[0]['total'] . ')' .
			               '</li>';
			$count++;
		}
		if ($count > $this->searchItemDisplay){
			$boxContent .= '<li class="searchShowMoreLink"><a href="#"><b>More</b></a></li>';
		}
	}

	private function guidedSearchPricePPR(&$boxContent, $prices){
		global $currencies;
		$count = 0;
		foreach($prices as $pInfo){
			$QproductCount = Doctrine_Query::create()
				->select('count(*) as total')
				->from('ProductsPayPerRental pppr')
				->leftJoin('pppr.PricePerRentalPerProducts ppprp')
				->where(' ppprp.price >= ' . $pInfo['price_start'] . ' or ppprp.price <=' . $pInfo['price_stop'])
				->fetchArray();

			$checkIcon = '<span class="ui-icon ui-icon-check" style="display:inline-block;height:14px;background:none;"></span>';
			$link = itw_app_link(tep_get_all_get_params(array('pprfrom[' . $count . ']', 'pprto[' . $count . ']')) . 'pprfrom[' . $count . ']=' . $pInfo['price_start'] . '&pprto[' . $count . ']=' . $pInfo['price_stop'], 'products', 'search_result');
			if (isset($_GET['pprfrom'][$count])){
				$checkIcon = '<span class="ui-icon ui-icon-check" style="display:inline-block;height:14px;"></span>';
				$link = itw_app_link(tep_get_all_get_params(array('pprfrom[' . $count . ']', 'pprto[' . $count . ']')), 'products', 'search_result');
			}
			$icon = '<span class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;">' .
				$checkIcon .
			'</span>';

			$boxContent .= '<li style="padding-bottom:.3em;' . ($count > $this->searchItemDisplay ? 'display:none;' : '') . '">' .
				' <a href="' . $link . '" data-url_param="pprfrom[' . $count . ']=' . $pInfo['price_start'] . '&pprto[' . $count . ']=' . $pInfo['price_stop'] . '">' .
			    $icon .
				$currencies->format($pInfo['price_start']) . ' - ' . $currencies->format($pInfo['price_stop']) .
				'</a>' . //' (' . $QproductCount[0]['total'] . ')' .
			'</li>';
			$count++;
		}
		if ($count > $this->searchItemDisplay){
			$boxContent .= '<li class="searchShowMoreLink"><a href="#"><b>More</b></a></li>';
		}
	}
	
	private function guidedSearchManufacturer(&$boxContent){
		$Qmanufacturers = Doctrine_Query::create()
			->from('Manufacturers')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qmanufacturers){
			$count = 0;
			foreach($Qmanufacturers as $mInfo){
				$QproductCount = Doctrine_Query::create()
					->select('count(*) as total')
					->from('Products')
					->where('manufacturers_id = ?', $mInfo['manufacturers_id'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				$checkIcon = '<span class="ui-icon ui-icon-check" style="display:inline-block;height:14px;background:none;"></span>';
				$link = itw_app_link(tep_get_all_get_params(array('manufacturers_id[' . $count . ']')) . 'manufacturers_id[' . $count . ']=' . $mInfo['manufacturers_id'], 'products', 'search_result');
				if (isset($_GET['manufacturers_id'][$count])){
					$checkIcon = '<span class="ui-icon ui-icon-check" style="display:inline-block;height:14px;"></span>';
					$link = itw_app_link(tep_get_all_get_params(array('manufacturers_id[' . $count . ']')), 'products', 'search_result');
				}
				$icon = '<span class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;">' .
				        $checkIcon .
				        '</span>';

				$boxContent .= '<li style="padding-bottom:.3em;' . ($count > $this->searchItemDisplay ? 'display:none;' : '') . '">' .
				               ' <a href="' . $link . '" data-url_param="manufacturers_id[' . $count . ']=' . $mInfo['manufacturers_id'] . '">' .
				               $icon .
				               $mInfo['manufacturers_name'] .
				               '</a> (' . $QproductCount[0]['total'] . ')' .
				               '</li>';
				$count++;
			}
			if ($count > $this->searchItemDisplay){
				$boxContent .= '<li class="searchShowMoreLink"><a href="#"><b>More</b></a></li>';
			}
		}
	}

	public function buildStylesheet(){
		$css = '' . "\n" .
		       '.guidedSearch { ' .
		       ' }' . "\n" .
		       '.guidedSearchBreadCrumb { ' .
		       'margin-top:.8em;' .
		       'margin-bottom:.8em;' .
		       'font-size:.8em;' .
		       ' }' . "\n" .
		       '.guidedSearchBreadCrumb .main { ' .
		       'font-size: 1em;' .
		       'font-family: Tahoma, Arial;' .
		       ' }' . "\n" .
		       '.guidedSearchButtonBar { ' .
		       'text-align:center;' .
		       'font-size: .8em;' .
		       ' }' . "\n" .
		       '.guidedSearchButtonBar button { ' .
		       ' }' . "\n" .
		       '.guidedSearchHeading { ' .
		       'font-weight: bold;' .
		       ' }' . "\n" .
		       '.guidedSearchListing { ' .
		       'height:200px;' .
		       'overflow-x:hidden;' .
		       'overflow-y:scroll;' .
		       'position:relative;' .
		       ' }' . "\n" .
		       '.guidedSearchListing ul { ' .
		       'list-style: none;' .
		       'margin:0;' .
		       'padding:0;' .
		       'width:175px;' .
		       ' }' . "\n" .
		       '.guidedSearchListing ul li { ' .
		       'border: 1px solid transparent;' .
		       'margin:.2em;' .
		       ' }' . "\n" .
		       '.guidedSearchListing ul li span { ' .
		       'line-height:1.5em;' .
		       'margin-left:.3em;' .
		       ' }' . "\n" .
		       '' . "\n";

		return $css;
	}
}
?>