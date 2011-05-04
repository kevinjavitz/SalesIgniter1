<?php
	$OrderProduct = $Editor->ProductManager->get((int) $_GET['id']);
   	$OrderProduct->setPurchaseType($_GET['purchase_type']);
	$Product = $OrderProduct->productClass;
	$purchaseTypeClass = $OrderProduct->purchaseTypeClass;

	/*if (isset($_POST['id']['reservation']) && !empty($_POST['id']['reservation'])){
		$purchaseTypeClass->inventoryCls->invMethod->trackMethod->aID_string = attributesUtil::getAttributeString($_POST['id']['reservation']);
	}*/

    $pInfo = $OrderProduct->getPInfo();
	$calendar = ReservationUtilities::getCalendar($pInfo['products_id'], $Product, $purchaseTypeClass, false);

	$pageButtons = sysLanguage::get('TEXT_ESTIMATED_PRICING') . '<span id="priceQuote"></span>';
	$pageButtons .= '<input type="hidden" name="products_id" id="pID" value="' . $pInfo['products_id'] . '">';
	$pageButtons .= $purchaseTypeClass->getHiddenFields($pInfo['products_id']);

	$pageButtons .= htmlBase::newElement('div')
	->attr('id','inCart')
	->css(array(
		   'display'   => 'inline-block',
		   'width' => '150px'
	))
	->html(sysLanguage::get('TEXT_BUTTON_IN_CART'))
	->draw();

	$calendar .= $pageButtons;

	EventManager::attachActionResponse(array(
		'success'   => true,
		'calendar'  => $calendar
	   ), 'json');
?>
