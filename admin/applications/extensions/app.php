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

$appContent = $App->getAppContentFile();

$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.core.js');
$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.slide.js');
$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fold.js');
$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fade.js');
$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
/*
//Add New Columns That Break Old Carts
//--BEGIN--
Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('ALTER TABLE  `configuration` ADD  `configuration_group_key` INT NOT NULL');

Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('ALTER TABLE  `languages` ADD  `forced_default` INT NOT NULL');

Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('ALTER TABLE  `admin` ADD  `admin_override_password` INT NOT NULL');

Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('ALTER TABLE  `admin` ADD  `admin_favs_id` INT NOT NULL');

Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('ALTER TABLE  `admin` ADD  `admin_simple_admin` INT NOT NULL');

Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('ALTER TABLE  `admin_groups` ADD  `extra_data` INT NOT NULL');
//--END--

//Add Installed Key For Modules That Are Enabled
//--BEGIN--
$ResultSet = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc('select modules_id from modules_configuration where configuration_key LIKE "MODULE_%_STATUS"');
foreach($ResultSet as $kInfo){
	Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->exec('insert into modules_configuration (configuration_key, configuration_value, modules_id) values ("INSTALLED", "True", "' . $kInfo['modules_id'] . '")');
}
//Add Installed Key For Modules That Are Enabled
//--END--

//Update Configuration Keys That Are Known To Be Common
//--BEGIN--
Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('update modules_configuration set configuration_key = "CHECKOUT_METHOD" where configuration_key LIKE "%_CHECKOUT_METHOD"');
Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('update modules_configuration set configuration_key = "ORDER_STATUS_ID" where configuration_key LIKE "%_ORDER_STATUS_ID"');
Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('update modules_configuration set configuration_key = "ZONE" where configuration_key LIKE "%_ZONE"');
Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('update modules_configuration set configuration_key = "DISPLAY_ORDER" where configuration_key LIKE "%_SORT_ORDER"');
Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('update modules_configuration set configuration_key = "STATUS" where configuration_key LIKE "%_STATUS"');
Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('update modules_configuration set configuration_key = "TAX_CLASS" where configuration_key LIKE "%_TAX_CLASS"');
//Update Configuration Keys That Are Known To Be Common
//--END--

//Update Module Types From Old
//--BEGIN--
Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('update modules set modules_type = "orderPayment" where modules_type = "order_payment"');
Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('update modules set modules_type = "orderShipping" where modules_type = "order_shipping"');
Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('update modules set modules_type = "orderTotal" where modules_type = "order_total"');
//Update Module Types From Old
//--END--

//Delete all configuration entries that are the same value as the xml configuration files
// --BEGIN--
$Directory = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'includes/configs/');
foreach($Directory as $ConfigFile){
	if ($ConfigFile->isDot() || $ConfigFile->isDir()) continue;

	$Configuration = simplexml_load_file(
		$ConfigFile->getPathname(),
		'SimpleXMLElement',
		LIBXML_NOCDATA
	);
	$keys = array();
	foreach($Configuration->tabs->children() as $tInfo){
		foreach($tInfo->configurations->children() as $ConfigKey => $Config){
			$key = (string) $ConfigKey;
			$value = (string) $Config->value;

			$ResultSet = Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->fetchAssoc('select configuration_value from configuration where configuration_key = "' . $key . '"');
			if ($ResultSet && sizeof($ResultSet) > 0){
				if ($value == $ResultSet[0]['configuration_value']){
					$keys[] = '"' . $key . '"';
				}else{
					Doctrine_Manager::getInstance()
						->getCurrentConnection()
						->exec('update configuration set configuration_group_key = "' . (string)$Configuration->key . '" where configuration_key = "' . $key . '"');
				}
			}
		}
	}

	if (!empty($keys)){
		Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->exec('delete from configuration where configuration_key in(' . implode(',', $keys) . ')');
	}
}

$Directory = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
foreach($Directory as $ConfigFile){
	if ($ConfigFile->isDot() || $ConfigFile->isFile()) continue;

	$Configuration = simplexml_load_file(
		$ConfigFile->getPathname() . '/data/base/configuration.xml',
		'SimpleXMLElement',
		LIBXML_NOCDATA
	);
	$keys = array();
	foreach($Configuration->tabs->children() as $tInfo){
		foreach($tInfo->configurations->children() as $ConfigKey => $Config){
			$key = (string) $ConfigKey;
			$value = (string) $Config->value;

			$ResultSet = Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->fetchAssoc('select configuration_value from configuration where configuration_key = "' . $key . '"');
			if ($ResultSet && sizeof($ResultSet) > 0){
				if ($value == $ResultSet[0]['configuration_value']){
					$keys[] = '"' . $key . '"';
				}else{
					Doctrine_Manager::getInstance()
						->getCurrentConnection()
						->exec('update configuration set configuration_group_key = "' . (string)$Configuration->key . '" where configuration_key = "' . $key . '"');
				}
			}
		}
	}

	if (!empty($keys)){
		Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->exec('delete from configuration where configuration_key in(' . implode(',', $keys) . ')');
	}
}
//Delete all configuration entries that are the same value as the xml configuration files
// --END--

//Add Installed Key For Extensions That Are Enabled
//--BEGIN--
$ResultSet = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc('select configuration_key, configuration_value, configuration_group_key from configuration where configuration_key LIKE "EXTENSION_%_ENABLED"');
foreach($ResultSet as $kInfo){
	$newKey = preg_replace('/_ENABLED/', '_INSTALLED', $kInfo['configuration_key']);
	Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->exec('insert into configuration (configuration_key, configuration_value, configuration_group_key) values ("' . $newKey . '", "True", "' . $kInfo['configuration_group_key'] . '")');
}
//Add Installed Key For Extensions That Are Enabled
//--END--

//Update address formats for new formatting
//--BEGIN--
$ResultSet = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc('select * from address_format');
foreach($ResultSet as $aInfo){
	$newFormat = str_replace('$cr', "\n", $aInfo['address_format']);
	$newFormat = str_replace('$streets', '$street_address', $newFormat);
	Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->exec('update address_format set address_format = "' . $newFormat . '" where address_format_id = "' . $aInfo['address_format_id'] . '"');
}
//Update address formats for new formatting
//--END--

//Delete all configuration that were from the old modules installers
// --BEGIN--
Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('delete from configuration where configuration_key like "MODULE_%"');
//Delete all configuration that were from the old modules installers
// --END--


$Products = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc('select * from products');

foreach($Products as $Product){
	$types = explode(',', $Product['products_type']);
	foreach($types as $pType){
		switch($pType){
			case 'used':
				$price = $Product['products_price_used'];
				break;
			case 'new':
				$price = $Product['products_price'];
				break;
			case 'reservation':
				$price = 0;
				break;
		}

		$TrackMethod = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select track_method from products_inventory where type="' . $pType . '" and products_id = "' . $Product['products_id'] . '" and controller = "' . $Product['products_inventory_controller'] . '"');

		$PurchaseType = new ProductsPurchaseTypes();
		$PurchaseType->products_id = $Product['products_id'];
		$PurchaseType->status = 1;
		$PurchaseType->type_name = $pType;
		$PurchaseType->price = $price;
		$PurchaseType->tax_class_id = $Product['products_tax_class_id'];
		$PurchaseType->inventory_controller = $Product['products_inventory_controller'];
		$PurchaseType->inventory_track_method = $TrackMethod[0]['track_method'];
		$PurchaseType->save();
	}
}
Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->exec('update products set products_type = "standard"');
*/
?>