<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxProductPurchaseTypes extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('productPurchaseTypes');
	}

	public function show(){
			global $appExtension;
			$htmlText = '';
			$boxWidgetProperties = $this->getWidgetProperties();
			if (isset($_GET['products_id'])){
				$productID = $_GET['products_id'];
				$product = new product((int)$productID);


				$purchaseBoxes = array();
				$purchaseTypes = array();
				foreach($product->productInfo['typeArr'] as $typeName){
					$canAdd = false;
					if($boxWidgetProperties->showNew == 'true' && $typeName == 'new'){
						$canAdd = true;
					}
					if($boxWidgetProperties->showUsed == 'true' && $typeName == 'used'){
						$canAdd = true;
					}
					if($boxWidgetProperties->showStream == 'true' && $typeName == 'stream'){
						$canAdd = true;
					}
					if($boxWidgetProperties->showDownload == 'true' && $typeName == 'download'){
						$canAdd = true;
					}
					if($boxWidgetProperties->showRental == 'true' && $typeName == 'rental'){
						$canAdd = true;
					}
					if($boxWidgetProperties->showReservation == 'true' && $typeName == 'reservation'){
						$canAdd = true;
					}
					if($canAdd){
						$purchaseTypes[$typeName] = $product->getPurchaseType($typeName);
						if ($purchaseTypes[$typeName]){
							$settings = $purchaseTypes[$typeName]->getPurchaseHtml('product_info');
							if (is_null($settings) === false){
								EventManager::notify('ProductInfoPurchaseBoxOnLoad', &$settings, $typeName, $purchaseTypes);
								$purchaseBoxes[] = $settings;
							}
						}
					}
				}

				$extDiscounts = $appExtension->getExtension('quantityDiscount');
				$extAttributes = $appExtension->getExtension('attributes');

				$purchaseTable = htmlBase::newElement('table')
					->addClass('ui-widget')
					->css('width', '100%')
					->setCellPadding(5)
					->setCellSpacing(0);

				$columns = array();
				foreach($purchaseBoxes as $boxInfo){
					if ($extAttributes !== false){
						$boxInfo['content'] .= $extAttributes->drawAttributes(array(
								'productClass' => $product,
								'purchase_type' => $boxInfo['purchase_type']
							));
					}

					if ($extDiscounts !== false && $purchaseTypes[$boxInfo['purchase_type']]->hasInventory() && $boxWidgetProperties->showQtyDiscounts == 'true'){
						$boxInfo['content'] .= $extDiscounts->showQuantityTable(array(
								'productClass' => $product,
								'purchase_type' => $boxInfo['purchase_type'],
								'product_id' => $product->getId()
							));
					}

					$boxInfo['content'] .= tep_draw_hidden_field('products_id', $productID);

					if($boxWidgetProperties->widgetHeader == 'true'){
						$boxObj = htmlBase::newElement('infobox')
						->setForm(array(
							'name' => 'cart_quantity',
							'action' => $boxInfo['form_action']
						))
						->css('width', 'auto')->removeCss('margin-left')->removeCss('margin-right')
						->setHeader($boxInfo['header'])
						->setButtonBarLocation('bottom');
					}else{
						$boxObj = htmlBase::newElement('form')
						->attr('name','cart_quantity')
						->attr('action', $boxInfo['form_action'])
						->attr('method', 'post')
						->css('width', 'auto')->removeCss('margin-left')->removeCss('margin-right');
					}

					if ($boxInfo['allowQty'] === true && $boxWidgetProperties->useQty == 'true'){
						$qtyInput = htmlBase::newElement('input')
							->css('margin-right', '1em')
							->setSize(3)
							->setName('quantity[' . $boxInfo['purchase_type'] . ']')
							->setLabel('Quantity:')
							->setValue(1)
							->setLabelPosition('before');
						if($boxWidgetProperties->widgetHeader == 'true'){
							$boxObj->addButton($qtyInput);
						}else{
							$boxObj->append($qtyInput);
						}
					}
					if(isset($boxInfo['button']) && is_object($boxInfo['button']) && $boxWidgetProperties->showButton == 'true'){
						if($boxWidgetProperties->widgetHeader == 'true'){
							$boxObj->addButton($boxInfo['button']);
						}else{
							$boxObj->append($boxInfo['button']);
						}
					}

					EventManager::notifyWithReturn('ProductInfoTabImageBeforeDrawPurchaseType', &$product, &$boxObj, &$boxInfo);
					if($boxWidgetProperties->showPrice == 'true'){
						if($boxWidgetProperties->widgetHeader == 'true'){
							$boxObj->addContentRow($boxInfo['content']);
						}else{
							$boxContentHtml = htmlBase::newElement('div')
							->addClass('purchaseType')
							->html($boxInfo['content']);
							$boxObj->append($boxContentHtml);
						}
					}

					$columns[] = array(
						'align' => 'center',
						'valign' => 'top',
						'text' => $boxObj->draw()
					);

					if (sizeof($columns) > 0){
						$purchaseTable->addBodyRow(array(
								'columns' => $columns
							));
						$columns = array();
					}
				}

				if(isset($purchaseTable)){
					$htmlText = $purchaseTable->draw();
				}else{
					$htmlText = '';
				}
			}
			$this->setBoxContent('<div class="prodPurchaseTypes">'.$htmlText.'</div>');
			return $this->draw();
	}
}
?>