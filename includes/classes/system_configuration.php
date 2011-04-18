<?php
/*
 * Sales Igniter E-Commerce System
 * 
 * I.T. Web Experts
 * http://www.itwebexperts.com
 * 
 * Copyright (c) 2010 I.T. Web Experts
 *
 * This script and it's source is not redistributable
*/

/**
 * Holds all configuration settings from the configuration table
 */
	class sysConfig {
		/*
		 * @var array	Holder for all configuration settings
		 */
		private static $config = array();

		/*
		 * @var array	Holder for all protected keys, used to check if a key is protected
		 */
		private static $protectedKeys = array();

		/*
		 * @var array	Used to hold class references
		 */
		private static $classInstances = array();

		/*
		 * @var array	Used to store already exploded keys to prevent doing it more than once
		 */
		private static $exploded = array();
		
		/*
		 * @TODO: Gotta have php 5.3+ for this to work...
		 */
		public static function __callStatic($function, $args){
			if (strstr($function, 'DirFs') || strstr($function, 'DirWs')){
				$define = '';
				for($i=0, $n=sizeof($function); $i<$n; $i++){
					if (isset($lastLetter) && ctype_upper($function[$i])){
						$define .= '_' . $function[$i];
					}else{
						$define .= $function[$i];
					}
					$lastLetter = $function[$i];
				}
				return self::get(strtoupper($define));
			}
		}

		public static function init(){
			global $request_type;
			self::$config = array();

			$dirName = substr(dirname(__FILE__), 0, -7);

			$xmlData = simplexml_load_file(
				$dirName . 'configure.xml',
				'SimpleXMLElement',
				LIBXML_NOCDATA
			);

			foreach($xmlData->config as $cInfo){
				self::set(
					(string) $cInfo->key,
					(string) $cInfo->value,
					(isset($cInfo['protected']) && (string) $cInfo['protected'] == 'true')
				);
			}

			$httpDomainName = self::get('HTTP_DOMAIN_NAME');
			if (substr($_SERVER['HTTP_HOST'], 0, 4) == 'www.' && substr($httpDomainName, 0, 4) != 'www.'){
				$httpDomainName = 'www.' . $httpDomainName;
			}elseif (substr($_SERVER['HTTP_HOST'], 0, 4) != 'www.' && substr($httpDomainName, 0, 4) == 'www.'){
				$httpDomainName = substr($httpDomainName, 4);
			}
			
			$httpsDomainName = self::get('HTTPS_DOMAIN_NAME');
			if (substr($_SERVER['HTTP_HOST'], 0, 4) == 'www.' && substr($httpsDomainName, 0, 4) != 'www.'){
				$httpsDomainName = 'www.' . $httpsDomainName;
			}elseif (substr($_SERVER['HTTP_HOST'], 0, 4) != 'www.' && substr($httpsDomainName, 0, 4) == 'www.'){
				$httpsDomainName = substr($httpsDomainName, 4);
			}
			
			self::setMultiple(array(
				'HTTP_SERVER' => 'http://' . $httpDomainName,
				'HTTP_CATALOG_SERVER' => 'http://' . $httpDomainName,
				'HTTPS_SERVER' => 'https://' . $httpsDomainName,
				'HTTPS_CATALOG_SERVER' => 'https://' . $httpsDomainName
			), false);
			
			self::setMultiple(array(
				'HTTP_COOKIE_PATH' => self::get('DIR_WS_CATALOG'),
				'HTTPS_COOKIE_PATH' => self::get('DIR_WS_CATALOG'),
				'HTTP_COOKIE_DOMAIN' => $httpDomainName,
				'HTTPS_COOKIE_DOMAIN' => $httpsDomainName
			), false);

			self::setMultiple(array(
				'DIR_WS_ADMIN' => self::get('DIR_WS_CATALOG') . 'admin/',
				'DIR_FS_ADMIN' => self::get('DIR_FS_DOCUMENT_ROOT') . self::get('DIR_WS_CATALOG') . 'admin/',
				'DIR_WS_HTTP_CATALOG'  => self::get('DIR_WS_CATALOG'),
				'DIR_WS_HTTPS_CATALOG' => self::get('DIR_WS_CATALOG'),
				'DIR_FS_CATALOG' => self::get('DIR_FS_DOCUMENT_ROOT') . self::get('DIR_WS_CATALOG'),
				'DIR_WS_IMAGES' => 'images/',
				'DIR_WS_INCLUDES' => 'includes/',
				'DIR_WS_APP' => 'applications/',
				'DIR_WS_ADMIN_TEMPLATES' => 'template/'
			), true);

			self::setMultiple(array(
				'DIR_WS_ICONS' => self::get('DIR_WS_IMAGES') . 'icons/',
				'DIR_WS_CATALOG_IMAGES' => self::get('DIR_WS_CATALOG') . 'images/',
				'DIR_WS_BOXES' => self::get('DIR_WS_INCLUDES') . 'boxes/',
				'DIR_WS_FUNCTIONS' => self::get('DIR_WS_INCLUDES') . 'functions/',
				'DIR_WS_CLASSES' => self::get('DIR_WS_INCLUDES') . 'classes/',
				'DIR_WS_MODULES' => self::get('DIR_WS_INCLUDES') . 'modules/',
				'DIR_WS_LANGUAGES' => self::get('DIR_WS_INCLUDES') . 'languages/',
				'DIR_WS_CATALOG_LANGUAGES' => self::get('DIR_WS_CATALOG') . 'includes/languages/',
				'DIR_FS_CATALOG_LANGUAGES' => self::get('DIR_FS_CATALOG') . 'includes/languages/',
				'DIR_FS_CATALOG_IMAGES' => self::get('DIR_FS_CATALOG') . 'images/',
				'DIR_FS_CATALOG_MODULES' => self::get('DIR_FS_CATALOG') . 'includes/modules/',
				'DIR_FS_BACKUP' => self::get('DIR_FS_ADMIN') . 'backups/',
				'ADMIN_TEMPLATE_NAME' => 'fallback/',
				'TEMPLATE_MAIN_PAGE' => 'main_page.tpl.php'
			), true);

			//Deprecated
			self::setMultiple(array(
				'USE_PCONNECT' => 'false',
				'STORE_SESSIONS' => 'mysql'
			), true);

			//Deprecated
			self::setMultiple(array(
				'INVENTORY_CENTERS_ENABLED' => 'True'
			), true);

			self::set('DOCTRINE_CONN_STRING', 'mysql://' . self::get('DB_SERVER_USERNAME') . ':' . self::get('DB_SERVER_PASSWORD') . '@' . self::get('DB_SERVER') . '/' . self::get('DB_DATABASE'));
			self::set('REQUEST_TYPE', (getenv('HTTPS') == 'on' ? 'SSL' : 'NONSSL'));
			self::set('PRODUCT_LISTING_HIDE_NO_INVENTORY', 'False');
			
			$request_type = (getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';

			if ($request_type == 'NONSSL') {
				self::set('DIR_WS_CATALOG', self::get('DIR_WS_HTTP_CATALOG'));
			}else{
				self::set('DIR_WS_CATALOG', self::get('DIR_WS_HTTPS_CATALOG'));
			}
		}

		/**
		 * getDirWsCatalog
		 * returns the correct relative catalog path based on the request type
		 *
		 * @param string $forceType [optional] Used to force the request type, possible values ( SSL or NONSSL )
		 * @return string
		 */
		public static function getDirWsCatalog($forceType = false){
			if ($forceType == 'NONSSL' || ($forceType === false && getenv('HTTPS') != 'on')){
				if (self::exists('DIR_WS_HTTP_CATALOG') === true){
					$returnDir = self::get('DIR_WS_HTTP_CATALOG');
				}else{
					$returnDir = self::get('DIR_WS_CATALOG');
				}
			}elseif ($forceType == 'SSL' || ($forceType === false && getenv('HTTPS') == 'on')){
				if (self::exists('DIR_WS_HTTPS_CATALOG') === true){
					$returnDir = self::get('DIR_WS_HTTPS_CATALOG');
				}else{
					$returnDir = self::get('DIR_WS_CATALOG');
				}
			}else{
				die('ERROR: Unable to determine connection type (' . __FILE__ . '::' . __LINE__ . ')');
			}
			return $returnDir;
		}
		
		/**
		 * getDirWsAdmin
		 * returns the correct relative admin path based on the request type
		 *
		 * @param string $forceType [optional] Used to force the request type, possible values ( SSL or NONSSL )
		 * @return string
		 */
		public static function getDirWsAdmin($forceType = false){
			if ($forceType == 'NONSSL' || ($forceType === false && getenv('HTTPS') != 'on')){
				if (self::exists('DIR_WS_HTTP_ADMIN') === true){
					$returnDir = self::get('DIR_WS_HTTP_ADMIN');
				}else{
					$returnDir = self::get('DIR_WS_ADMIN');
				}
			}elseif ($forceType == 'SSL' || ($forceType === false && getenv('HTTPS') == 'on')){
				if (self::exists('DIR_WS_HTTPS_ADMIN') === true){
					$returnDir = self::get('DIR_WS_HTTPS_ADMIN');
				}else{
					$returnDir = self::get('DIR_WS_ADMIN');
				}
			}else{
				die('ERROR: Unable to determine connection type (' . __FILE__ . '::' . __LINE__ . ')');
			}
			return $returnDir;
		}
	
		/**
		 * getDirFsAdmin
		 * returns the correct absolute admin path based on the request type
		 *
		 * @param string $forceType [optional] Used to force the request type, possible values ( SSL or NONSSL )
		 * @return string
		 */
		public static function getDirFsAdmin($forceType = false){
			if ($forceType == 'NONSSL' || ($forceType === false && getenv('HTTPS') != 'on')){
				if (self::exists('DIR_FS_HTTP_ADMIN') === true){
					$returnDir = self::get('DIR_FS_HTTP_ADMIN');
				}else{
					$returnDir = self::get('DIR_FS_ADMIN');
				}
			}elseif ($forceType == 'SSL' || ($forceType === false && getenv('HTTPS') == 'on')){
				if (self::exists('DIR_FS_HTTPS_ADMIN') === true){
					$returnDir = self::get('DIR_FS_HTTPS_ADMIN');
				}else{
					$returnDir = self::get('DIR_FS_ADMIN');
				}
			}else{
				die('ERROR: Unable to determine connection type (' . __FILE__ . '::' . __LINE__ . ')');
			}
			return $returnDir;
		}
	
		/**
		 * getDirFsCatalog
		 * returns the correct absolute catalog path based on the request type
		 *
		 * @param string $forceType [optional] Used to force the request type, possible values ( SSL or NONSSL )
		 * @return string
		 */
		public static function getDirFsCatalog($forceType = false){
			if ($forceType == 'NONSSL' || ($forceType === false && getenv('HTTPS') != 'on')){
				if (self::exists('DIR_FS_HTTP_CATALOG') === true){
					$returnDir = self::get('DIR_FS_HTTP_CATALOG');
				}else{
					$returnDir = self::get('DIR_FS_CATALOG');
				}
			}elseif ($forceType == 'SSL' || ($forceType === false && getenv('HTTPS') == 'on')){
				if (self::exists('DIR_FS_HTTPS_CATALOG') === true){
					$returnDir = self::get('DIR_FS_HTTPS_CATALOG');
				}else{
					$returnDir = self::get('DIR_FS_CATALOG');
				}
			}else{
				die('ERROR: Unable to determine connection type (' . __FILE__ . '::' . __LINE__ . ')');
			}
			return $returnDir;
		}
	
		/**
		 * load
		 * Loads all the configuration settings from the configuration table
		 *
		 * @return void
		 */
		public static function load(){
			$Qconfig = Doctrine_Query::create()
			->select('configuration_key, configuration_value')
			->from('Configuration')
			->execute();
			foreach($Qconfig as $cfg){
				/* 
				 * @TODO: Remove when ALL defines are upgraded
				 */
				if (!defined($cfg->configuration_key)){
					define($cfg->configuration_key, $cfg->configuration_value);
				}
				
				self::set($cfg->configuration_key, $cfg->configuration_value);
			}
			$Qconfig->free();
		}
		
		/**
		 * set
		 * Sets a configuration value
		 *
		 * @param string $k The key to be used when setting the configuration
		 * @param string $v The value to be used when setting the configuration
		 * @param bool $protected [optional] Sets the value to be protected
		 * @return void
		 */
		public static function set($k, $v, $protected = false){
			if (in_array($k, self::$protectedKeys)){
				trigger_error('Key Already Defined As Protected. (' . $k . ')', E_USER_ERROR);
//				throw new Exception('Key Already Defined As Protected. (' . $k . ')');
				return;
			}
			
			if ($protected === true){
				self::$protectedKeys[] = $k;
			}
			
			/* 
			 * @TODO: Remove when ALL defines are upgraded
			 */
			if (!defined($k)){
				define($k, $v);
			}
			self::$config[$k] = $v;
		}
		
		/**
		 * setMultiple
		 * Sets an array of configuration keys/values
		 *
		 * @param array $array Associative array of keys/values to be set
		 * @param bool $protected [optional] Sets the values to be protected
		 * @return void
		 */
		public static function setMultiple(array $array, $protected = false){
			foreach($array as $k => $v){
				self::set($k, $v, $protected);
			}
		}
		
		/**
		 * get
		 * Gets the configuration value based on the configuration key
		 *
		 * @param string $k The key to use to find the configuration value
		 * @return string
		 */
		public static function get($k){
			if (self::exists($k)){
				return self::$config[$k];
			}
		}
		
		/**
		 * exists
		 * Determines if the configuration key has been set
		 *
		 * @param string $k The key to use to find the configuration value
		 * @return bool
		 */
		public static function exists($k){
			return array_key_exists($k, self::$config);
		}
		
		/**
		 * isNotEmpty
		 * Determines if the configuration value is not empty
		 *
		 * @param string $k The key to use to find the configuration value
		 * @return bool
		 */
		public static function isNotEmpty($k){
			return tep_not_null(self::get($k));
		}
		
		/**
		 * inSet
		 * Determines if the passed value is in the set
		 *
		 * @param string $v The value to look for
		 * @param string $set The unexploded string of configuration values
		 * @param string $glue [optional] The glue used between the values
		 * @return bool
		 */
		public static function inSet($v, $set, $glue = ','){
			$setArr = self::explode($set, $glue);
			return in_array($v, $setArr);
		}
		
		/**
		 * explode
		 * Explodes the values based on the $glue setting
		 *
		 * @param string $k The key to look for
		 * @param string $glue [optional] The glue used between the values
		 * @return array
		 */
		public static function explode($k, $glue = ','){
			if (array_key_exists($k, self::$exploded) === false){
				self::$exploded[$k] = explode($glue, self::get($k));
			}
			return self::$exploded[$k];
		}
		
		/**
		 * addClassInstance
		 * Adds a class instance to the factory to be pulled later
		 *
		 * @param string $name The name to use when storing the object
		 * @param string $id The id to use when storing the object
		 * @param object $obj The class object to store
		 * @return array
		 */
		public static function addClassInstance($name, $id, &$obj){
			self::$classInstances[$name][$id] = $obj;
		}
		
		/**
		 * getClassInstance
		 * returns a stored class instance
		 *
		 * @param string $name The name used to store the object
		 * @param string $id The id used to store the object
		 * @return object
		 * @return bool
		 */
		public static function getClassInstance($name, $id){
			if (isset(self::$classInstances[$name][$id])){
				return self::$classInstances[$name][$id];
			}
			return false;
		}
	}
?>