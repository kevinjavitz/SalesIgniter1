<?php
$Modules = Doctrine_Core::getTable('Modules');
$ModulesConfiguration = Doctrine_Core::getTable('ModulesConfiguration');

$paymentsInstalled = $DoctrineConnection->execute('select * from configuration where  configuration_key = ?', array(
	'MODULE_PAYMENT_INSTALLED'
));

$orderTotalsInstalled = $DoctrineConnection->execute('select * from configuration where  configuration_key = ?', array(
	'MODULE_ORDER_TOTAL_INSTALLED'
));

$shippingInstalled = $DoctrineConnection->execute('select * from configuration where  configuration_key = ?', array(
	'MODULE_SHIPPING_INSTALLED'
));

$PaymentModules = explode(';', $paymentsInstalled[0]['configuration_value']);
$OrderTotalModules = explode(';', $orderTotalsInstalled[0]['configuration_value']);
$ShippingModules = explode(';', $shippingInstalled[0]['configuration_value']);

require(sysConfig::getDirFsCatalog() . 'includes/modules/orderPaymentModules/install.php');
foreach($PaymentModules as $fileName){
	$moduleName = strtolower(str_replace('.php', '', $fileName));
	$Install = null;
	if (is_dir(sysConfig::getDirFsCatalog() . 'includes/modules/orderPaymentModules/' . $moduleName)){
		$Install = new OrderPaymentInstaller($moduleName);
	}
	elseif (is_dir(sysConfig::getDirFsCatalog() . 'includes/modules/orderPaymentModules/' . str_replace('_', '', $moduleName))){
		$Install = new OrderPaymentInstaller(str_replace('_', '', $moduleName));
	}

	if (is_object($Install)){
		$Install->install();
	}
}

require(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/install.php');
foreach($PaymentModules as $fileName){
	$moduleName = strtolower(str_replace('.php', '', $fileName));
	$Install = null;
	if (is_dir(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/' . $moduleName)){
		$Install = new OrderShippingInstaller($moduleName);
	}
	elseif (is_dir(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/' . str_replace('_', '', $moduleName))){
		$Install = new OrderShippingInstaller(str_replace('_', '', $moduleName));
	}

	if (is_object($Install)){
		$Install->install();
	}
}

require(sysConfig::getDirFsCatalog() . 'includes/modules/orderTotalModules/install.php');
foreach($PaymentModules as $fileName){
	$moduleName = strtolower(str_replace('.php', '', $fileName));
	$Install = null;
	if (is_dir(sysConfig::getDirFsCatalog() . 'includes/modules/orderTotalModules/' . $moduleName)){
		$Install = new OrderTotalInstaller($moduleName);
	}
	elseif (is_dir(sysConfig::getDirFsCatalog() . 'includes/modules/orderTotalModules/' . str_replace('_', '', $moduleName))){
		$Install = new OrderTotalInstaller(str_replace('_', '', $moduleName));
	}

	if (is_object($Install)){
		$Install->install();
	}
}

$configs = $DoctrineConnection->execute('select * from configuration where configuration_key like ?', array(
	'MODULE_%'
));

foreach($configs as $cInfo){
	$configKey = $cInfo['configuration_key'];
	if (stristr($configKey, '_paypal_ipn_')){
		$configKey = str_replace('_PAYPAL_IPN_', '_PAYPALIPN_', $configKey);
	}elseif (stristr($configKey, '_cod_')){
		$configKey = str_replace('_COD_', '_CASHONDELIVERY_', $configKey);
	}elseif (stristr($configKey, '_cc_vcs_')){
		$configKey = str_replace('_CC_VCS_', 'CREDITCARDVCS', $configKey);
	}elseif (stristr($configKey, '_cc_')){
		$configKey = str_replace('_CC_', '_CREDITCARD_', $configKey);
	}elseif (stristr($configKey, '_ccerr_')){
		$configKey = str_replace('_CCERR_', '_CREDITCARDERROR_', $configKey);
	}

	$DoctrineConnection->exec('update ' . $ModulesConfiguration->getTableName() . ' set configuration_value = ? where configuration_key = ? or configuration_key = ?', array(
		$cInfo['configuration_value'],
		$configKey,
		str_replace('MODULE_', 'MODULE_ORDER_', $configKey)
	));
}

$DoctrineConnection->exec('delete from configuration where configuration_key like ?', array(
	'MODULE_PAYMENT%'
));
$DoctrineConnection->exec('delete from configuration where configuration_key like ?', array(
	'MODULE_ORDER_TOTAL%'
));
$DoctrineConnection->exec('delete from configuration where configuration_key like ?', array(
	'MODULE_SHIPPING%'
));
