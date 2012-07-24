<?php
	class sysPermissions {
		private static $perms = array();

		public static function checkLoggedIn(){
			global $navigation, $App;
			if (Session::exists('login_id') === false) {
				if(strpos($_SERVER["REQUEST_URI"],'login/default.php') === false){
					$pageURL = 'http';
					if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
					$pageURL .= "://";
					if ($_SERVER["SERVER_PORT"] != "80") {
						$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
					} else {
						$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
					}
					Session::set('redirectToAdminUrl', $pageURL);
				}
				tep_redirect(itw_app_link(null, 'login', 'default', 'SSL'));
			}
		}

		public static function isSimple(){
			if (Session::get('login_id') == 'master'){
				$isSimple = false;
			}else{
				$Admin = Doctrine_Core::getTable('Admin')
					->findOneByAdminId((int)Session::get('login_id'));
				$isSimple = ($Admin->admin_simple_admin == '1' ? true : false);
			}
			return $isSimple;
		}
		public static function loadPermissions(){
			if (Session::exists('login_groups_id') && Session::get('login_groups_id') == '1'){
				return;
			}
			
			$Qpermissions = Doctrine_Query::create()
			->from('AdminApplicationsPermissions')
			->where('FIND_IN_SET(?, admin_groups)', Session::get('login_groups_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			foreach($Qpermissions as $pInfo){
				if (!empty($pInfo['extension'])){
					self::$perms['ext'][$pInfo['extension']][$pInfo['application']][$pInfo['page']] = explode(',', $pInfo['admin_groups']);
				}else{
					self::$perms[$pInfo['application']][$pInfo['page']] = explode(',', $pInfo['admin_groups']);
				}
			}
		}
		
		public static function adminAccessAllowed($app, $page = null, $ext = null){
			if (Session::exists('login_groups_id') && Session::get('login_groups_id') == '1'){
				return true;
			}
			
			$workingArr = false;
			if (is_null($ext) === false){
				if (array_key_exists('ext', self::$perms)){
					if (array_key_exists($ext, self::$perms['ext'])){
						$workingArr = self::$perms['ext'][$ext];
					}
				}
			}else{
				$workingArr = self::$perms;
			}
			
			if ($workingArr !== false){
				if (is_null($page) === true){
					return array_key_exists($app, $workingArr);
				}else{
					if (array_key_exists($app, $workingArr)){
						if (substr($page, -4) != '.php'){
							$page .= '.php';
						}
						
						if (array_key_exists($page, $workingArr[$app])){
							$permissions = $workingArr[$app][$page];
							/* Should always return true, but just in case */
							if (in_array(Session::get('login_groups_id'), $permissions)){
								return true;
							}
						}
					}
				}
			}
			return false;
		}
	}
?>