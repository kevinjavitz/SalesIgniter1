<?php
class productListing_productsPricePayPerRentalPeriods {

   public function sortColumns(){

	    $QPricePerRentalProducts = Doctrine_Query::create()
	    ->from('PricePerRentalPerProducts pprp')
	    ->leftJoin('pprp.PricePayPerRentalPerProductsDescription pprpd')
	    ->where('pprpd.language_id =?', Session::get('languages_id'))
	    ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	    $selectSortKeys = array();

		foreach($QPricePerRentalProducts as $iPrices){
			$sortc =  array(
				'value' => 'ppprp.price',
				'name'  => $iPrices['PricePayPerRentalPerProductsDescription'][0]['price_per_rental_per_products_name']
			);
			$selectSortKeys[] = $sortc;
		}

		return $selectSortKeys;
	}

	public function show(&$productClass, &$purchaseTypesCol){
		global $currencies;
		$tableRow = array();
		$purchaseTypeClass = $productClass->getPurchaseType('reservation');

		if (is_null($purchaseTypeClass) === false && sysConfig::get('EXTENSION_PAY_PER_RENTALS_ENABLED') == 'True' && in_array('reservation', $productClass->getPurchaseTypesArray())){

			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_SELECTION') == 'Using calendar after browsing products and clicking Reserve' && $purchaseTypeClass->hasInventory()){

				ob_start();
				$_GET['products_id'] = $productClass->getID();
				require(sysConfig::getDirFsCatalog() . 'extensions/payPerRentals/catalog/base_app/build_reservation/pages/default.php');

				echo '<script type="text/javascript" src="'.sysConfig::getDirWsCatalog() . 'ext/jQuery/external/fullcalendar/fullcalendar.js'.'"></script>';
				echo '<script type="text/javascript" src="'.sysConfig::getDirWsCatalog() . 'ext/jQuery/external/datepick/jquery.datepick.js'.'"></script>';

				echo '<link rel="stylesheet" type="text/css" href="'.sysConfig::getDirWsCatalog() . 'ext/jQuery/external/fullcalendar/fullcalendar.css'.'"/>';
				echo '<link rel="stylesheet" type="text/css" href="'.sysConfig::getDirWsCatalog() . 'ext/jQuery/external/datepick/css/jquery.datepick.css'.'"/>';

				echo '<script type="text/javascript" src="'.sysConfig::getDirWsCatalog() . 'extensions/payPerRentals/catalog/base_app/build_reservation/javascript/default.js'.'"></script>';
				$pageHtml = ob_get_contents();
				ob_end_clean();

					$tableRow[0] = '<tr>
					   <td class="main" colspan="2" style="font-size:.8em;" align="center">' .  $pageHtml . '</td>
					  </tr>';
					ksort($tableRow);

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