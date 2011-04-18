<?php
	$OrderProduct = new OrderCreatorProduct();
	$OrderProduct->setProductsId($_GET['pID']);
	$OrderProduct->setPurchaseType('new');
	$OrderProduct->setQuantity(1);

	$Editor->ProductManager->add($OrderProduct);

	$html = '<tr data-id="' . $OrderProduct->getId() . '">' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;">' . $OrderProduct->getQuantityEdit() . '</td>' .
		'<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . $OrderProduct->getNameEdit($Editor->ProductManager->getExcludedPurchaseTypes($OrderProduct)) . '</td>' .
		'<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . ($OrderProduct->hasBarcode() ? $OrderProduct->getBarcode() : '') . '</td>' .
		'<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . $OrderProduct->getModel() . '</td>' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getTaxRateEdit() . '</td>' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit() . '</td>' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit(false, true) . '</td>' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit(true, false) . '</td>' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit(true, true) . '</td>' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;"><span class="ui-icon ui-icon-closethick deleteProductIcon"></span></td>' .
	'</tr>';

	EventManager::attachActionResponse($html, 'html');
?>