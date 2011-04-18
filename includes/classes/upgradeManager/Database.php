<?php
class UpgradeDatabase{

	private $version;

	public function __construct($version){
		$this->version = $version;
	}

	public function createTables(){
		global $appExtension;
		$manager = Doctrine_Manager::getInstance();
		$DoctrineConnection = $manager->getCurrentConnection();
		$DoctrineExport = $DoctrineConnection->export;
		$DoctrineImport = $DoctrineConnection->import;

		$Dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'ext/Doctrine/Models/');
		$newModels = array();
		foreach($Dir as $d){
			if ($d->isDot() || substr($d->getBasename(), -4) != '.php') {
				continue;
			}

			$Model = $d->getBasename('.php');
			$ModelObj = Doctrine_Core::getTable($Model);
			if ($DoctrineImport->tableExists($ModelObj->getTableName()) === false){
				$newModels[] = $Model;
			}
		}

		$Dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
		foreach($Dir as $d){
			if ($d->isDot() || $d->isFile()) {
				continue;
			}

			$extName = $d->getBasename();
			$ext = $appExtension->getExtension($extName);
			if ($ext->isInstalled() === true){
				if (is_dir($d->getPathname() . '/Doctrine/base/')){
					$mDir = new DirectoryIterator($d->getPathname() . '/Doctrine/base/');
					foreach($mDir as $m){
						$Model = $m->getBasename('.php');
						$ModelObj = Doctrine_Core::getTable($Model);
						if ($DoctrineImport->tableExists($ModelObj->getTableName()) === false){
							$newModels[] = $Model;
						}
					}
				}

				if (is_dir($d->getPathname() . '/Doctrine/ext/')){
					$emDir = new DirectoryIterator($d->getPathname() . '/Doctrine/ext/');
					foreach($emDir as $em){
						if ($em->isDot() || $em->isFile()) {
							continue;
						}

						$extExtName = $em->getBasename();
						$extExt = $appExtension->getExtension($extExtName);
						if ($extExt->isInstalled() === true){
							$extExtModels = new DirectoryIterator($em->getPathname());
							foreach($extExtModels as $eem){
								if ($eem->isDot() || $eem->isDir()) continue;

								$Model = $eem->getBasename('.php');
								$ModelObj = Doctrine_Core::getTable($Model);
								if ($DoctrineImport->tableExists($ModelObj->getTableName()) === false){
									$newModels[] = $Model;
								}
							}
						}
					}
				}
			}
		}

		if (!empty($newModels)){
			Doctrine_Core::createTablesFromArray($newModels);
		}
	}

	public function addColumns(){
		$this->includeFiles('addColumns');
	}

	public function updateData(){
		$this->includeFiles('updateData');
	}

	public function removeColumns(){
		$this->includeFiles('removeColumns');
	}

	public function removeTables(){
		$this->includeFiles('removeTables');
	}

	private function includeFiles($folder){
		$manager = Doctrine_Manager::getInstance();
		$DoctrineConnection = $manager->getCurrentConnection();
		$DoctrineExport = $DoctrineConnection->export;
		$DoctrineImport = $DoctrineConnection->import;

		$Dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'ext/Doctrine/ModelsUpgrades/' . $this->version . '/' . $folder . '/');
		$sorted = array();
		foreach($Dir as $d){
			if ($d->isDot()) {
				continue;
			}
			$sorted[$d->getBasename()] = $d->getPathname();
		}
		ksort($sorted);

		$newModels = array();
		foreach($sorted as $d){
			require($d);
		}
	}
}

?>