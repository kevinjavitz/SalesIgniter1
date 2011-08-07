<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	error_reporting(E_ALL & ~E_DEPRECATED);

	function onShutdown(){
		global $ExceptionManager;
		// This is our shutdown function, in
		// here we can do any last operations
		// before the script is complete.

		if ($ExceptionManager->size() > 0){
			echo '<br /><div style="width:98%;margin-right:auto;margin-left:auto;">' . $ExceptionManager->output('text') . '</div>';
		}
	}
	register_shutdown_function('onShutdown');

	define('APPLICATION_ENVIRONMENT', (isset($_GET['env']) ? $_GET['env'] : 'catalog'));

	// start the timer for the page parse time log
	define('PAGE_PARSE_START_TIME', microtime());
	define('START_MEMORY_USAGE', memory_get_usage());
	require('includes/classes/system_configuration.php');

	/* TO BE MOVED LATER -- BEGIN -- */
	include('includes/conversionArrays.php');
	define('USER_ADDRESS_BOOK_ENABLED', 'True');
	/* TO BE MOVED LATER -- END -- */

	date_default_timezone_set('America/New_York');

	/*
	 * Load system path/database settings
	 */
	sysConfig::init();

	require(sysConfig::getDirFsCatalog() . 'ext/Doctrine.php');
	spl_autoload_register(array('Doctrine_Core', 'autoload'));
	spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));
	$manager = Doctrine_Manager::getInstance();
	$manager->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true);
	$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
	/*
	 * KNOWN ISSUES
	 1: causes the extension installer to not install doctrine tables
	*/
	$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
	Doctrine_Core::setModelsDirectory(sysConfig::getDirFsCatalog() . 'ext/Doctrine/Models');
	//Doctrine_Core::loadModels(sysConfig::getDirFsCatalog() . 'ext/Doctrine/Models');

	$profiler = new Doctrine_Connection_Profiler();

	$conn = Doctrine_Manager::connection(sysConfig::get('DOCTRINE_CONN_STRING'), 'mainConnection');
	$cacheDriver = new Doctrine_Cache_Apc();
	$conn->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, $cacheDriver);
	$conn->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, $cacheDriver);

	$conn->setListener($profiler);

	/*$cacheConnection = Doctrine_Manager::connection(new PDO('sqlite::memory:'), 'cacheConnection');
	$cacheDriver = new Doctrine_Cache_Db(array(
		'connection' => $conn,
		'tableName'  => 'DoctrineCache'
	));
	$conn->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, $cacheDriver);
	$conn->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, $cacheDriver);
	$conn->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE_LIFESPAN, 3600);*/

	$manager->setCurrentConnection('mainConnection');

	// include the list of project filenames
	require(sysConfig::getDirFsCatalog() . 'includes/filenames.php');

	// include the list of project database tables
	require(sysConfig::getDirFsCatalog() . 'includes/database_tables.php');

	// customization for the design layout
	define('BOX_WIDTH', 195); // how wide the boxes should be in pixels (default: 125)
	define('RATING_UNITWIDTH', 10);
	// include the database functions
	require(sysConfig::getDirFsCatalog() . 'includes/classes/dataAccess.php');
	new dataAccess(); /* Establish Database Connection */

	require(sysConfig::getDirFsCatalog() . 'includes/functions/database.php');

	// set the application parameters
	sysConfig::load();

