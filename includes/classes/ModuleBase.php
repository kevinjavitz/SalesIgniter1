<?php
/*
	Sales Igniter E-Commerce Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2011 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ModuleBase extends MI_Base
{

	private $moduleInfo = array();

	private $configData = array();

	private $xmlData = null;

	private $enabled = false;

	private $code = null;

	private $moduleType = null;

	private $title = 'No Title Set';

	private $description = 'No Description Set';

	private $path = '';

	public function init($code, $forceEnable = false, $moduleDir = false) {
		global $App;
		$this->setCode($code);

		if ($moduleDir === false){
			$this->setPath(sysConfig::getDirFsCatalog() . 'includes/modules/' . $this->getModuleType() . 'Modules/' . $this->getCode() . '/');
		}
		else {
			$this->setPath($moduleDir);
		}

		$this->moduleInfo = simplexml_load_file(
			$this->getPath() . 'data/info.xml',
			'SimpleXMLElement',
			LIBXML_NOCDATA
		);

		$Config = new ModuleConfigReader(
			$this->getCode(),
			$this->getModuleType(),
			$this->getPath()
		);
		$this->configData = $Config->getConfig();

		sysLanguage::loadDefinitions($this->getPath() . 'language_defines/global.xml');
		if (file_exists(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/includes/modules/' . $this->getModuleType() . 'Modules/' . $this->getCode() . '/global.xml')){
			sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/includes/modules/' . $this->getModuleType() . 'Modules/' . $this->getCode() . '/global.xml');
		}

		if (is_dir(sysConfig::getDirFsCatalog() . 'includes/modules/' . $this->getModuleType() . 'Modules/' . $this->getCode() . '/Doctrine/')){
			Doctrine_Core::loadModels(sysConfig::getDirFsCatalog() . 'includes/modules/' . $this->getModuleType() . 'Modules/' . $this->getCode() . '/Doctrine/', Doctrine_Core::MODEL_LOADING_AGGRESSIVE);
		}

		$this->setTitle(sysLanguage::get((string)$this->moduleInfo->title_key));
		$this->setDescription(sysLanguage::get((string)$this->moduleInfo->description_key));

		if (array_key_exists((string)$this->moduleInfo->status_key, $this->configData)){
			$this->setEnabled(($this->getConfigData((string)$this->moduleInfo->status_key) == 'True' ? true : false));
		}

		if (array_key_exists((string)$this->moduleInfo->visible_key, $this->configData)){
			if ($App->getEnv() == 'admin' && $this->getConfigData((string)$this->moduleInfo->visible_key) == 'Catalog'){
				$this->setEnabled(false);
			}elseif ($App->getEnv() == 'catalog' && $this->getConfigData((string)$this->moduleInfo->visible_key) == 'Admin'){
				$this->setEnabled(false);
			}
		}


		if ($forceEnable === true){
			$this->setEnabled(true);
		}

		if ($this->imported('Installable')){
			$this->setInstalled(($this->getConfigData($this->getModuleInfo('installed_key')) == 'True') ? true : false);
		}

		if ($this->imported('SortedDisplay')){
			$this->setDisplayOrder((int)$this->getConfigData($this->getModuleInfo('display_order_key')));
		}
	}

	public function getModuleInfo($k) {
		if (isset($this->moduleInfo->$k)){
			return (string)$this->moduleInfo->$k;
		}
		return null;
	}

	public function setEnabled($val) {
		$this->enabled = $val;
	}

	public function isEnabled() {
		return $this->enabled;
	}

	public function isFromExtension() {
		return false;
	}

	public function getExtensionName() {
		return false;
	}

	public function configExists($key) {
		return (array_key_exists($key, $this->configData));
	}

	public function getConfig() {
		return $this->configData;
	}

	public function getConfigData($key) {
		if ($this->configExists($key)){
			return $this->configData[$key]->getValue();
		}
		return null;
	}

	public function setModuleType($val) {
		$this->moduleType = $val;
	}

	public function getModuleType() {
		return $this->moduleType;
	}

	public function setPath($val) {
		$this->path = $val;
	}

	public function getPath() {
		return $this->path;
	}

	public function setCode($val) {
		$this->code = $val;
	}

	public function getCode() {
		return $this->code;
	}

	public function setTitle($val) {
		$this->title = $val;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setDescription($val) {
		$this->description = $val;
	}

	public function getDescription() {
		return $this->description;
	}
}
