<?php
	$pageContents = '';
	if ($product->isValid() === false || $product->isActive() === false){
		$notFoundDiv = htmlBase::newElement('div')
		->addClass('ui-widget ui-widget-content ui-corner-all')
		->css('padding', '.5em')
		->html(sysLanguage::get('TEXT_PRODUCT_NOT_FOUND'));

		$continueButton = htmlBase::newElement('button')->usePreset('continue')
		->setHref(itw_app_link(null, 'index', 'default'));

		$buttonBar = htmlBase::newElement('div')
		->addClass('ui-widget ui-widget-content ui-corner-all pageButtonBar')
		->append($continueButton);

		$pageTitle = sysLanguage::get('TEXT_PRODUCT_NOT_FOUND');
		$pageContents .= $notFoundDiv->draw() . $buttonBar->draw();
	} else {
		$pageTitle = $product->getName();
		if ($product->hasModel()){
			$pageTitle .= '&nbsp;<span class="smallText">[' . $product->getModel() . ']</span>';
		}
		$product->updateViews();

		//DISPLAY PRODUCT WAS ADDED TO WISHLIST IF WISHLIST REDIRECT IS ENABLED
		if (Session::exists('wishlist_id') === true){
			Session::remove('wishlist_id');
			$pageContents .= '<div class"messageStackSuccess">' . sysLanguage::get('PRODUCT_ADDED_TO_WISHLIST') . '</div>';
		}

		$showAlsoPurchased = false;
		if (isset($_GET['products_id'])) {
			$orders_query = tep_db_query("select p.products_id, p.products_image from " . TABLE_ORDERS_PRODUCTS . " opa, " . TABLE_ORDERS_PRODUCTS . " opb, " . TABLE_ORDERS . " o, " . TABLE_PRODUCTS . " p where opa.products_id = '" . (int)$_GET['products_id'] . "' and opa.orders_id = opb.orders_id and opb.products_id != '" . (int)$_GET['products_id'] . "' and opb.products_id = p.products_id and opb.orders_id = o.orders_id and p.products_status = '1' group by p.products_id order by o.date_purchased desc limit " . MAX_DISPLAY_ALSO_PURCHASED);
			$num_products_ordered = tep_db_num_rows($orders_query);
			if ($num_products_ordered >= MIN_DISPLAY_ALSO_PURCHASED) {
				$showAlsoPurchased = true;
			}
		}
		
		$contents = EventManager::notifyWithReturn('ProductInfoBeforeInfo', &$product);
		if (!empty($contents)){
			foreach($contents as $content){
				$pageContents .= $content;
			}
		}

		$pageContents .= '<div id="tabs"><ul>';
		$pageContents .= '<li><a href="#tabImage"><span>'.sysLanguage::get('TAB_OVERVIEW').'</span></a></li>';
		if ($showAlsoPurchased === true){
			$pageContents .= '<li><a href="#tabAlsoPurchased"><span>'.sysLanguage::get('TAB_ALSO_PURCHASED').'</span></a></li>';
		}
		
		$contents = EventManager::notifyWithReturn('ProductInfoTabHeader', &$product);
		if (!empty($contents)){
			foreach($contents as $content){
				$pageContents .= $content;
			}
		}
		$pageContents .= '</ul>';

		$pageContents .= '<div id="tabImage">';
		ob_start();
		include($pageTabsFolder . 'tab_image.php');
		$pageContents .= ob_get_contents();
		ob_end_clean();
		$pageContents .= '</div>';

		if ($showAlsoPurchased === true){
			$pageContents .= '<div id="tabAlsoPurchased">';
			ob_start();
			include($pageTabsFolder . 'tab_also_purchased.php');
			$pageContents .= ob_get_contents();
			ob_end_clean();
			$pageContents .= '</div>';
		}
		
		$contents = EventManager::notifyWithReturn('ProductInfoTabBody', &$product);
		if (!empty($contents)){
			foreach($contents as $content){
				$pageContents .= $content;
			}
		}
		
		$pageContents .= '</div>';
		
		$contents = EventManager::notifyWithReturn('ProductInfoAfterInfo', &$product);
		if (!empty($contents)){
			foreach($contents as $content){
				$pageContents .= $content;
			}
		}
	}

	$pageButtons = '';
	
	$content = EventManager::notifyWithReturn('ProductInfoButtonBarAddButton', $product);
	if (!empty($content)){
		foreach($content as $html){
			$pageButtons .= $html;
		}
	}
	
	$link = itw_app_link('cPath=' . tep_get_product_path($product->getID()), 'index', 'default');
	if (sizeof($navigation->snapshot)){
		$getVars = array();
		if (is_array($navigation->snapshot['get'])){
			foreach($navigation->snapshot['get'] as $k => $v){
				$getVars[] = $k . '=' . $v;
			}
		}else{
			$getVars[] = $navigation->snapshot['get'];
		}
		$link = tep_href_link($navigation->snapshot['page'], implode('&', $getVars), $navigation->snapshot['mode']);
	}
	
	$pageButtons .= htmlBase::newElement('button')
	->usePreset('back')
	->setHref($link)
	->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
