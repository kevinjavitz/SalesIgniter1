<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

// start profiling
if (isset($_GET['runProfile'])){
	xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
}

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

	require('includes/classes/Profiler/Base.php');
	require('includes/classes/ConfigReader/Base.php');
	require('includes/classes/MainConfigReader.php');
	require('includes/classes/ExtensionConfigReader.php');
	require('includes/classes/system_configuration.php');
	include('includes/conversionArrays.php');

	date_default_timezone_set('America/New_York');
	sysConfig::init();

	require(sysConfig::getDirFsCatalog() . 'ext/Doctrine.php');
	spl_autoload_register(array('Doctrine_Core', 'autoload'));
	spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));
	$manager = Doctrine_Manager::getInstance();
	$manager->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true);
	$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
	$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
	Doctrine_Core::setModelsDirectory(sysConfig::getDirFsCatalog() . 'ext/Doctrine/Models');

	//Doctrine_Core::loadModels(sysConfig::getDirFsCatalog() . 'ext/Doctrine/Models');

	//$profiler = new Doctrine_Connection_Profiler();

	$conn = Doctrine_Manager::connection(sysConfig::get('DOCTRINE_CONN_STRING'), 'mainConnection');
	/*$cacheDriver = new Doctrine_Cache_Apc();
	$conn->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, $cacheDriver);
	$conn->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, $cacheDriver);*/

	//$conn->setListener($profiler);

	/*$cacheConnection = Doctrine_Manager::connection(new PDO('sqlite::memory:'), 'cacheConnection');
	$cacheDriver = new Doctrine_Cache_Db(array(
		'connection' => $conn,
		'tableName'  => 'DoctrineCache'
	));
	$conn->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, $cacheDriver);
	$conn->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, $cacheDriver);
	$conn->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE_LIFESPAN, 3600);*/

	$conn->setCharset(sysConfig::get('SYSTEM_CHARACTER_SET'));
	$conn->setCollate(sysConfig::get('SYSTEM_CHARACTER_SET_COLLATION'));
	$manager->setCurrentConnection('mainConnection');
	Doctrine_Manager::connection()->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true );

	//require(sysConfig::getDirFsCatalog() . 'includes/functions/database.php'); //to be removed


	// set the application parameters
	sysConfig::load();

	if(isset($_GET['lID'])){

		$QApps = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from template_pages where FIND_IN_SET("'.$_GET['lID'].'",layout_id)');
		if(sizeof($QApps) == 1){
			$myApps = $QApps[0];
			$_GET['app'] = $myApps['application'];
			if($_GET['app'] == 'product'){
				$_GET['appPage'] = $myApps['page'];
			}else{
				$_GET['appPage'] = str_replace('.php','',$myApps['page']);
			}
			if(!empty($myApps['extension'])){
				$_GET['appExt'] = $myApps['extension'];
			}
		}
	}

require(sysConfig::getDirFsCatalog() . 'includes/classes/MultipleInheritance.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/Importable/Installable.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/Importable/SortedDisplay.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/cache.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/htmlBase.php');

	require(sysConfig::getDirFsCatalog() . 'includes/classes/exceptionManager.php');
	$ExceptionManager = new ExceptionManager;
	set_error_handler(array($ExceptionManager, 'addError'));
	set_exception_handler(array($ExceptionManager, 'add'));

	require(sysConfig::getDirFsCatalog() . 'includes/classes/eventManager/Manager.php');

// define general functions used application-wide
require(sysConfig::getDirFsCatalog() . 'includes/functions/general.php');
require(sysConfig::getDirFsCatalog() . 'includes/functions/crypt.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/system_modules_loader.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/ModuleBase.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/ModuleConfigReader.php');

require(sysConfig::getDirFsCatalog() . 'includes/functions/html_output.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/boxes.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/email_events.php');

require(sysConfig::getDirFsCatalog() . 'includes/classes/user/membership.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/user/address_book.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/user.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/shopping_cart.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/rental_queue-base.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/rental_queue.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/navigation_history.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/product.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/http_client.php');


require(sysConfig::getDirFsCatalog() . 'includes/classes/application.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/extension.php');

$App = new Application((isset($_GET['app']) ? $_GET['app'] : ''), (isset($_GET['appPage']) ? $_GET['appPage'] : ''));
$appExtension = new Extension;
$appExtension->preSessionInit();

require(sysConfig::getDirFsCatalog() . 'includes/classes/session.php');
Session::init(); /* Initialize the session */

require(sysConfig::getDirFsCatalog() . 'includes/classes/message_stack.php');
$messageStack = new messageStack;

require(sysConfig::getDirFsCatalog() . 'includes/classes/system_language.php');
sysLanguage::init();
//load language
if(Session::exists('tplDir') && file_exists(sysConfig::getDirFsCatalog() .'templates/'.Session::get('tplDir') . '/language_defines/global.xml')){
	sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() .'templates/'.Session::get('tplDir') . '/language_defines/global.xml');
}

	
$appExtension->postSessionInit();
$appExtension->loadExtensions();

