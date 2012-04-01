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

class MainConfigReader extends ConfigurationReader
{

	/**
	 * @var string
	 */
	private $group;

	/**
	 * @param string $group
	 */
	public function __construct($group) {
		$this->group = $group;

		$this->loadConfiguration(sysConfig::getDirFsCatalog() . 'includes/configs/' . $this->group . '.xml');

		if (class_exists('EventManager')){
			EventManager::notify('MainConfigReaderModuleConfigLoad', &$this->configData, $this->group);
		}
	}

	/**
	 * @param SimpleXMLElement $xmlObj
	 * @return array
	 */
	public function loadCompareData(SimpleXMLElement $xmlObj){
		$Config = array();
		$QConfig = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select c.* from configuration c where c.configuration_group_key = "' . $this->group . '"');
		foreach($QConfig as $cfgInfo){
			$Config[$cfgInfo['configuration_key']] = $cfgInfo;
		}
		return $Config;
	}
}
