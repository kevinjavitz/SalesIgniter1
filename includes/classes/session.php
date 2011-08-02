<?php
class Session {
	private static $sessionLife = 43200; /* in seconds - 60(seconds in a minute)*60(minutes in an hour) = 3600 seconds */
	private static $cookiePath;
	private static $cookieDomain;

	public static function init(){
		global $request_type;
		self::resetSaveHandler();
		
		// set the session name and save path
		if (APPLICATION_ENVIRONMENT == 'admin'){
			self::setSessionName('osCAdminID');
		}else{
			self::setSessionName('osCID');
		}
		
		self::setSavePath(sysConfig::get('SESSION_WRITE_DIRECTORY'));

		// set the session name and save path
		if ($_SERVER['HTTP_HOST'] == 'localhost'){
			self::$cookieDomain = '';
		}else{
			self::$cookieDomain = str_replace('www.', '.', $_SERVER['HTTP_HOST']);
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
		$Qvalue = mysql_query('select value from sessions where sesskey = "' . $key .'" and expiry > "' . time() . '"');
		if (mysql_num_rows($Qvalue)){
			$Result = mysql_fetch_assoc($Qvalue);
			$value = $Result['value'];
			if (!empty($value)){
				return $value;
			}
		}
		return '';
	}

	public static function _write($key, $val){
		$Qcheck = mysql_query('select count(*) as total from sessions where sesskey = "' . $key . '"');
		$check = mysql_fetch_assoc($Qcheck);

		if ($check['total'] > 0){
			mysql_query('insert into sessions (sesskey, value, expiry) values ("' . $key . '", "' . $val . '", "' . (time() + self::$sessionLife) . '")');
		}else{
			mysql_query('update sessions set value = "' . $val . '", expiry = "' . (time() + self::$sessionLife) . '" where sesskey = "' . $key . '"');
		}
		return true;
	}

	public static function _destroy($key){
		mysql_query('delete from sessions where sesskey = "' . $key . '"');
		return true;
	}

	public static function _gc($maxLifetime){
		mysql_query('delete from sessions where expiry < "' . time() . '"');
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
		}else{
			ExceptionManager::report('Undefined Array Index', E_USER_ERROR);
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