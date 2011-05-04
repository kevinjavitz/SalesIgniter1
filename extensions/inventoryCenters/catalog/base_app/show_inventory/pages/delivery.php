<?php


		$Qinv = Doctrine_Core::getTable('ProductsInventoryCenters')->findOneByInventoryCenterId($_GET['delv']);
		$deliveryInstructions = $Qinv->inventory_center_delivery_instructions;

	$contentHeading = "Delivery Instructions";
	$contentHtml = $deliveryInstructions;


	$contentHeading = stripslashes($contentHeading);
	$contentHtml = stripslashes($contentHtml);

	$pageTitle = $contentHeading;
	$pageContents = $contentHtml;

	//$pageButtons = htmlBase::newElement('button')
	//->usePreset('continue')
	//->setHref(itw_app_link(null, 'index', 'default'))
	//->draw();

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	//$pageContent->set('pageButtons', $pageButtons);

	if (isset($_GET['dialog'])){
		$Template->setPopupMode(true);
	}
		
	/*$continueButton = htmlBase::newElement('button')->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'));
		
	$pageContent->setVars(array(
		'pageHeader'     => $contentHeading,
		'continueButton' => $continueButton->draw(),
		'pageContent'    => $contentHtml,
	));

		$Template->setPopupMode(true);
		$pageContent->setTemplateFile('popup.tpl', DIR_FS_CATALOG . 'extensions/inventoryCenters/catalog/base_app/show_inventory/templates/');


		//$pageContent->setTemplateFile('default.tpl', DIR_FS_CATALOG . 'extensions/payPerRentals/catalog/base_app/show_shipping/templates/');
	
	echo $pageContent->parse();*/
?>