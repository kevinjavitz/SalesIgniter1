<?php
$html = '';
$idarr = array();
$purchaseTypes = array();
$mainOrderedProduct = $Editor->ProductManager->get((int)$_POST['id']);
$mainPurchaseType = $mainOrderedProduct->purchaseTypeClass;
$mainInfo = $mainOrderedProduct->getPInfo();

if($mainOrderedProduct)
foreach($_POST['addon_product'] as $addon => $val){
	    $purchaseTypeCode = $_POST['addon_product_type'][$addon];
	    $OrderProduct = new OrderCreatorProduct();
	    $OrderProduct->setProductsId($addon);
	    $OrderProduct->setQuantity($_POST['addon_product_qty'][$addon]);
		$OrderProduct->setPurchaseType($purchaseTypeCode);
	    $Editor->ProductManager->add($OrderProduct);
		if($purchaseTypeCode == 'reservation'){
			if(isset($mainInfo['reservationInfo'])){
				$pInfo = $OrderProduct->getPInfo();
				$pInfo['reservationInfo'] = $mainInfo['reservationInfo'];
				$OrderProduct->setPInfo($pInfo);
			}

		}
	    //$_GET['purchase_type'] = $purchaseTypeCode;
		//$OrderedProduct = $Editor->ProductManager->get($OrderProduct->getId());
		//$OrderedProduct->updateProductInfo();

		    $html .= '<tr data-product_id="' . $addon . '" data-id="' . $OrderProduct->getId() . '">' .
			    '<td class="ui-widget-content" valign="top" align="right" style="border-top:none;">' . $OrderProduct->getQuantityEdit() . '</td>' .
			    '<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . $OrderProduct->getNameEdit() . '</td>' .
			    '<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . ($OrderProduct->hasBarcode()
			    ? $OrderProduct->getBarcode() : '') . '</td>' .
			    '<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . $OrderProduct->getModel() . '</td>' .
			    '<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getTaxRateEdit() . '</td>' .
			    '<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit() . '</td>' .
			    '<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit(false, true) . '</td>' .
			    '<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit(true, false) . '</td>' .
			    '<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit(true, true) . '</td>' .
			    '<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;"><span class="ui-icon ui-icon-closethick deleteProductIcon"></span></td>' .
			    '</tr>';
		$idarr[] = 'product['.$OrderProduct->getId().'][purchase_type]';
	    $purchaseTypes[] = $purchaseTypeCode;


	}

    EventManager::attachActionResponse(array(
		'success' => true,
		'html'	=> $html,
		'idarr' => $idarr,
		'purchaseType' => $purchaseTypes
	), 'json');

?>