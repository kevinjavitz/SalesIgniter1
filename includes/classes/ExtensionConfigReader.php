<?php
/*
 * Sales Igniter E-Commerce System
 * Version: 2.0
 *
 * I.T. Web Experts
 * http://www.itwebexperts.com
 *
 * Copyright (c) 2011 I.T. Web Experts
 *
 * This script and its source are not distributable without the written conscent of I.T. Web Experts
 */

class ExtensionConfigReader extends ConfigurationReader
{

	private $extension;

	public function __construct($extension, $extensionDir = false) {
		$this->extension = $extension;

		if ($extensionDir === false){
			$extensionDir = sysConfig::getDirFsCatalog() . 'extensions/' . $this->extension . '/';
		}

		$this->loadConfiguration($extensionDir . 'data/base/configuration.xml');

		$Extensions = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions');
		foreach($Extensions as $Ext){
			if ($Ext->isDot() || $Ext->isFile()) {
				continue;
			}

			if (is_dir($Ext->getPathname() . '/data/ext')){
				if (file_exists($Ext->getPathname() . '/data/ext/' . $this->extension . '/configuration.xml')){
					$this->loadConfiguration($Ext->getPathname() . '/data/ext/' . $this->extension . '/configuration.xml');
				}
			}
		}

		EventManager::notify('ExtensionConfigReaderExtensionConfigLoad', &$this->configData, $this->extension);
	}

	public function loadCompareData(SimpleXMLElement $xmlObj){
		$ExtConfig = array();
		$QExtConfig = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select c.* from configuration c where c.configuration_group_key = "' . (string)$xmlObj->key . '"');
		foreach($QExtConfig as $cfgInfo){
			$ExtConfig[$cfgInfo['configuration_key']] = $cfgInfo;
		}
		return $ExtConfig;
	}
}