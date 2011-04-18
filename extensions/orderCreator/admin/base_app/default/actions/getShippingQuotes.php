<?php
	$total_weight = $Editor->ProductManager->getTotalWeight();
	OrderShippingModules::setDeliveryAddress($Editor->AddressManager->getAddress('delivery'));
	
	$selectBox = '<select name="order_total[' . $_GET['totalCount'] . '][title]" style="width:98%;">';
	$Quotes = OrderShippingModules::quote();
	//print_r($Quotes);
	foreach($Quotes as $qInfo){
		$selectBox .= '<optgroup label="' . $qInfo['module'] . '">';
		foreach($qInfo['methods'] as $mInfo){
			$selectBox .= '<option value="' . $qInfo['id'] . '_' . $mInfo['id'] . '">' . $mInfo['title'] . ' ( Recommended Price: ' . $currencies->format($mInfo['cost']) . ' )</option>';
		}
		$selectBox .= '</optgroup>';
	}
	
	$selectBox .= '</select>';
	
	EventManager::attachActionResponse($selectBox, 'html');
?>