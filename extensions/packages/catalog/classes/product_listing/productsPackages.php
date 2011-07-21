<?php
class productListing_productsPackages {

   public function sortColumns(){

		return false;
	}

	public function show(&$productClass){
		global $currencies;
		$tableRow = array();
		$purchaseTypeClass = $productClass->getPurchaseType('reservation');

		if (is_null($purchaseTypeClass) === false && sysConfig::get('EXTENSION_PAY_PER_RENTALS_ENABLED') == 'True' && in_array('reservation', $productClass->getPurchaseTypesArray())){

			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_SELECTION') == 'Using calendar after browsing products and clicking Reserve' && $purchaseTypeClass->hasInventory()){
				$payPerRentalButton = htmlBase::newElement('button')
				->setText(sysLanguage::get('TEXT_BUTTON_PAY_PER_RENTAL'))
				->setHref(
					itw_app_link(
						tep_get_all_get_params(array('action', 'products_id')) .
						'action=reserve_now&products_id=' . $productClass->getID()
					),
					true
				);

				EventManager::notify('ProductListingModuleShowBeforeShow', 'reservation', $productClass, &$payPerRentalButton);

				$QPricePerRentalProducts = Doctrine_Query::create()
				->from('PricePerRentalPerProducts pprp')
				->leftJoin('pprp.PricePayPerRentalPerProductsDescription pprpd')
				->where('pprp.pay_per_rental_id =?',$purchaseTypeClass->getId())
				->andWhere('pprpd.language_id =?', Session::get('languages_id'))
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				$i = 1;
				foreach($QPricePerRentalProducts as $iPrices){
				$tableRow[$i] = '<tr>
                                    <td class="main">'.$iPrices['PricePayPerRentalPerProductsDescription'][0]['price_per_rental_per_products_name'].':</td>
                                    <td class="main">' . $purchaseTypeClass->displayReservePrice($iPrices['price']) . '</td>
				                  </tr>';
					$i++;
				}

				if (sizeof($tableRow) > 0){
					$tableRow[0] = '<tr>
					   <td class="main" colspan="2" style="font-size:.8em;" align="center">' .  $payPerRentalButton->draw() . '</td>
					  </tr>';
					ksort($tableRow);
				}
			}
		}

		if (sizeof($tableRow) > 0){
			return '<table cellpadding="2" cellspacing="0" border="0">' .
			implode('', $tableRow) .
			'</table>';
		}
		return false;
	}
}
?>