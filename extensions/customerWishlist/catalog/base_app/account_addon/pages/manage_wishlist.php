<?php
	$is_public = false;
	$the_id = $userAccount->getCustomerId();
	
	//if viewing a public profile
	if( isset( $_GET['wishlist'] ) ) {
		$is_public = true;
		$the_id = (int)$_GET['wishlist'];
	}
	
	//if a search
	if( isset( $_POST['wishlist_search'] ) ) {
		$is_public = true;
		
		 $QfindWishlist = Doctrine_Query::create()
		->from('Customers')
		->where("customers_email_address='".mysql_real_escape_string( $_POST['wishlist_search'] )."'")
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		if( isset( $QfindWishlist[0] ) ) {
			$the_id = $QfindWishlist[0]['customers_id'];
			
			//get this users settings
			$currentSettings = $QcustomerWishlist = Doctrine_Query::create()
				->from('CustomerWishlistSettings')
				->where('customers_id=?', $the_id)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				
			if( isset($currentSettings[0]) && $currentSettings[0]['wishlist_search'] == 0 )
				$the_id = 0;
		} else {
			$the_id = 0;
		}
	}
	
	//get this users settings
	$currentSettings = $QcustomerWishlist = Doctrine_Query::create()
		->from('CustomerWishlistSettings')
		->where('customers_id=?', $the_id)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);	
	
	//get wishlist
    $QcustomerWishlist = Doctrine_Query::create()
	->from('CustomerWishlist cf')
    ->leftJoin('cf.CustomersWishlistProductAttributes cfpa')
	->where('cf.customers_id=?', $the_id)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	if( count( $QcustomerWishlist ) && ( !$is_public || (isset($currentSettings[0]) && $currentSettings[0]['wishlist_public'] == 1) ) ) {
		$OverViewTable = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0)
		->css(array(
			'width' => '100%'
		));
					
		$OverViewTableHeader = array(
			array('css' => array('text-align' => 'center'),'text' => 'Select'),
			array('css' => array('text-align' => 'center'),'text' => sysLanguage::get('TABLE_HEADING_PRODUCTS_NAME')),
			array('css' => array('text-align' => 'center'),'text' => sysLanguage::get('TABLE_HEADING_PURCHASE_TYPE')),
			array('css' => array('text-align' => 'center'),'text' => sysLanguage::get('TABLE_HEADING_ATTRIBUTE'))
		);
	
		$OverViewTable->addHeaderRow(array(
			'addCls' => 'ui-widget-header ui-state-hover',
			'columns' => $OverViewTableHeader
		));
		
		foreach($QcustomerWishlist as $iWishlist){
	
			$QProduct = Doctrine_Query::create()
			->from('Products p')
			->leftJoin('p.ProductsDescription pd')
			->where('p.products_id=?', $iWishlist['products_id'])
			->andWhere('pd.language_id=?', Session::get('languages_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	
			$attr = '';
			foreach($iWishlist['CustomersWishlistProductAttributes'] as $iAttr){
				$Query = Doctrine_Query::create()
				->from('ProductsAttributes a')
				->leftJoin('a.ProductsOptions o')
				->leftJoin('o.ProductsOptionsDescription od')
				->leftJoin('a.ProductsOptionsValues ov')
				->leftJoin('ov.ProductsOptionsValuesDescription ovd')
				->leftJoin('ov.ProductsOptionsValuesToProductsOptions v2o')
				->where('a.products_attributes_id=?', $iAttr['products_attributes_id'])
				->andWhere('od.language_id=?', Session::get('languages_id'))
				->andWhere('ovd.language_id=?', Session::get('languages_id'))
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	
				$attr .= $Query[0]['ProductsOptions']['ProductsOptionsDescription'][0]['products_options_name'].': '.$Query[0]['ProductsOptionsValues']['ProductsOptionsValuesDescription'][0]['products_options_values_name'].'<br/>';
			}
	
			$OverViewTableBody = array(
					array('css' => array('text-align' => 'center'),'text' => '<input name="customerWishlistSelect[]" type="checkbox" value="'.$iWishlist['customer_wishlist_id'].'">'),
					array('css' => array('text-align' => 'center'),'text' => $QProduct[0]['ProductsDescription'][0]['products_name']),
					array('css' => array('text-align' => 'center'),'text' => $iWishlist['purchase_type']),
					array('css' => array('text-align' => 'center'), 'text' => $attr)
			);
	
	
			$OverViewTable->addBodyRow(array(
				'columns' => $OverViewTableBody
			));
		}
		
		//build contents
		$pageContents = customerWishlistSocialSharing( $the_id ) . $OverViewTable->draw();
	} else {
		$pageContents = sysLanguage::get('WISHLIST_IS_EMPTY');
	}

	//if viewing a public profile
	if( $is_public ) {
		$user = new RentalStoreUser( $the_id );
		$pageTitle = $user->getFirstName().' '.sysLanguage::get('HEADING_TITLE_WISHLIST_PUBLIC');
		$pageSettings = '';
	} else {
		$pageTitle = sysLanguage::get('HEADING_TITLE_WISHLIST');
		
		$_public = isset( $currentSettings[0] ) && $currentSettings[0]['wishlist_public'] == 1 ? ' checked="checked"' : '';
		$_search = isset( $currentSettings[0] ) && $currentSettings[0]['wishlist_search'] == 1 ? ' checked="checked"' : '';
		
		//page settings
		$pageSettings = '<br /><input name="wishlistAllowPublic" type="checkbox"'.$_public.' value="true" /> '.sysLanguage::get('WISHLIST_SETTING_PUBLIC');
		$pageSettings .= '<br /><input name="wishlistAllowSearch" type="checkbox"'.$_search.' value="true" /> '.sysLanguage::get('WISHLIST_SETTING_SEARCH');
		$pageSettings .= '<input type="hidden" name="settingsUserId" value="'.$the_id.'" />';
	}
	
	//the search
	$pageSearch = htmlBase::newElement('button')
	->setType('submit')
	->setId('searchWishlist')
	->setName('search_wishlist')
	->setText(sysLanguage::get('WISHLIST_BUTTON_SEARCH'))
	->draw();
	$pageSearch = '<div id="wishlist_search_wrapper"><input type="text" name="wishlist_search" /> '.$pageSearch.'</div>';
	
	$pageContents = $pageContents . $pageSettings . $pageSearch . '<div class="selectDialog"></div>';
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null,'account','default'))
	->draw();

	$pageButtons .= htmlBase::newElement('button')
	->setType('submit')
	->setId('saveSettings')
	->setName('save_settings')
	->setText(sysLanguage::get('WISHLIST_BUTTON_SETTING_SAVE'))
	->draw();

	$pageButtons .= htmlBase::newElement('button')
	->setType('submit')
	->setId('removeWishlist')
	->setName('remove_wishlist')
	->setText(sysLanguage::get('TEXT_REMOVE_SELECTED_WISHLIST'))
	->draw();

	$pageButtons .= htmlBase::newElement('button')
	->setType('submit')
	->setName('add_to_cart_wishlist')
	->setId('addCartWishlist')
	->setText(sysLanguage::get('TEXT_ADD_SELECTED_TO_CART'))
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'manage_wishlist',
		'action' => itw_app_link('appExt=customerWishlist&action=addToCart', 'account_addon', 'manage_wishlist', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
