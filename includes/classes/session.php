<?php
class Session {
	private static $sessionLife = 43200; /* in seconds - 60(seconds in a minute)*60(minutes in an hour) = 3600 seconds */
	private static $cookiePath;
	private static $cookieDomain;

	public static function init() {
		global $request_type, $session_started;
		self::resetSaveHandler();
		
		// set the session name and save path
		if (APPLICATION_ENVIRONMENT == 'admin'){
			self::setSessionName('osCAdminID');
		}else{
			self::setSessionName('osCID');
		}
		
		self::setSavePath(sysConfig::get('SESSION_WRITE_DIRECTORY'));

		// set the session name and save path
		if (sysConfig::get('HTTP_HOST') == 'localhost'){
			self::$cookieDomain = '';
		}else{
			self::$cookieDomain = str_replace('www.', '.', sysConfig::get('HTTP_HOST'));
		}

		if (APPLICATION_ENVIRONMENT == 'admin'){
			self::$cookiePath = sysConfig::getDirWsAdmin();
			self::setSessionName('osCAdminID');
		}else{
			self::$cookiePath = '/';
			self::setSessionName('osCID');
		}
		session_set_cookie_params(0, self::$cookiePath, self::$cookieDomain);

		// set the session ID if it exists
		$sessionName = self::getSessionName();
		if (isset($_POST[$sessionName])){
			self::setSessionId($_POST[$sessionName]);
		} elseif ($request_type == 'SSL' && isset($_GET[$sessionName])){
			self::setSessionId($_GET[$sessionName]);
		}elseif ((isset($_GET['rType']) && $_GET['rType'] == 'ajax') && isset($_GET[$sessionName])){
			self::setSessionId($_GET[$sessionName]);
		}elseif ($sessionName == 'osCAdminID' && isset($_GET[$sessionName])){
			self::setSessionId($_GET[$sessionName]);
		}

		// start the session
		$session_started = false;
		if (sysConfig::get('SESSION_FORCE_COOKIE_USE') == 'True'){
			tep_setcookie('cookie_test', 'please_accept_for_session', time() + 60 * 60 * 24 * 30, $cookie_path, $cookie_domain);

			if (isset($_COOKIE['cookie_test'])){
				Session::start();
				$session_started = true;
			}
		}
		elseif (sysConfig::get('SESSION_BLOCK_SPIDERS') == 'True') {
			$user_agent = strtolower(getenv('HTTP_USER_AGENT'));
			$spider_flag = false;

			if (tep_not_null($user_agent)){
				$spiders = file(sysConfig::getDirFsCatalog() . 'includes/spiders.txt');

				for($i = 0, $n = sizeof($spiders); $i < $n; $i++){
					if (tep_not_null($spiders[$i])){
						if (is_integer(strpos($user_agent, trim($spiders[$i])))){
							$spider_flag = true;
							break;
						}
					}
				}
			}

			if ($spider_flag == false){
				Session::start();
				$session_started = true;
			}
		}
		else {
			Session::start();
			$session_started = true;
		}

		// set SID once, even if empty
		$SID = (defined('SID') ? SID : '');

		// verify the ssl_session_id if the feature is enabled
		if (($request_type == 'SSL') && (sysConfig::get('SESSION_CHECK_SSL_SESSION_ID') == 'True') && (sysConfig::get('ENABLE_SSL') == true) && ($session_started == true)){
			if (Session::exists('SSL_SESSION_ID') === false){
				Session::set('SSL_SESSION_ID', $_SERVER['SSL_SESSION_ID']);
			}

			if (Session::get('SSL_SESSION_ID') != $_SERVER['SSL_SESSION_ID']){
				Session::destroy();
				tep_redirect(itw_app_link('appExt=infoPages', 'show_page', 'ssl_check'));
			}
		}

		// verify the browser user agent if the feature is enabled
		if (sysConfig::get('SESSION_CHECK_USER_AGENT') == 'True'){
			$http_user_agent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
			if (Session::exists('SESSION_USER_AGENT') === false){
				Session::set('SESSION_USER_AGENT', $http_user_agent);
			}

			if (Session::get('SESSION_USER_AGENT') != $http_user_agent){
				Session::destroy();
				tep_redirect(itw_app_link(null, 'account', 'login'));
			}
		}

		// verify the IP address if the feature is enabled
		if (sysConfig::get('SESSION_CHECK_IP_ADDRESS') == 'True'){
			$ip_address = tep_get_ip_address();
			if (Session::exists('SESSION_IP_ADDRESS') === false){
				Session::set('SESSION_IP_ADDRESS', $ip_address);
			}

			if (Session::get('SESSION_IP_ADDRESS') != $ip_address){
				Session::destroy();
				tep_redirect(itw_app_link(null, 'account', 'login'));
			}
		}
	}

	public static function resetSaveHandler(){
		session_set_save_handler(
		array(__CLASS__, '_open'),
		array(__CLASS__, '_close'),
		array(__CLASS__, '_read'),
		array(__CLASS__, '_write'),
		array(__CLASS__, '_destroy'),
		array(__CLASS__, '_gc')
		);
	}

	public static function _open($savePath, $sessionName){
		return true;
	}

	public static function _close(){
		return true;
	}

	public static function _read($key){
		$ResultSet = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select value from sessions where sesskey = "' . $key .'" and expiry > "' . time() . '"');
		if (sizeof($ResultSet) > 0){
			$value = stripslashes($ResultSet[0]['value']);
			if (!empty($value)){
				return $value;
			}
		}
		return '';
	}

