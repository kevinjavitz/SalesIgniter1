<?php
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
				
    $QcustomerFavorites = Doctrine_Query::create()
	->from('CustomerFavorites cf')
    ->leftJoin('cf.CustomersFavoritesProductAttributes cfpa')
	->where('cf.customers_id=?', $userAccount->getCustomerId())
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	foreach($QcustomerFavorites as $iFavorites){

		$QProduct = Doctrine_Query::create()
		->from('Products p')
		->leftJoin('p.ProductsDescription pd')
		->where('p.products_id=?', $iFavorites['products_id'])
		->andWhere('pd.language_id=?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$attr = '';
		foreach($iFavorites['CustomersFavoritesProductAttributes'] as $iAttr){
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
				array('css' => array('text-align' => 'center'),'text' => '<input name="customerFavoritesSelect[]" type="checkbox" value="'.$iFavorites['customer_favorites_id'].'">'),
				array('css' => array('text-align' => 'center'),'text' => $QProduct[0]['ProductsDescription'][0]['products_name']),
				array('css' => array('text-align' => 'center'),'text' => $iFavorites['purchase_type']),
				array('css' => array('text-align' => 'center'), 'text' => $attr)
		);


		$OverViewTable->addBodyRow(array(
			'columns' => $OverViewTableBody
		));
	}

	$pageTitle = sysLanguage::get('HEADING_TITLE_FAVORITES');
	$pageContents = $OverViewTable->draw() . '<div class="selectDialog"></div>';
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null,'account','default'))
	->draw();

	$pageButtons .= htmlBase::newElement('button')
	->setType('submit')
	->setId('removeFavorites')
	->setName('remove_favorites')
	->setText(sysLanguage::get('TEXT_REMOVE_SELECTED_FAVORITES'))
	->draw();

	$pageButtons .= htmlBase::newElement('button')
	->setType('submit')
	->setName('add_to_cart_favorites')
	->setId('addCartFavorites')
	->setText(sysLanguage::get('TEXT_ADD_SELECTED_TO_CART'))
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'manage_favorites',
		'action' => itw_app_link('appExt=customerFavorites&action=addToCart', 'account_addon', 'manage_favorites', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
