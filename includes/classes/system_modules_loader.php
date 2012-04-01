<?php
class SystemModulesLoader {
	protected static $Modules = array();
	
	private static function getClassModules(){
		if (array_key_exists(static::$classPrefix, self::$Modules)){
			$returnVal = self::$Modules[static::$classPrefix];
		}else{
			$returnVal = array();
		}
		return $returnVal;
	}
	
	public static function registerModule($moduleName, &$class){
		self::$Modules[static::$classPrefix][$moduleName] =& $class;
		if (method_exists(self::$Modules[static::$classPrefix][$moduleName], 'onLoad')){
			self::$Modules[static::$classPrefix][$moduleName]->onLoad();
		}
	}
	
	public static function unregisterModule($moduleName){
		if (method_exists(self::$Modules[static::$classPrefix][$moduleName], 'onUnload')){
			self::$Modules[static::$classPrefix][$moduleName]->onUnload();
		}
		unset(self::$Modules[static::$classPrefix][$moduleName]);
	}
	
	public static function getModules($includeDisabled = false){
		if ($includeDisabled === true){
			return self::getClassModules();
		}else{
			$returnArr = array();
			foreach(self::getClassModules() as $ModuleName => $Module){
				if ($Module->isEnabled() === true){
					$enabledModulesId[$ModuleName] = $Module->getDisplayOrder();
					$returnArr[$ModuleName] = $Module;
				}
			}
			array_multisort($enabledModulesId, $returnArr);
			return $returnArr;
		}
	}
	
	public static function countEnabled(){
		$enabled = 0;
		foreach(self::getClassModules() as $ModuleName => $Module){
			if ($Module->isEnabled() === true){
				$enabled++;
			}
		}
		return $enabled;
	}

	public static function hasModules(){
		return (self::countEnabled() > 0);
	}
	
	public static function getModuleDirs(){
		global $appExtension;
		$moduleDirs = array(
			sysConfig::getDirFsCatalog() . 'includes/modules/' . static::$dir . '/'
		);
		$extensions = $appExtension->getExtensions();
		foreach($extensions as $extCls){
			if ($extCls->isEnabled()){
				if (is_dir($extCls->getExtensionDir() . static::$dir . '/')){
					$moduleDirs[] = $extCls->getExtensionDir() . static::$dir . '/';
				}
			}
		}
		return $moduleDirs;
	}
	
	public static function findModuleDir($moduleName){
		$moduleDir = false;
		foreach(self::getModuleDirs() as $dirName){
			$dirObj = new DirectoryIterator($dirName);
			foreach($dirObj as $dir){
				if ($dir->isDot() || $dir->isFile()) continue;
				
				if ($dir->getBasename() == $moduleName){
					$moduleDir = $dir->getPathname() . '/';
					break 2;
				}
			}
		}
		return $moduleDir;
	}
	
	public static function isLoaded($moduleName, $loadOnFail = false){
		$isLoaded = false;
		if (array_key_exists($moduleName, self::getClassModules())){
			$isLoaded = true;
		}else{
			if ($loadOnFail === true){
				if (self::loadModule($moduleName) === true){
					$isLoaded = true;
				}
			}
		}
		return $isLoaded;
	}
	
	public static function isEnabled($moduleName, $loadOnFail = false){
		$isEnabled = false;
		if (self::isLoaded($moduleName, $loadOnFail) === true){
			$classModules = self::getClassModules();
			if (array_key_exists($moduleName, $classModules)){
				$isEnabled = $classModules[$moduleName]->isEnabled();
			}
		}
		return $isEnabled;
	}
	
	public static function loadModule($moduleCode, $dir = false, $reloadModule = false){
		if ($dir === false){
			$dir = self::findModuleDir($moduleCode);
		}
		
		$isLoaded = false;
		if ($dir !== false){
			$className = static::$classPrefix . ucfirst($moduleCode);
			if (!class_exists($className)){
				require($dir . 'module.php');
			}
			
			$register = false;
			if (self::isLoaded($moduleCode) === true){
				if ($reloadModule === true){
					$classObj = new $className;
					$register = true;
				}
			}else{
				$classObj = new $className;
				$register = true;
			}
			
			if ($register === true){
				self::registerModule($moduleCode, $classObj);
			}
			
			$isLoaded = true;
		}
		return $isLoaded;
	}
	
	public static function unloadModule($moduleName){
		$unloaded = false;
		if (self::isLoaded($moduleName) === true){
			self::unregisterModule($moduleName);
			$unloaded = true;
		}
		return $unloaded;
	}

	public static function loadModules($reloadAll = false){
		$modulesLoaded = false;
		foreach(self::getModuleDirs() as $dirName){
			$dirObj = new DirectoryIterator($dirName);
			foreach($dirObj as $dir){
				if ($dir->isDot() || $dir->isFile()) continue;
					
				if (self::loadModule($dir->getBasename(), $dir->getPathname() . '/', $reloadAll) === true){
					$modulesLoaded = true;
				}
			}
		}
		return $modulesLoaded;
	}
	
	public static function getModule($moduleName, $ignoreStatus = false){
		$Module = false;
		if (self::isLoaded($moduleName, true) === true){
			$Module = self::$Modules[static::$classPrefix][$moduleName];
			if ($ignoreStatus === false){
				if ($Module->isEnabled() === false){
					$Module = false;
				}
			}
		}
		return $Module;
	}
}
?>