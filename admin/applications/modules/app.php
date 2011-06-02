<?php
	$appContent = $App->getAppContentFile();

	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.core.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.slide.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fold.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fade.js');
	$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');

	if (isset($_GET['module'])){
		$App->setInfoBoxId($_GET['module']);
	}

	$ext_module_directory = sysConfig::getDirFsCatalog() . 'extensions/';
	switch ($App->getPageName()) {
		case 'infoboxes':
			$module_type = 'infoboxes';
			$module_directory = 'infoboxes';
			$module_key = 'MODULE_INFOBOXES_INSTALLED';
			break;
		case 'purchaseTypes':
			$module_type = 'purchase_type';
			$module_directory = 'purchaseTypes';
			$module_key = 'MODULE_PURCHASETYPES_INSTALLED';
			break;
		case 'orderShipping':
			$module_type = 'shipping';
			$module_directory = 'orderShippingModules';
			$module_key = 'MODULE_SHIPPING_INSTALLED';
			break;
		case 'orderTotal':
			$module_type = 'order_total';
			$module_directory = 'orderTotalModules';
			$module_key = 'MODULE_ORDER_TOTAL_INSTALLED';
			break;
		case 'orderPayment':
		default:
			$module_type = 'payment';
			$module_directory = 'orderPaymentModules';
			$module_key = 'MODULE_PAYMENT_INSTALLED';
			break;
	}
?>