require(sysConfig::getDirFsCatalog() . 'includes/classes/cache.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/Profiler/Base.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/htmlBase.php');
	require(sysConfig::getDirFsCatalog() . 'includes/classes/exceptionManager.php');
	$ExceptionManager = new ExceptionManager;
	set_error_handler(array($ExceptionManager, 'addError'));
	set_exception_handler(array($ExceptionManager, 'add'));

	// if gzip_compression is enabled, start to buffer the output
	if ( (sysConfig::get('GZIP_COMPRESSION') == 'true') && ($ext_zlib_loaded = extension_loaded('zlib')) && (PHP_VERSION >= '4') ) {
		if (($ini_zlib_output_compression = (int)ini_get('zlib.output_compression')) < 1) {
			if (PHP_VERSION >= '4.0.4') {
				ob_start('ob_gzhandler');
			}
		} else {
			ini_set('zlib.output_compression_level', GZIP_LEVEL);
		}
	}

	require(sysConfig::getDirFsCatalog() . 'includes/classes/eventManager/Manager.php');
	require(sysConfig::getDirFsCatalog() . 'includes/classes/eventManager/Event.php');
	require(sysConfig::getDirFsCatalog() . 'includes/classes/eventManager/EventActionResponse.php');

	require(sysConfig::getDirFsCatalog() . 'includes/classes/application.php');
	$App = new Application((isset($_GET['app']) ? $_GET['app'] : ''), (isset($_GET['appPage']) ? $_GET['appPage'] : ''));

	if ($App->isValid() === false) die('No valid application found.');

	require(sysConfig::getDirFsCatalog() . 'includes/classes/extension.php');
	$appExtension = new Extension;
	$appExtension->preSessionInit();

	// define general functions used application-wide
	require(sysConfig::getDirFsCatalog() . 'includes/functions/general.php');
	require(sysConfig::getDirFsCatalog() . 'includes/functions/crypt.php');
	require(sysConfig::getDirFsCatalog() . 'includes/classes/system_modules_loader.php');
	require(sysConfig::getDirFsCatalog() . 'includes/modules/infoboxes/InfoBoxAbstract.php');
	require(sysConfig::getDirFsCatalog() . 'includes/functions/html_output.php');

	//Email Template Manager Start
	require(sysConfig::getDirFsCatalog() . 'includes/classes/email_events.php');
	//Email Template Manager End

	// include cache functions if enabled
	if (sysConfig::get('USE_CACHE') == 'true') include(sysConfig::getDirFsCatalog() . 'includes/functions/cache.php');

	/*
	* All Classes that will be registered in sessions must go here -- BEGIN
	*/

	require(sysConfig::getDirFsCatalog() . 'includes/classes/user/membership.php');
	require(sysConfig::getDirFsCatalog() . 'includes/classes/user/address_book.php');
	require(sysConfig::getDirFsCatalog() . 'includes/classes/user.php');

	// include shopping cart class
	require(sysConfig::getDirFsCatalog() . 'includes/classes/shopping_cart.php');

	// include rental queue class
	require(sysConfig::getDirFsCatalog() . 'includes/classes/rental_queue-base.php');
	require(sysConfig::getDirFsCatalog() . 'includes/classes/rental_queue.php');

	// include navigation history class
	require(sysConfig::getDirFsCatalog() . 'includes/classes/navigation_history.php');

	// include the product class
	require(sysConfig::getDirFsCatalog() . 'includes/classes/product.php');

	//Include the order class
	//require(sysConfig::getDirFsCatalog() . 'includes/classes/Order/Base.php');

	/*
	* All Classes that will be registered in sessions must go here -- END
	*/

	require(sysConfig::getDirFsCatalog() . 'includes/classes/http_client.php');

	// define how the session functions will be used
	require(sysConfig::getDirFsCatalog() . 'includes/classes/session.php');
	Session::init(); /* Initialize the session */

	// start the session
	$session_started = false;
	if (sysConfig::get('SESSION_FORCE_COOKIE_USE') == 'True') {
		tep_setcookie('cookie_test', 'please_accept_for_session', time()+60*60*24*30, $cookie_path, $cookie_domain);

		if (isset($_COOKIE['cookie_test'])) {
			Session::start();
			$session_started = true;
		}
	} elseif (sysConfig::get('SESSION_BLOCK_SPIDERS') == 'True') {
		$user_agent = strtolower(getenv('HTTP_USER_AGENT'));
		$spider_flag = false;

		if (tep_not_null($user_agent)) {
			$spiders = file(sysConfig::getDirFsCatalog() . 'includes/spiders.txt');

			for ($i=0, $n=sizeof($spiders); $i<$n; $i++) {
				if (tep_not_null($spiders[$i])) {
					if (is_integer(strpos($user_agent, trim($spiders[$i])))) {
						$spider_flag = true;
						break;
					}
				}
			}
		}

		if ($spider_flag == false) {
			Session::start();
			$session_started = true;
		}
	} else {
		Session::start();
		$session_started = true;
	}

	// set SID once, even if empty
	$SID = (defined('SID') ? SID : '');

	// verify the ssl_session_id if the feature is enabled
	if ( ($request_type == 'SSL') && (sysConfig::get('SESSION_CHECK_SSL_SESSION_ID') == 'True') && (sysConfig::get('ENABLE_SSL') == true) && ($session_started == true) ) {
		if (Session::exists('SSL_SESSION_ID') === false){
			Session::set('SSL_SESSION_ID', $_SERVER['SSL_SESSION_ID']);
		}

		if (Session::get('SSL_SESSION_ID') != $_SERVER['SSL_SESSION_ID']) {
			Session::destroy();
			tep_redirect(itw_app_link('appExt=infoPages', 'show_page', 'ssl_check'));
		}
	}

	// verify the browser user agent if the feature is enabled
	if (sysConfig::get('SESSION_CHECK_USER_AGENT') == 'True') {
		$http_user_agent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		if (Session::exists('SESSION_USER_AGENT') === false) {
			Session::set('SESSION_USER_AGENT', $http_user_agent);
		}

		if (Session::get('SESSION_USER_AGENT') != $http_user_agent) {
			Session::destroy();
			tep_redirect(itw_app_link(null, 'account', 'login'));
		}
	}

	// verify the IP address if the feature is enabled
	if (sysConfig::get('SESSION_CHECK_IP_ADDRESS') == 'True') {
		$ip_address = tep_get_ip_address();
		if (Session::exists('SESSION_IP_ADDRESS') === false) {
			Session::set('SESSION_IP_ADDRESS', $ip_address);
		}

		if (Session::get('SESSION_IP_ADDRESS') != $ip_address) {
			Session::destroy();
			tep_redirect(itw_app_link(null, 'account', 'login'));
		}
	}
	$appExtension->postSessionInit();

	$ExceptionManager->initSessionMessages();

	require(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/modules.php');
	require(sysConfig::getDirFsCatalog() . 'includes/modules/orderPaymentModules/modules.php');
	require(sysConfig::getDirFsCatalog() . 'includes/modules/orderTotalModules/modules.php');

	// initialize the message stack for output messages
	require(sysConfig::getDirFsCatalog() . 'includes/classes/message_stack.php');
	$messageStack = new messageStack;

	require(sysConfig::getDirFsCatalog() . 'includes/classes/system_language.php');
	sysLanguage::init();

	// include the breadcrumb class and start the breadcrumb trail
	require(sysConfig::getDirFsCatalog() . 'includes/classes/breadcrumb.php');
	$breadcrumb = new breadcrumb;

	$breadcrumb->add(sysLanguage::get('HEADER_TITLE_CATALOG') .' '. sysLanguage::get('HEADER_LINK_HOME'), itw_app_link(null, 'index', 'default'));

	$appExtension->loadExtensions();

	//Doctrine_Core::initializeModels(Doctrine_Core::getLoadedModels());

	$App->loadLanguageDefines();

	if (Session::exists('userAccount') === false){
		$userAccount = new RentalStoreUser();
		$userAccount->loadPlugins();
		Session::set('userAccount', $userAccount);
	}
	$userAccount = &Session::getReference('userAccount');
	$appExtension->bindMethods($userAccount);

	// create the shopping cart & fix the cart if necesary
	if (Session::exists('ShoppingCart') === false){
		$ShoppingCart = new ShoppingCart;
		Session::set('ShoppingCart', $ShoppingCart);
	}
	$ShoppingCart = &Session::getReference('ShoppingCart');
	$ShoppingCart->initContents();

	// create the rental queue & fix the queue if necesary - added by Deepali
	if (Session::exists('rentalQueueBase') === false){
		$rentalQueueBase = new rentalQueue_base();
		Session::set('rentalQueueBase', $rentalQueueBase);
	}
	$rentalQueueBase = &Session::getReference('rentalQueueBase');

	$rentalQueue = new rentalQueue;

	// include currencies class and create an instance
	require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
	$currencies = new currencies();

	// include the mail classes
	require(sysConfig::getDirFsCatalog() . 'includes/classes/mime.php');
	require(sysConfig::getDirFsCatalog() . 'includes/classes/email.php');

	// Ultimate SEO URLs v2.1
	//include_once(sysConfig::getDirFsCatalog() . 'includes/classes/seo.class.php');
	//$seo_urls = new SEO_URL(Session::get('languages_id'));

	// currency
	if (Session::exists('currency') === false || isset($_GET['currency']) || ( (sysConfig::get('USE_DEFAULT_LANGUAGE_CURRENCY') == 'true') && (LANGUAGE_CURRENCY != Session::get('currency')) ) ) {
		if (isset($_GET['currency'])) {
			if (!$currency = tep_currency_exists($_GET['currency'])) $currency = (sysConfig::get('USE_DEFAULT_LANGUAGE_CURRENCY') == 'true') ? LANGUAGE_CURRENCY : sysConfig::get('DEFAULT_CURRENCY');
		} else {
			$currency = sysConfig::get('DEFAULT_CURRENCY');
		}
		Session::set('currency', $currency);
	}

	// navigation history
	if (Session::exists('navigation') === false){
		Session::set('navigation', new navigationHistory);
	}
	$navigation = &Session::getReference('navigation');
	$navigation->add_current_page();

	EventManager::notify('ApplicationTopBeforeCartAction');

	// Shopping cart actions
	$action = (isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : ''));

	if (!empty($action)) {
	if (isset($_POST['update_product']))       $action = 'update_product';
	if (isset($_POST['add_product']))          $action = 'add_product';
	if (isset($_POST['buy_now']))              $action = 'buy_now';
	if (isset($_POST['buy_new_product']))      $action = 'buy_new_product';
	if (isset($_POST['buy_used_product']))     $action = 'buy_used_product';
	if (isset($_POST['rent_now']))             $action = 'rent_now';
	if (isset($_POST['add_queue']))            $action = 'add_queue';
	if (isset($_POST['add_queue_all']))        $action = 'add_queue_all';
	if (isset($_POST['update_queue']))         $action = 'update_queue';
	if (isset($_POST['cust_order']))           $action = 'cust_order';
	if (isset($_POST['checkout']))             $action = 'update_product';

	EventManager::notify('ApplicationTopActionCheckPost', &$action);

		// redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled
		if ($session_started == false) {
			tep_redirect(itw_app_link('appExt=infoPages', 'show_page', 'cookie_usage'));
		}

		if (isset($_POST['checkout'])){
			$parameters = array('action', 'cPath', 'products_id', 'pid');
			$goto = itw_app_link(tep_get_all_get_params($parameters), 'checkout', 'default', 'SSL');
		}elseif (sysConfig::get('DISPLAY_CART') == 'true') {
			$parameters = array('app', 'appPage', 'action', 'cPath', 'products_id', 'pid');
			$goto = itw_app_link(tep_get_all_get_params($parameters), 'shoppingCart', 'default');
			$gotologin =  FILENAME_LOGIN;
		} else {
			$parameters = array('action', 'pid', 'products_id');
			if (isset($_GET['seoTag'])){
				$goto = itw_app_link('products_id=' . $_GET['products_id'], 'product', 'info');
			}else{
				//if (isset($_GET['app'])){
					//tep_redirect(itw_app_link(tep_get_all_get_params($parameters)));
				//}else{
					$goto = itw_app_link(tep_get_all_get_params($parameters));
				//}
			}
		}

		if ($action == 'add_queue' || $action == 'rent_now'){
			$QboxCheck = Doctrine_Query::create()
			->select('products_id')
			->from('ProductsToBox')
			->where('box_id = ?', (int)$_REQUEST['products_id'])
			->execute();
			if ($QboxCheck->count() > 0){
				$_REQUEST['action'] = 'add_queue_all';
			}
			$QboxCheck->free();
			unset($QboxCheck);
		}

		$productsId = (isset($_POST['products_id']) ? $_POST['products_id'] : (isset($_GET['products_id']) ? $_GET['products_id'] : null));
		switch($action){
			// customer wants to update the product quantity in their shopping cart
			case 'update_product' :
				$removed = array();
				if(isset($_POST['cart_delete']) && is_array($_POST['cart_delete'])){
					foreach($_POST['cart_delete'] as $item => $purchaseTypeVal){
	                	$ShoppingCart->removeProduct($item, $purchaseTypeVal);
						$removed[] = $item;
					}
				}

				if(isset($_POST['cart_quantity']) && is_array($_POST['cart_quantity'])){
					foreach($_POST['cart_quantity'] as $item => $val){
						if(!in_array($item, $removed)){
							foreach($val as $purchaseTypeVal => $qtyVal){
								$ShoppingCart->updateProduct($item, array(
										'purchase_type' => $purchaseTypeVal,
										'quantity'      => $qtyVal
								));
							}
						}
					}
				}
				tep_redirect($goto);
				break;
			case 'removeCartProduct':
				$ShoppingCart->removeProduct($_GET['pID']);
				if (isset($_GET['app'])){
					tep_redirect(itw_app_link(tep_get_all_get_params(array('action', 'pID'))));
				}else{
					tep_redirect(itw_app_link(tep_get_all_get_params(array('action', 'pid', 'products_id'))));
				}
				break;
			case 'add_product' :
				$ShoppingCart->addProduct($productsId, 'new', 1);
				tep_redirect($goto);
				break;
			case 'buy_now' :
				$ShoppingCart->addProduct($productsId, 'new', 1);
				tep_redirect($goto);
				break;
			case 'buy_new_product':
				$qty = (isset($_POST['quantity']['new']) ? $_POST['quantity']['new'] : 1);
				$ShoppingCart->addProduct($productsId, 'new', $qty);
				tep_redirect(itw_app_link(null, 'shoppingCart', 'default'));
				break;
			case 'buy_used_product':
				$qty = (isset($_POST['quantity']['used']) ? $_POST['quantity']['used'] : 1);
				$ShoppingCart->addProduct($productsId, 'used', $qty);
				tep_redirect(itw_app_link(null, 'shoppingCart', 'default'));
				break;
			case 'rent_now':
			case 'add_queue':
				$pID = $productsId;
				$attribs = array();
				if (isset($_GET['id']) && isset($_GET['id']['rental'])){
					$attribs = $_GET['id']['rental'];
				}elseif (isset($_POST['id']) && isset($_POST['id']['rental'])){
					$attribs = $_POST['id']['rental'];
				}
				if ($userAccount->isLoggedIn() === true){
					if ($pID === false){
						$messageStack->addSession('pageStack', 'Error: No Product Id Found', 'warning');
						tep_redirect(itw_app_link(tep_get_all_get_params(array('action')), 'product', 'info'));
					}

					$customerCanRent = $rentalQueue->rentalAllowed($userAccount->getCustomerId());
					if ($customerCanRent !== true){
						switch($customerCanRent){
							case 'membership':
								if (Session::exists('account_action') === true){
									Session::remove('account_action');
								}

								$errorMsg = sprintf(sysLanguage::get('TEXT_NOT_RENTAL_CUSTOMER'),itw_app_link('checkoutType=rental','checkout','default','SSL'), itw_app_link(null,'account','login'));
								break;
							case 'inactive':
								$errorMsg = sprintf(sysLanguage::get('TEXT_NOT_ACTIVE_CUSTOMER'), itw_app_link('checkoutType=rental','checkout','default','SSL'));
								break;
						}
						$messageStack->addSession('pageStack', $errorMsg, 'warning');
						tep_redirect(itw_app_link(tep_get_all_get_params(array('action')), 'product', 'info'));
					}

					$rentalQueue->addToQueue($pID, $attribs);
					tep_redirect( itw_app_link(tep_get_all_get_params($parameters),'rentals','queue'));
				}else{
					Session::set('add_to_queue_product_id', $productsId);
					Session::set('add_to_queue_product_attrib', $attribs);
					//$navigation->set_snapshot();
					$messageStack->addSession('pageStack',sysLanguage::get('TO_ADD_TO_QUEUE_MESSAGE'),'warning');
					tep_redirect(itw_app_link('checkoutType=rental','checkout','default','SSL'));
				}
				break;
			case 'add_queue_all':
				if ($userAccount->isLoggedIn() === true){
					$customerCanRent = $rentalQueue->rentalAllowed($customer_id);
					if ($customerCanRent === true){
						$pID = false;
						if (isset($_GET['products_id'])){
							$pID = $_GET['products_id'];
						}elseif(isset($_POST['products_id'])){
							$pID = $_POST['products_id'];
						}

						if ($pID === false){
							$messageStack->addSession('pageStack', 'Error: No Product Id Found', 'warning');
							tep_redirect(itw_app_link(tep_get_all_get_params(array('action')), 'product', 'info'));
						}

						$rentalQueue->addBoxSet((int)$pID);
						tep_redirect( itw_app_link(tep_get_all_get_params($parameters),'rentals','queue'));
					}else{
						switch($customerCanRent){
							case 'membership':
								if (Session::exists('account_action') === true){
									Session::remove('account_action');
								}

								$errorMsg = sprintf(sysLanguage::get('TEXT_NOT_RENTAL_CUSTOMER'),itw_app_link('checkoutType=rental','checkout','default','SSL'), itw_app_link(null,'account','login'));
								break;
							case 'inactive':
								$errorMsg = sprintf(sysLanguage::get('TEXT_NOT_ACTIVE_CUSTOMER'), itw_app_link('checkoutType=rental','checkout','default','SSL'));
								break;
						}
						$messageStack->addSession('pageStack', $errorMsg, 'warning');

						tep_redirect(itw_app_link(tep_get_all_get_params(array('action')), 'product', 'info'));
					}
				}else{
					$navigation->set_snapshot();
					$messageStack->addSession('pageStack',sysLanguage::get('TO_ADD_TO_QUEUE_MESSAGE'),'warning');
					tep_redirect(itw_app_link(tep_get_all_get_params($parameters),'account','login') . '#tabNewRentAccount');
				}
				break;
			case 'update_queue':
				for ($i=0, $n=sizeof($productsId); $i<$n; $i++) {
					if (in_array($productsId[$i], (isset($_REQUEST['queue_delete']) && is_array($_REQUEST['queue_delete']) ? $_REQUEST['queue_delete'] : array()))) {
						$rentalQueueBase->removeFromQueue($productsId[$i]);
					} else {
						if ($_REQUEST['queue_priority'][$i] == "") $_REQUEST['queue_priority'][$i] = 999;
						$rentalQueue->updatePriority($productsId[$i], $_REQUEST['queue_priority'][$i], $_REQUEST['queue_previous_priority'][$i]);
					}
				}
				$rentalQueueBase->fixPriorities();
				tep_redirect( itw_app_link(tep_get_all_get_params($parameters),'rentals','queue'));
				break;
			case 'rateProduct':
				if ($userAccount->isLoggedIn() && isset($_GET['pID'])){
					$Ratings = Doctrine_Core::getTable('Ratings');
					$Rating = $Ratings->findOneByProductsIdAndCustomersId($_GET['pID'], $userAccount->getCustomerId());
					if (!$Rating){
						$Rating = $Ratings->create();
						$Rating->products_id = $_GET['pID'];
						$Rating->customers_id = $userAccount->getCustomerId();
					}
					$Rating->reviews_rating = number_format((float)$_GET['rating'], 1);
					$Rating->save();

					echo '{ success: true }';
				}else{
					echo '{ success: false }';
				}
				itwExit();
				break;
		}

		EventManager::notify('ApplicationTopAction_' . $action);
	}


	// include the who's online functions
	require(sysConfig::getDirFsCatalog() . 'includes/functions/whos_online.php');
	tep_update_whos_online();

	// include the password crypto functions
	require(sysConfig::getDirFsCatalog() . 'includes/functions/password_funcs.php');

	// include validation functions (right now only email address)
	require(sysConfig::getDirFsCatalog() . 'includes/functions/validations.php');

	// split-page-results
	require(sysConfig::getDirFsCatalog() . 'includes/classes/split_page_results.php');

	// infobox
	require(sysConfig::getDirFsCatalog() . 'includes/classes/boxes.php');

	// calculate category path
	if (isset($_GET['cPath'])) {
		$cPath = $_GET['cPath'];
		$cPathValid = true;
		$check = explode('_', $cPath);
		foreach($check as $catId){
			if (!is_numeric($catId)){
				$cPathValid = false;
				break;
			}
		}
		if ($cPathValid === false){
			$cPath = '';
			unset($_GET['cPath']);
		}
	} elseif (isset($_GET['products_id']) && !isset($_GET['manufacturers_id'])) {
		$cPath = tep_get_product_path($_GET['products_id']);
	} else {
		$cPath = '';
	}

	// calculate funways category path
	if (isset($_GET['fcPath'])) {
		$fcPath = $_GET['fcPath'];
		/* } elseif (isset($_GET['products_id']) && !isset($_GET['manufacturers_id'])) {
		$fcPath = tep_get_product_path($_GET['products_id']);*/
	} else {
		$fcPath = 0;
	}

	if (tep_not_null($cPath)) {
		$cPath_array = tep_parse_category_path($cPath);
		$cPath = implode('_', $cPath_array);
		$current_category_id = $cPath_array[(sizeof($cPath_array)-1)];
	} else {
		$current_category_id = 0;
	}

	if (tep_not_null($fcPath)) {
		$fcPath_array = tep_parse_funways_category_path($fcPath);
		$fcPath = implode('_', $fcPath_array);
		$current_fcategory_id = $fcPath_array[(sizeof($fcPath_array)-1)];
	} else {
		$current_fcategory_id = 0;
	}

	// add category names or the manufacturer name to the breadcrumb trail
	if (isset($cPath_array)) {
		for ($i=0, $n=sizeof($cPath_array); $i<$n; $i++) {
			$Qcategory = mysql_query('select categories_name from categories_description where categories_id = "' . (int) $cPath_array[$i] . '" and language_id = "' . Session::get('languages_id') . '"');
			if (mysql_num_rows($Qcategory)){
				$Category = mysql_fetch_assoc($Qcategory);
				$breadcrumb->add($Category['categories_name'], itw_app_link('cPath=' . implode('_', array_slice($cPath_array, 0, ($i+1))), 'index', 'default'));
			} else {
				break;
			}
		}
	} elseif (isset($_GET['manufacturers_id'])) {
		$Qmanufacturer = mysql_query('select manufacturers_name from manufacturers where manufacturers_id = "' . (int) $_GET['manufacturers_id'] . '"');
		if (mysql_num_rows($Qmanufacturer)){
			$Manufacturer = mysql_fetch_assoc($Qmanufacturer);
			$breadcrumb->add($Manufacturer['manufacturers_name'], itw_app_link('manufacturers_id=' . (int) $_GET['manufacturers_id'], 'index', 'default'));
		}
	}

	// add the products model to the breadcrumb trail
	if (isset($_GET['products_id'])) {
		$Qproduct = mysql_query('select products_name from products_description where products_id = "' . (int)$_GET['products_id'] . '" and language_id = "' . Session::get('languages_id') . '"');
		if (mysql_num_rows($Qproduct)){
			$Product = mysql_fetch_assoc($Qproduct);
			$breadcrumb->add($Product['products_name'], itw_app_link('products_id=' . (int)$_GET['products_id'], 'product', 'info'));
		}
	}

	// set which precautions should be checked
	define('WARN_INSTALL_EXISTENCE', 'true');
	define('WARN_CONFIG_WRITEABLE', 'true');
	define('WARN_SESSION_DIRECTORY_NOT_WRITEABLE', 'true');
	define('WARN_SESSION_AUTO_START', 'true');
	define('WARN_DOWNLOAD_DIRECTORY_NOT_READABLE', 'true');

	// BOF: WebMakers.com Added: Header Tags Controller
	require(sysConfig::getDirFsCatalog() . 'includes/functions/header_tags.php');
	// Clean out HTML comments from ALT tags etc.
	require(sysConfig::getDirFsCatalog() . 'includes/functions/clean_html_comments.php');
	// EOF: WebMakers.com Added: Header Tags Controller
	require(sysConfig::getDirFsCatalog() . 'includes/add_ccgvdc_application_top.php');  // ICW CREDIT CLASS Gift Voucher Addittion

	// BOF BTS
	//require(sysConfig::getDirFsCatalog() . 'includes/configure_bts.php');
	// EOF BTS
	include(sysConfig::getDirFsCatalog() . 'includes/functions/drawrating.php');

	class PagerLayoutWithArrows extends Doctrine_Pager_Layout {
		private $myType = '';

		public function setMyType($val){
			$this->myType = $val;
		}

		public function getMyType(){
			return $this->myType;
		}


		public function display($options = array(), $return = false){
			if(empty($this->myType)){
				$this->myType = sysLanguage::get('TEXT_PAGER_TYPE');
			}
			$pager = $this->getPager();
			$str = '';

			// First page
			$this->addMaskReplacement('page', '&laquo;', true);
			$options['page_number'] = $pager->getFirstPage();
			$str .= $this->processPage($options);

			// Previous page
			$this->addMaskReplacement('page', '&lsaquo;', true);
			$options['page_number'] = $pager->getPreviousPage();
			$str .= $this->processPage($options);

			// Pages listing
			$this->removeMaskReplacement('page');
			$str .= parent::display($options, true);

			// Next page
			$this->addMaskReplacement('page', '&rsaquo;', true);
			$options['page_number'] = $pager->getNextPage();
			$str .= $this->processPage($options);

			// Last page
			$this->addMaskReplacement('page', '&raquo;', true);
			$options['page_number'] = $pager->getLastPage();
			$str .= $this->processPage($options);

			$str .= '&nbsp;&nbsp;<b>' . $pager->getFirstIndice() . ' - ' . $pager->getLastIndice() . ' ('.sysLanguage::get('TEXT_PAGER_OF').' ' . $pager->getNumResults() . ' ' . $this->myType. ')</b>';
			// Possible wish to return value instead of print it on screen
			if ($return) {
				return $str;
			}
			echo $str;
		}
	}
	require(sysConfig::getDirFsCatalog() . 'includes/classes/products.php');
	$storeProducts = new storeProducts();

	require(sysConfig::getDirFsCatalog() . 'includes/classes/productListing_row.php');
	require(sysConfig::getDirFsCatalog() . 'includes/classes/productListing_col.php');
	require(sysConfig::getDirFsCatalog() . 'includes/classes/productListing_date.php');
?>