	public static function _write($key, $val) {
		if (
			(basename($_SERVER['PHP_SELF']) != 'stylesheet.php') &&
			(basename($_SERVER['PHP_SELF']) != 'javascript.php') &&
			($_GET['rType'] != 'ajax' && APPLICATION_ENVIRONMENT == 'admin' || APPLICATION_ENVIRONMENT == 'catalog')
		){
			$Check = Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->fetchAssoc('select count(*) as total from sessions where sesskey = "' . $key . '"');

			if ($Check[0]['total'] <= 0){
				$queryStr = 'insert into sessions (sesskey, value, expiry) values ("' . $key . '", "' . addslashes($val) . '", "' . (time() + self::$sessionLife) . '")';
			}
			else {
				$queryStr = 'update sessions set value = "' . addslashes($val) . '", expiry = "' . (time() + self::$sessionLife) . '" where sesskey = "' . $key . '"';
			}
			Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->exec($queryStr);
		}
		return true;
	}

	public static function _destroy($key){
		Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->exec('delete from sessions where sesskey = "' . $key . '"');
		return true;
	}

	public static function _gc($maxLifetime){
		Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->exec('delete from sessions where expiry < "' . time() . '"');
		return true;
	}

	public static function start(){
		$sane_session_id = true;
		$sessionName = self::getSessionName();
		if (isset($_GET[$sessionName]) && (empty($_GET[$sessionName]) || (ctype_alnum($_GET[$sessionName]) === false))){
			$sane_session_id = false;
		}elseif (isset($_POST[$sessionName]) && (empty($_POST[$sessionName]) || (ctype_alnum($_POST[$sessionName]) === false))){
			$sane_session_id = false;
		}elseif (isset($_COOKIE[$sessionName]) && (empty($_COOKIE[$sessionName]) || (ctype_alnum($_COOKIE[$sessionName]) === false))){
			$sane_session_id = false;
		}
		
		if ($sane_session_id === false){
			if (isset($_COOKIE[$sessionName])){
				$session_data = session_get_cookie_params();
				setcookie($sessionName, '', time()-42000, $session_data['path'], $session_data['domain']);
			}
			
			tep_redirect(itw_app_link(null, 'index', 'default', 'NONSSL', false));
		}elseif (session_start()){
			return true;
		}
		return false;
	}

	public static function stop(){
		global $ErrorException;
		unset($ErrorException);
		try {
			serialize($_SESSION);
		} catch(Exception $e){
			echo '<pre>' . $e->__toString() . '</pre>';
		}
		
		return session_write_close();
	}

	public static function recreate(){
		return session_regenerate_id(true);
	}

	public static function set($varName, $value, $useKey = null){
		if (is_null($useKey) === false){
			if (!isset($_SESSION[$varName]) || !is_array($_SESSION[$varName])){
				$_SESSION[$varName] = array();
			}
			$_SESSION[$varName][$useKey] = $value;
		}else{
			$_SESSION[$varName] = $value;
		}
		return true;
	}

	public static function get($varName, $useKey = null){
		if (self::exists($varName, $useKey)){
			if (is_null($useKey) === false){
				return $_SESSION[$varName][$useKey];
			}else{
				return $_SESSION[$varName];
			}
		}
		else {
			ExceptionManager::report('Undefined Array Index ( ' . $varName . ' )', E_USER_ERROR);
		}
	}

	public static function &getReference($varName, $useKey = null){
		if (self::exists($varName, $useKey)){
			if (is_null($useKey) === false){
				return $_SESSION[$varName][$useKey];
			}else{
				return $_SESSION[$varName];
			}
		}else{
			ExceptionManager::report('Undefined Array Index', E_USER_ERROR);
		}
	}

	public static function remove($varName, $useKey = null){
		if (self::exists($varName, $useKey)){
			if (is_null($useKey) === false){
				unset($_SESSION[$varName][$useKey]);
			}else{
				unset($_SESSION[$varName]);
			}
		}
		return true;
	}

	public static function append($varName, $val, $useKey = null){
		if (self::exists($varName, $useKey)){
			if (is_null($useKey) === false){
				Session::set($varName, $val, $useKey);
			}elseif (is_array($_SESSION[$varName])){
				$_SESSION[$varName][] = $val;
			}else{
				$_SESSION[$varName] .= $val;
			}
		}
		return true;
	}

	public static function exists($varName, $useKey = null){
		if (isset($_SESSION[$varName])){
			if (is_null($useKey) === false){
				if (isset($_SESSION[$varName][$useKey])){
					return true;
				}
			}else{
				return true;
			}
		}
		return false;
	}
	
	public static function sizeOf($varName, $useKey = null){
		if (isset($_SESSION[$varName]) && is_array($_SESSION[$varName])){
			if (is_null($useKey) === false){
				if (isset($_SESSION[$varName][$useKey]) && is_array($_SESSION[$varName][$useKey])){
					return sizeof($_SESSION[$varName][$useKey]);
				}
			}else{
				return sizeof($_SESSION[$varName]);
			}
		}
		return 0;
	}

	public static function getSessionName(){
		return session_name();
	}

	public static function setSessionName($newName){
		return session_name($newName);
	}

	public static function setSessionId($newId){
		return session_id($newId);
	}

	public static function getSessionId(){
		return session_id();
	}

	public static function setSavePath($path){
		return session_save_path($path);
	}

	public static function getSavePath(){
		return session_save_path();
	}

	public function __destruct(){
		//return session_write_close();
	}
}
?>