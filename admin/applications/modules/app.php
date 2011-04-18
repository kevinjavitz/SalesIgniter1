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
	if ($App->getPageName() == 'infoboxes'){
		$module_type = 'infoboxes';
		$module_directory = sysConfig::getDirFsCatalog() . 'includes/modules/infoboxes/';
		$module_key = 'MODULE_INFOBOXES_INSTALLED';
	}else{
		$set = (isset($_GET['set']) ? $_GET['set'] : '');
		if (!empty($set)){
			switch ($set) {
				case 'shipping':
					$module_type = 'shipping';
					$module_directory = sysConfig::getDirFsCatalog() . 'includes/modules/shipping/';
					$module_key = 'MODULE_SHIPPING_INSTALLED';
					define('HEADING_TITLE', sysLanguage::get('HEADING_TITLE_MODULES_SHIPPING'));
					break;
				case 'ordertotal':
					$module_type = 'order_total';
					$module_directory = sysConfig::getDirFsCatalog() . 'includes/modules/order_total/';
					$module_key = 'MODULE_ORDER_TOTAL_INSTALLED';
					define('HEADING_TITLE', sysLanguage::get('HEADING_TITLE_MODULES_ORDER_TOTAL'));
					break;
				case 'payment':
				default:
					$module_type = 'payment';
					$module_directory = sysConfig::getDirFsCatalog() . 'includes/modules/payment/';
					$module_key = 'MODULE_PAYMENT_INSTALLED';
					define('HEADING_TITLE', sysLanguage::get('HEADING_TITLE_MODULES_PAYMENT'));
					break;
			}
		}
	}
?>