<?php
class productListing_productsPriceNew {

	public function sortColumns(){
		$selectSortKeys = array(
								array(
									'value' => 'p.products_price',
									'name'  => sysLanguage::get('PRODUCT_LISTING_PRICE_NEW')
								),
								array(
									'value' => 'p.products_price_used',
									'name'  => sysLanguage::get('PRODUCT_LISTING_PRICE_USED')
								),
								array(
									'value' => 'p.products_price_stream',
									'name'  => sysLanguage::get('PRODUCT_LISTING_PRICE_STREAM')
								),
								array(
									'value' => 'p.products_price_download',
									'name'  => sysLanguage::get('PRODUCT_LISTING_PRICE_DOWNLOAD')
								)

		);
		return $selectSortKeys;
	}

	public function show(&$productClass, &$purchaseTypesCol){

		global $currencies;
		$tableRow = array();

		$buyNowButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_BUY_NOW'));
		$purchaseTypesCol = 'new';
		if($productClass->isNotAvailable()){
			$purchaseTypesCol = '';
			$buyNowButton->disable();
			$buyNowButton->setText(sysLanguage::get('TEXT_AVAILABLE').': '. strftime(sysLanguage::getDateFormat('short'), strtotime($productClass->getAvailableDate())));
		}
		$productPurchaseTypes = $productClass->productInfo['typeArr'];
		$purchaseTypes = array();
		if (in_array('used', $productPurchaseTypes)){
			$purchaseTypes['used'] = $productClass->getPurchaseType('used');
		}

		if (in_array('download', $productPurchaseTypes)){
			$purchaseTypes['download'] = $productClass->getPurchaseType('download');
		}

		if (in_array('stream', $productPurchaseTypes)){
			$purchaseTypes['stream'] = $productClass->getPurchaseType('stream');
		}

		if (in_array('new', $productPurchaseTypes)){
			$purchaseTypes['new'] = $productClass->getPurchaseType('new');
		}

		foreach($purchaseTypes as $k => $pType){

			if ($k == 'new' && is_null($pType) === false && $pType->hasInventory()){
				$buyNowButton->setHref(itw_app_link(tep_get_all_get_params(array('action', 'products_id')) . 'action=buy_' . $pType->typeLong . '_product&products_id=' . $productClass->getID()), true);
				if (sizeof($tableRow) <= 0){
					$tableRow[] = '<tr>
    	               <td class="main"' .  sysLanguage::get('BUY_NEW') . ':</td>
    	               <td class="main">' . $pType->displayPrice() . '</td>
    	              </tr>
    	              <tr>
    	               <td class="main" colspan="2">' . $buyNowButton->draw() . '</td>
    	              </tr>';
				}else{
					array_unshift($tableRow, '<tr>
    	               <td class="main"></td>
    	               <td class="main">' . $pType->typeShow . ':</td>
    	               <td class="main">' . $pType->displayPrice() . '</td>
    	               <td class="main" style="font-size:.7em;">' . $buyNowButton->draw() . '</td>
    	              </tr>');
				}
			}elseif (is_null($pType) === false && $pType->hasInventory()){
				$buyNowButton->setHref(itw_app_link(tep_get_all_get_params(array('action', 'products_id')) . 'action=buy_' . $pType->typeLong . '_product&products_id=' . $productClass->getID()), true);
				$purchaseTypeHtml = $pType->getPurchaseHtml('product_listing_row');
				if (is_null($purchaseTypeHtml) === false){
					$tableRow[] = $pType->getPurchaseHtml('product_listing_row');
				}else{
					$tableRow[] = '<tr>
        	   	    <td class="main"></td>
        	   	    <td class="main">' . $pType->typeShow . ':</td>
        	   	    <td class="main">' . $pType->displayPrice() . '</td>
        	   	    <td class="main" style="font-size:.7em;">' . $buyNowButton->draw() . '</td>
        	   	   </tr>';
				}
			}
		}
		ksort($tableRow);

		if (sizeof($tableRow) > 0){
			return '<table cellpadding="2" cellspacing="0" border="0">' .
			implode('', $tableRow) .
			'</table>';
		}
		return false;
	}

}
?>