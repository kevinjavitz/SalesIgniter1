<?php
function get_category_tree_list($parent_id = '0', $checked = false, $include_itself = true, $ProdID){
	$langId = Session::get('languages_id');
	$catList = '';
	$QCategories = Doctrine_Query::create()
			->from('Categories c')
			->leftJoin('c.CategoriesDescription cd')
			->where('cd.language_id = ?', $langId)
			->andWhere('c.parent_id = ?', $parent_id)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	foreach($QCategories as $cat){
		$catList .= '<optgroup label="' . $cat['CategoriesDescription'][0]['categories_name'] . '">';
		$QProducts = Doctrine_Query::create()
				->from('Products p')
				->leftJoin('p.ProductsDescription pd')
				->leftJoin('p.ProductsToCategories p2c')
				->where('pd.language_id = ?', $langId)
				->andWhere('p2c.categories_id = ?', $cat['categories_id'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($QProducts as $products){
			$catList .= '<option value="' . $products['products_id'] . '">(' . $products['products_model'] . ") " . $products['ProductsDescription'][0]['products_name'] . '</option>';
		}
		if (tep_childs_in_category_count($cat['categories_id']) > 0){
			$catList .= get_category_tree_list($cat['categories_id'], $checked, false);
		}
		$catList .= '</optgroup>';
	}
	return $catList;
}
 function no_category($ProdID){
	$langId = Session::get('languages_id');
	$catList = '<optgroup label="nocategory">';
	$QProducts = Doctrine_Query::create()
			->from('Products p')
			->leftJoin('p.ProductsDescription pd')
			->leftJoin('p.ProductsToCategories p2c')
			->where('pd.language_id = ?', $langId)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	foreach($QProducts as $products){
		if(count($products['ProductsToCategories']) == 0){
			$catList .= '<option value="' . $products['products_id'] . '">(' . $products['products_model'] . ") " . $products['ProductsDescription'][0]['products_name'] . '</option>';
		}
	}
	$catList .= '</optgroup>';
	return $catList;
}
	$Coupons = Doctrine_Core::getTable('Coupons');
	if (isset($_GET['cID'])){
		$Coupon = $Coupons->find((int) $_GET['cID']);
		$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_EDIT_COUPON');
		$boxIntro = sysLanguage::get('TEXT_INFO_EDIT_INTRO');
	}else{
		$Coupon = $Coupons->getRecord();
		$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_NEW_COUPON');
		$boxIntro = sysLanguage::get('TEXT_INFO_INSERT_INTRO');
		$Coupon->coupon_start_date = date('Y-m-d', mktime(0,0,0, date('m'), date('d'), date('Y')));
		$Coupon->coupon_expire_date = date('Y-m-d', mktime(0,0,0, date('m'), date('d'), date('Y')+1));
	}

	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . $boxHeading . '</b>');
	$infoBox->setButtonBarLocation('top');

	$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBox->addButton($saveButton)->addButton($cancelButton);

	$purchaseTypeBoxes = array();
	$current = explode(',', $Coupon->restrict_to_purchase_type);
	foreach($typeNames as $name => $text){
		$checkbox = htmlBase::newElement('input')
		->setType('checkbox')
		->setName('restrict_to_purchase_type[]')
		->val($name)
		->setLabel($text)
		->setLabelPosition('after')
		->setChecked((in_array($name, $current)))
		->draw();
		
		EventManager::notify('CouponEditPurchaseTypeBeforeOutput', &$checkbox, $name, $Coupon);
		
		$purchaseTypeBoxes[] = $checkbox;
	}
	
	$infoBox->addContentRow($boxIntro);
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_STATUS') . '<br>' . tep_draw_radio_field('coupon_active', 'Y', ($Coupon->coupon_active == 'Y')) . '&nbsp;' . sysLanguage::get('TEXT_ENABLED') . '&nbsp;' . tep_draw_radio_field('coupon_active', 'N', ($Coupon->coupon_active == 'N')) . '&nbsp;' . sysLanguage::get('TEXT_DISABLED'));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_FREE_SHIPPING') . '<br>' . tep_draw_checkbox_field('coupon_free_ship', 'Y', ($Coupon->coupon_type == 'S')));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_AMOUNT') . ' - ' . sysLanguage::get('TEXT_INFO_COUPON_AMOUNT_HELP') . '<br>' . tep_draw_input_field('coupon_amount', $Coupon->coupon_amount . ($Coupon->coupon_type == 'P' ? '%' : '')));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_MIN_ORDER') . ' - ' . sysLanguage::get('TEXT_INFO_COUPON_MIN_ORDER_HELP') . '<br>' . tep_draw_input_field('coupon_minimum_order', $Coupon->coupon_minimum_order));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_MAX_ORDER') . ' - ' . sysLanguage::get('TEXT_INFO_COUPON_MAX_ORDER_HELP') . '<br>' . tep_draw_input_field('coupon_maximum_order', $Coupon->coupon_maximum_order));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_CODE') . ' - ' . sysLanguage::get('TEXT_INFO_COUPON_CODE_HELP') . '<br>' . tep_draw_input_field('coupon_code', $Coupon->coupon_code));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_USES_COUPON') . ' - ' . sysLanguage::get('TEXT_INFO_COUPON_USES_COUPON_HELP') . '<br>' . tep_draw_input_field('uses_per_coupon', $Coupon->uses_per_coupon));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_USES_USER') . ' - ' . sysLanguage::get('TEXT_INFO_COUPON_USES_USER_HELP') . '<br>' . tep_draw_input_field('uses_per_user', $Coupon->uses_per_user));
	//$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_PRODUCTS') . ' - ' . sysLanguage::get('TEXT_INFO_COUPON_PRODUCTS_HELP') . '<br>' . tep_draw_input_field('restrict_to_products', $Coupon->restrict_to_products));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_PURCHASE_TYPE') . ' - ' . sysLanguage::get('TEXT_INFO_COUPON_PURCHASE_TYPE_HELP') . '<br>' . implode('<br>', $purchaseTypeBoxes));
	//$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_CATEGORIES') . ' - ' . sysLanguage::get('TEXT_INFO_COUPON_CATEGORIES_HELP') . '<br>' . tep_draw_input_field('restrict_to_categories', $Coupon->restrict_to_categories));
	//$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_CUSTOMERS') . ' - ' . sysLanguage::get('TEXT_INFO_COUPON_CUSTOMERS_HELP') . '<br>' . tep_draw_input_field('restrict_to_customers', $Coupon->restrict_to_customers));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_STARTDATE') . ' - ' . sysLanguage::get('TEXT_INFO_COUPON_STARTDATE_HELP') . '<br>' . tep_draw_input_field('coupon_start_date', $Coupon->coupon_start_date));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_FINISHDATE') . ' - ' . sysLanguage::get('TEXT_INFO_COUPON_FINISHDATE_HELP') . '<br>' . tep_draw_input_field('coupon_expire_date', $Coupon->coupon_expire_date));

	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_NUMBER_DAYS_MEMBERSHIP') . '<br>' . tep_draw_input_field('number_days_membership', $Coupon->number_days_membership));
	
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_NAME') . ' - ' . sysLanguage::get('TEXT_INFO_COUPON_NAME_HELP'));
	foreach(sysLanguage::getLanguages() as $lInfo){
		$infoBox->addContentRow($lInfo['showName']('&nbsp;') . ': ' . tep_draw_input_field('coupon_name[' . $lInfo['id'] . ']', $Coupon->CouponsDescription[$lInfo['id']]->coupon_name));
	}
	
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUPON_DESC') . ' - ' . sysLanguage::get('TEXT_INFO_COUPON_DESC_HELP'));
	foreach(sysLanguage::getLanguages() as $lInfo){
		$infoBox->addContentRow($lInfo['showName']('&nbsp;') . ': ' . tep_draw_textarea_field('coupon_description[' . $lInfo['id'] . ']','physical','24','3', $Coupon->CouponsDescription[$lInfo['id']]->coupon_description));
	}