$ExceptionManager->initSessionMessages();
require(sysConfig::getDirFsCatalog() . 'includes/modules/pdfinfoboxes/PDFInfoBoxAbstract.php');
require(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/modules.php');
require(sysConfig::getDirFsCatalog() . 'includes/modules/orderPaymentModules/modules.php');
require(sysConfig::getDirFsCatalog() . 'includes/modules/orderTotalModules/modules.php');


require(sysConfig::getDirFsCatalog() . 'includes/classes/breadcrumb.php');
$breadcrumb = new breadcrumb;
$breadcrumb->add(sysLanguage::get('HEADER_TITLE_CATALOG') .' '. sysLanguage::get('HEADER_LINK_HOME'), itw_app_link(null, 'index', 'default'));



	/*This position might not be the best since it might throw some errors on update.*/
	if(sysConfig::get('SITE_MAINTENANCE_MODE') == 'true' && $App->getEnv() == 'catalog'){
		$ipList = explode(';', sysConfig::get('IP_LIST_MAINTENANCE_ENABLED'));
		if(!in_array($_SERVER['REMOTE_ADDR'], $ipList)){
			$infoPages = $appExtension->getExtension('infoPages');
			if ($infoPages !== false && $infoPages->isEnabled() === true){
				$maintenancePage = $infoPages->getInfoPage('maintenance_page');
				echo $maintenancePage['PagesDescription'][Session::get('languages_id')]['pages_html_text'];
			}
			die();
		}
	}

	if (Session::exists('userAccount') === false){
		$userAccount = new RentalStoreUser();
		$userAccount->loadPlugins();
		Session::set('userAccount', $userAccount);
	}
	$userAccount = &Session::getReference('userAccount');
	$appExtension->bindMethods($userAccount);

	// create the shopping cart & fix the cart if necesary
	if (Session::exists('ShoppingCart') === false){
		$ShoppingCart = new ShoppingCart();
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


	require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
	$currencies = new currencies();
	require(sysConfig::getDirFsCatalog() . 'includes/classes/mime.php');
	require(sysConfig::getDirFsCatalog() . 'includes/classes/email.php');

	// navigation history
	if (Session::exists('navigation') === false){
		$navigation = new navigationHistory();
		Session::set('navigation', $navigation);
	}
	$navigation = &Session::getReference('navigation');



	if ($App->isValid() === false){
		die('No valid application found.');
	}
	$appExtension->initApplicationPlugins();
	$App->loadLanguageDefines();


	EventManager::notify('ApplicationTopBeforeCartAction');

	// Shopping cart actions
	$action = (isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : ''));

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
	if (!empty($action)) {
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
			$gotologin =  itw_app_link(null,'account','login');
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
										'quantity'      => $qtyVal['quantity'],
										'postVal'      => $qtyVal
								));
							}
						}
					}
				}
				tep_redirect($goto);
				break;
			case 'removeCartProduct':
				$ShoppingCart->removeProduct($_GET['pID'], $_GET['purchaseTypeVal']);
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
					$membership =& $userAccount->plugins['membership'];
					if ($customerCanRent !== true){
						switch($customerCanRent){
							case 'membership':
								if (Session::exists('account_action') === true){
									Session::remove('account_action');
								}

								$errorMsg = sprintf(sysLanguage::get('TEXT_NOT_RENTAL_CUSTOMER'),itw_app_link('checkoutType=rental','checkout','default','SSL'), itw_app_link(null,'account','login'));
								break;
							case 'inactive':
								$errorMsg = sprintf(sysLanguage::get('TEXT_NOT_ACTIVE_CUSTOMER'), ($membership->isPastDue()?itw_app_link((isset($membership)?'edit='.$membership->getRentalAddressId():''),'account','billing_address_book','SSL'):itw_app_link('checkoutType=rental','checkout','default','SSL')));
								break;
							case 'pastdue':
								$errorMsg = sprintf(sysLanguage::get('RENTAL_CUSTOMER_IS_PAST_DUE'), itw_app_link((isset($membership)?'edit='.$membership->getRentalAddressId():''),'account','billing_address_book','SSL'));//
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
						$membership =& $userAccount->plugins['membership'];

						switch($customerCanRent){
							case 'membership':
								if (Session::exists('account_action') === true){
									Session::remove('account_action');
								}

								$errorMsg = sprintf(sysLanguage::get('TEXT_NOT_RENTAL_CUSTOMER'),itw_app_link('checkoutType=rental','checkout','default','SSL'), itw_app_link(null,'account','login'));
								break;
							case 'inactive':
								$errorMsg = sprintf(sysLanguage::get('TEXT_NOT_ACTIVE_CUSTOMER'), ($membership->isPastDue()?itw_app_link((isset($membership)?'edit='.$membership->getRentalAddressId():''),'account','billing_address_book','SSL'):itw_app_link('checkoutType=rental','checkout','default','SSL')));
								break;
							case 'pastdue':
								$errorMsg = sprintf(sysLanguage::get('RENTAL_CUSTOMER_IS_PAST_DUE'), itw_app_link((isset($membership)?'edit='.$membership->getRentalAddressId():''),'account','billing_address_book','SSL'));//
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

Session::set('current_category_id', '-1');
//Session::remove('current_app_page');
tep_update_whos_online();

	// add the products model to the breadcrumb trail
	if (isset($_GET['products_id'])){
		$Product = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select products_name from products_description where products_id = "' . (int)$_GET['products_id'] . '" and language_id = "' . Session::get('languages_id') . '"');
		if (sizeof($Product) > 0){
			$breadcrumb->add($Product[0]['products_name'], itw_app_link('products_id=' . (int)$_GET['products_id'], 'product', 'info'));
		}
	}

	// set which precautions should be checked
	define('WARN_INSTALL_EXISTENCE', 'true');
	define('WARN_CONFIG_WRITEABLE', 'true');
	define('WARN_SESSION_DIRECTORY_NOT_WRITEABLE', 'true');
	define('WARN_SESSION_AUTO_START', 'true');
	define('WARN_DOWNLOAD_DIRECTORY_NOT_READABLE', 'true');


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