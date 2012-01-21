<?php
class productListing_productsPricePayPerRentalRates {

   public function sortColumns(){
		$selectSortKeys = array();
	    $QPricePerRentalProducts = Doctrine_Query::create()
		->from('PricePerRentalPerProducts pprp')
		->leftJoin('pprp.PricePayPerRentalPerProductsDescription pprpd')
		->where('pprpd.language_id =?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		foreach($QPricePerRentalProducts as $iPrices){
			$sortc =  array(
							'value' => 'ppprp.price',
							'name'  => $iPrices['PricePayPerRentalPerProductsDescription'][0]['price_per_rental_per_products_name']
								);
			$selectSortKeys[] = $sortc;
		}
		return $selectSortKeys;
	}
	public function show(&$productClass){
		global $currencies;
		$tableRow = array();
		$purchaseTypeClass = $productClass->getPurchaseType('reservation');

		if (is_null($purchaseTypeClass) === false && sysConfig::get('EXTENSION_PAY_PER_RENTALS_ENABLED') == 'True'){

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