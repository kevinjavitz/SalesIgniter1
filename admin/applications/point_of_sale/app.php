<?php
	$appName = 'point_of_sale';
	$posInit = true;
	if (isset($_GET['action']) || isset($_POST['action'])) $posInit = false;

	Session::set('currency', (USE_DEFAULT_LANGUAGE_CURRENCY == 'true' ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY));

/* Include all classes required, order DOES matter -- BEGIN -- */
	require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
	require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'product.php');
	require(DIR_FS_CATALOG . DIR_WS_FUNCTIONS . 'crypt.php');
	require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'http_client.php');
	require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'order.php');
	require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'shipping.php');
	require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'payment.php');
	require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'order_total.php');
	require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'shopping_cart-actions.php');
	require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'shopping_cart.php');

	require(DIR_WS_CLASSES . 'point_of_sale/posHtml.php');
/* Include all classes required, order DOES matter -- END -- */

	$currencies = new currencies();

	if (Session::exists('userAccount') === false || $posInit === true){
		$userAccount = new rentalStoreUser;
		$userAccount->loadPlugins();
		Session::set('userAccount', $userAccount);
	}
	$userAccount = &Session::getReference('userAccount');
	$addressBook =& $userAccount->plugins['addressBook'];

	if (Session::exists('shoppingCartBase') === false || $posInit === true){
		Session::set('shoppingCartBase', new shoppingCart_base);
	}
	$shoppingCartBase = &Session::getReference('shoppingCartBase');

	if (Session::exists('pointOfSale') === false || $posInit === true){
		Session::set('pointOfSale', new pointOfSale);
	}
	$pointOfSale = &Session::getReference('pointOfSale');
	$pointOfSale->mode = 'insert';

	$shippingModules = new shipping();
	$paymentModules = new payment();
	$orderTotalModules = new order_total();
	$cart = new shoppingCart();
	$shoppingCartAction = new shoppingCart_actions();

	if ($pointOfSale->mode == 'edit' && !isset($pointOfSale->orderLoaded)){
		$order = new OrderProcessor($oID);
	}else{
		$order = new OrderProcessor;
	}

	$action = (isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : ''));

	if (!empty($action)) {
		$dir = new DirectoryIterator(DIR_FS_ADMIN . DIR_WS_APP . 'point_of_sale/actions/');
		foreach($dir as $fileObj){
			if ($fileObj->isDot()) continue;

			if ($fileObj->getBasename() == $action . '.php'){
				require($fileObj->getPathname());
				itwExit();
			}
		}
	}

	function dialogBlock($id, $headerText, $defaultText = false, $addressBox = false){
		return '<div class="ui-inline-dialog-titlebar ui-widget-header ui-corner-t-l ui-helper-clearfix main" unselectable="on" style="-moz-user-select: none;"><span class="ui-inline-dialog-title main" id="ui-inline-dialog-title-dialog" unselectable="on" style="-moz-user-select: none;">' . $headerText . '</span>' . ($addressBox === true ? '<button id="' . $id . 'Edit" style="float:right;display:none;" type="button" class="ui-state-default ui-corner-all">Edit</button>' : '') . '</div><div id="' . $id . 'Dialog" class="ui-inline-dialog-content ui-widget-content main" style="text-align:left;width: auto;">' . ($defaultText !== false ? '<div class="defaultText">' . $defaultText . '</div>' : '') . '</div>';
	}
	
	$appContent = $appName . '/pages/default.php';
?>