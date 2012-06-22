<?php
	$conditions = array();
	if (isset($_GET['ptype'])){
		$conditions[] = $typeNames[$_GET['ptype']] . ' <a href="' . itw_app_link(tep_get_all_get_params(array('ptype')), 'products', 'search_result') . '">(x)</a>';
	}
	if (isset($_GET['pfrom']) && is_array($_GET['pfrom'])){
		foreach($_GET['pfrom'] as $k => $v){
			$conditions[] = $currencies->format($v) . ' - ' . $currencies->format($_GET['pto'][$k]) . ' <a href="' . itw_app_link(tep_get_all_get_params(array('pfrom[' . $k . ']', 'pto[' . $k . ']')), 'products', 'search_result') . '">(x)</a>';
		}
	}

	if ($appExtension->isInstalled('attributes') && $appExtension->isEnabled('attributes')){
		if (isset($_GET['options']) && isset($_GET['values'])){
			$Attributes = $appExtension->getExtension('attributes');
			foreach($_GET['options'] as $i => $optionId){
				$valueId = $_GET['values'][$i];
				$attrib = attributesUtil::getAttributes(null, $optionId, $valueId);
				if (!empty($attrib)){
					$conditions[] = $attrib[0]['ProductsOptions']['ProductsOptionsDescription'][Session::get('languages_id')]['products_options_name'] . ': ' . $attrib[0]['ProductsOptionsValues']['ProductsOptionsValuesDescription'][Session::get('languages_id')]['products_options_values_name'] . ' <a href="' . itw_app_link(tep_get_all_get_params(array('options[' . $i . ']', 'values[' . $i . ']')), 'products', 'search_result') . '">(x)</a>';
				}
			}
		}
	}
	if ($appExtension->isInstalled('customFields') && $appExtension->isEnabled('customFields')){
		$customFields = $appExtension->getExtension('customFields');
		if (isset($customFields->validSearchKeys)){
			$validFields = $customFields->validSearchKeys;
			if (sizeof($validFields) > 0){
				foreach($validFields as $k => $v){
					if (is_array($v)){
						foreach($v as $count => $val){
							$conditions[] = $val . ' <a href="' . itw_app_link(tep_get_all_get_params(array($k . '[' . $count . ']')), 'products', 'search_result') . '">(x)</a>';
						}
					}else{
						$conditions[] = $v . ' <a href="' . itw_app_link(tep_get_all_get_params(array($k)), 'products', 'search_result') . '">(x)</a>';
					}
				}
			}
		}
	}

	$Qproducts = Doctrine_Query::create()
	->select('DISTINCT p.products_id')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('p.ProductsToBox p2b')
	->where('p.products_status = ?', '1')
	->andWhere('p.is_hidden = ?', '0')
	->andWhere('p2b.products_id is null')
	->andWhere('pd.language_id = ?', (int)Session::get('languages_id'));

	if (isset($_GET['categories_id']) && tep_not_null($_GET['categories_id'])){
		$Qproducts->leftJoin('p.ProductsToCategories p2c');
		if (isset($_GET['inc_subcat']) && ($_GET['inc_subcat'] == '1')){
			$subcategories_array = array();
			tep_get_subcategories($subcategories_array, $_GET['categories_id']);

			$Qproducts->andWhereIn('p2c.categories_id', $subcategories_array);
		}else{
			$Qproducts->andWhere('p2c.categories_id = ?', (int)$_GET['categories_id']);
		}
	}

	
	if (isset($_GET['ptype']) && tep_not_null($_GET['ptype'])){
		$Qproducts->andWhere('FIND_IN_SET(?, p.products_type) > 0', $_GET['ptype']);
	}
	
	if (isset($search_keywords) && (sizeof($search_keywords) > 0)){
		$where_str = '(';
		for($i=0, $n=sizeof($search_keywords); $i<$n; $i++){
			switch($search_keywords[$i]){
				case '(':
				case ')':
				case 'and':
				case 'or':
					$where_str .= " " . $search_keywords[$i] . " ";
					break;
				default:
					$keyword = addslashes(strip_tags($search_keywords[$i]));
					$where_str .= '(pd.products_name like "%' . $keyword . '%" or p.products_model like "%' . $keyword . '%"';
					
					if (isset($_GET['search_in_description']) && ($_GET['search_in_description'] == '1')){
						$where_str .= ' or pd.products_description like "%' . $keyword . '%"';
					}
					$where_str .= ')';
					break;
			}
		}
		$where_str .= ')';
		$Qproducts->andWhere($where_str);
	}
	
	if (tep_not_null($dfrom)) {
		$Qproducts->andWhere('p.products_date_added >= ?', $dfrom);
	}
	
	if (tep_not_null($dto)) {
		$Qproducts->andWhere('p.products_date_added <= ?', $dto);
	}
	
	if (tep_not_null($pfrom)){
		if (is_array($pfrom)){
			$queryAdd = array();
			if ($currencies->is_set(Session::get('currency'))){
				$rate = $currencies->get_value(Session::get('currency'));
			}
			foreach($pfrom as $k => $v){
				if (isset($rate)){
					$v = $v / $rate;
					if (isset($pto[$k])){
						$pto[$k] = $pto[$k] / $rate;
					}
				}
				$queryAddString = '(p.products_price >= ' . (double)$v;
				if (isset($pto[$k])){
					$queryAddString .= ' AND p.products_price <= ' . (double)$pto[$k];
				}
				$queryAddString .= ')';
				
				$queryAdd[] = $queryAddString;
			}
			$priceFiltersCheck = Doctrine_Query::create()
				->select('p.products_id')
				->from('Products p')
				->where('(' . implode(' or ', $queryAdd) . ')')
				->fetchArray();
			if(count($priceFiltersCheck) > 0) {
				foreach($priceFiltersCheck as $priceFilterProductID){
					$priceFilters[$priceFilterProductID['products_id']] = $priceFilterProductID['products_id'];
				}
			}
			EventManager::notify('ProductSearchQueryPriceFilterBeforeExecute', &$priceFilters);
			$Qproducts->andWhere('p.products_id in (' . implode(', ', $priceFilters) . ')');


		}else{
			if ($currencies->is_set(Session::get('currency'))){
				$rate = $currencies->get_value(Session::get('currency'));
				$pfrom = $pfrom / $rate;
			}
			$Qproducts->andWhere('p.products_price >= ?', (double)$pfrom);
		}
	}
	
	if (tep_not_null($pto) && !is_array($pto)){
		if (isset($rate)){
			$pto = $pto / $rate;
		}
		$Qproducts->andWhere('p.products_price <= ?', (double)$pto);
	}
	
	EventManager::notify('ProductSearchQueryBeforeExecute', &$Qproducts);

	if(sysConfig::get('PRODUCT_LISTING_TYPE') == 'row'){
		$productListing = new productListing_row();
	} else {
		$productListing = new productListing_col();
	}
	$productListing->setQuery($Qproducts);
	
	$pageContents = '';
	if (!empty($conditions)){
		$pageContents .= htmlBase::newElement('div')
		->css(array('margin-top' => '.5em'))
		->html(implode(' &raquo; ', $conditions))
		->draw();
	}

	if (isset($_GET['plural']) && ($_GET['plural'] == '1')) {
		$pageContents .= htmlBase::newElement('div')
		->css(array('margin-top' => '.5em'))
		->html(sysLanguage::get('TEXT_REPLACEMENT_SEARCH_RESULTS') . ' <b><i>' . stripslashes($_GET['keywords']) . '</i></b>')
		->draw();
	}else{
		$searchedFor = array();
		if (isset($_GET['keywords']) && !empty($_GET['keywords'])){
			$searchedFor[] = stripslashes($_GET['keywords']);
			
			$pageContents .= htmlBase::newElement('div')
			->css(array('margin-top' => '.5em'))
			->html(sysLanguage::get('TEXT_REPLACEMENT_SEARCH_RESULTS') . ' <b><i>' . implode(', ', $searchedFor) . '</i></b>')
			->draw();
		}
	}
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(tep_get_all_get_params(array('sort', 'page')), 'products', 'search', 'NONSSL', true, false))
	->draw();
	
	$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE_SEARCH_RESULT'));
	$pageContent->set('pageContent', $pageContents . $productListing->draw());
	$pageContent->set('pageButtons', $pageButtons);
