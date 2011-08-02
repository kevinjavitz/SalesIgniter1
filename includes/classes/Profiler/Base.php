<?php
	require(dirname(__FILE__) . '/Profile.php');

	class SES_Profiler {
		private static $profiles = array();

		/**
		 * @static
		 * @param $name
		 * @param bool $start
		 * @return SES_Profile
		 */
		public static function newProfile($name, $start = false){
			if (array_key_exists($name, self::$profiles)){
				$Profile = self::$profiles[$name];
			}else{
				$Profile = new SES_Profile($name);
				self::$profiles[$name] = $Profile;
			}

			if ($start === true){
				$Profile->start();
			}
			return $Profile;
		}

		public static function getAll(){
			ksort(self::$profiles);
			return self::$profiles;
		}

		public static function get($name){
			if (array_key_exists($name, self::$profiles)){
				return self::$profiles[$name];
			}
			return null;
		}
	}
?>