$table = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->css('width', '100%');
$table->addHeaderRow(array(
	'columns' => array(
		array('attr' => array('width' => '40%'), 'text' => sysLanguage::get('TEXT_PRODUCTS')),
		array('text' => '&nbsp;'),
		array('attr' => array('width' => '40%'), 'text' => sysLanguage::get('TEXT_EXCLUDED_PRODUCTS'))
	)
));
$addonProducts = '';
//print_r($pInfo);
if(!empty($Coupon->products_excluded)){
	$products = explode(',', $Coupon->products_excluded);
}
if(isset($products)){
	foreach($products as $pID){
		$excludedProducts .= '<div><a href="#" class="ui-icon ui-icon-circle-close removeButton"></a><span class="main">' .tep_get_products_name($pID) . '</span>' . tep_draw_hidden_field('products_excluded[]', $pID) . '</div>';
	}
}
$table->addBodyRow(array(
	'columns' => array(
		array(
			'addCls' => 'main',
			'attr' => array(
				'valign' => 'top'
			),
			'text' => '<select multiple="multiple" size="30" style="width:100%;" id="productListAddons">' .  no_category('')  . get_category_tree_list('0',false,true) . '</select>'
		),
		array(
			'addCls' => 'main',
			'text' => '<button type="button" id="moveRightAddon"><span>&nbsp;&nbsp;&raquo;&nbsp;&nbsp;</span></button>'
		),
		array(
			'addCls' => 'main',
			'attr' => array(
				'id' => 'addons',
				'valign' => 'top'
			),
			'text' => $excludedProducts
		)
	)
));
$addonTables = $table->draw();
$infoBox->addContentRow($addonTables);
	
	EventManager::attachActionResponse($infoBox->draw(), 'html');
?>