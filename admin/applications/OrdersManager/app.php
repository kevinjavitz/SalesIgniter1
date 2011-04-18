<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of app
 *
 * @author Stephen
 */

	namespace Applications\OrdersManager;
	use Pages;

	use \Application;
	use \currencies;
	use \HttpClient;
	use \Crypt;
	use \DataPopulate\Export;
	use \sysConfig;
	use \sysLanguage;
	use \Session;
	use \Doctrine_Query;
	use \Doctrine_Core;
	use \rentalStoreUser;
	use \Order;

	class Bootstrap extends Application {

		public function __construct(){
			require(sysConfig::getDirFsCatalog() . 'includes/classes/product.php');
			require(sysConfig::getDirFsAdmin() . 'includes/classes/data_populate/export.php');
			require(sysConfig::getDirFsCatalog() . 'includes/functions/crypt.php');
			require(sysConfig::getDirFsCatalog() . 'includes/classes/http_client.php');
			require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
		}

		public function output(){
			require(sysConfig::getDirFsAdmin() . 'applications/OrdersManager/Pages/' . $this->getAppPage() . '.php');

			$className = ucfirst($this->getAppPage());
			$PageClass = new $className;
			$PageClass->init();

			return $PageClass->output();
		}
	}

	$appContent = 'OrdersManager/Pages/Listing.php';
	$AppBoot = new Bootstrap();
?>