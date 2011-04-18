<?php
class productListing_productsPricePayPerRental {

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

		/*$selectSortKeys = array(
							    array(
									'value' => 'pppr.price_oneh',
									'name'  => sysLanguage::get('PPR_ONEH_PRICE')
								),
								array(
									'value' => 'pppr.price_twoh',
									'name'  => sysLanguage::get('PPR_TWOH_PRICE')
								),
								array(
									'value' => 'pppr.price_fourh',
									'name'  => sysLanguage::get('PPR_FOURH_PRICE')
								),
								array(
									'value' => 'pppr.price_daily',
									'name'  => sysLanguage::get('PPR_DAILY_PRICE')
								),
								array(
									'value' => 'pppr.price_weekly',
									'name'  => sysLanguage::get('PPR_WEEKLY_PRICE')
								),
								array(
									'value' => 'pppr.price_monthly',
									'name'  => sysLanguage::get('PPR_MONTHLY_PRICE')
								),
								array(
									'value' => 'pppr.price_six_month',
									'name'  => sysLanguage::get('PPR_6_MONTHS_PRICE')
								),
								array(
									'value' => 'pppr.price_year',
									'name'  => sysLanguage::get('PPR_1_YEAR_PRICE')
								),
								array(
									'value' => 'pppr.price_three_year',
									'name'  => sysLanguage::get('PPR_3_YEAR_PRICE')
								)

		);  */

		return $selectSortKeys;
	}

	public function show(&$productClass){
		global $currencies;
		$tableRow = array();
		$purchaseTypeClass = $productClass->getPurchaseType('reservation');
		if (is_null($purchaseTypeClass) === false && sysConfig::get('EXTENSION_PAY_PER_RENTALS_ENABLED') == 'True' && $purchaseTypeClass->hasInventory()){

			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_SELECTION') == 'Using calendar after browsing products and clicking Reserve'){
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
			}else{
				$isav = false;
				if(Session::exists('isppr_selected') && Session::get('isppr_selected') == true){
					$start_date = '';
					$end_date = '';
					$event_date = '';
					$event_name = '';
					$pickup = '';
					$dropoff = '';
					if (Session::exists('isppr_date_start')){
						$start_date = Session::get('isppr_date_start');
					}
					if (Session::exists('isppr_date_end')){
						$end_date = Session::get('isppr_date_end');
					}
					if (Session::exists('isppr_event_date')){
						$event_date = Session::get('isppr_event_date');
					}
					if (Session::exists('isppr_event_name')){
						$event_name = Session::get('isppr_event_name');
					}
					if (Session::exists('isppr_inventory_pickup')){
						$pickup = Session::get('isppr_inventory_pickup');
					}
					if (Session::exists('isppr_inventory_dropoff')){
						$dropoff = Session::get('isppr_inventory_dropoff');
					}
					if (Session::exists('isppr_product_qty')){
						$qtyVal = (int)Session::get('isppr_product_qty');
					}else{
						$qtyVal = 1;
					}

					$payPerRentalButton = htmlBase::newElement('button')
					->setType('submit')
					->setText(sysLanguage::get('TEXT_BUTTON_RESERVE'))
					->setId('inCart')
					->setName('add_reservation_product');
					$isav = true;
					if($purchaseTypeClass->hasInventory()){
						if(Session::exists('isppr_shipping_cost')){
							$ship_cost = (float)Session::get('isppr_shipping_cost');
						}
						$depositAmount= $purchaseTypeClass->getDepositAmount();
						$price = $purchaseTypeClass->getReservationPrice($start_date, $end_date);
						//$price['price'] = $price['price'] - $depositAmount;//exclude deposit amount on product listing
						$pricing = $currencies->format($qtyVal*$price['price'] - $qtyVal*$depositAmount + $ship_cost);

						$pageForm = htmlBase::newElement('form')
						->attr('name', 'build_reservation')
						->attr('action', itw_app_link('appExt=payPerRentals&products_id='. $productClass->getID(), 'build_reservation', 'default'))
						->attr('method', 'post');

						if (isset($start_date)){
							$htmlStartDate = htmlBase::newElement('input')
							->setType('hidden')
							->setName('start_date')
							->setValue($start_date);
						}

						if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
							$htmlEventDate = htmlBase::newElement('input')
							->setType('hidden')
							->setName('event_date')
							->setValue($event_date);
							$htmlEventName = htmlBase::newElement('input')
							->setType('hidden')
							->setName('event_name')
							->setValue($event_name);
						}
						if (isset($pickup)){
							$htmlPickup = htmlBase::newElement('input')
							->setType('hidden')
							->setName('pickup')
							->setValue($pickup);
						}
						if (isset($dropoff)){
							$htmlDropoff = htmlBase::newElement('input')
							->setType('hidden')
							->setName('dropoff')
							->setValue($dropoff);
						}
						$htmlRentalQty = htmlBase::newElement('input')
						->setType('hidden')
						->setName('rental_qty')
						->setValue($qtyVal);
						$htmlProductsId = htmlBase::newElement('input')
						->setType('hidden')
						->setName('products_id')
						->setValue($productClass->getID());
						if (isset($end_date)){
							$htmlEndDate = htmlBase::newElement('input')
							->setType('hidden')
							->setName('end_date')
							->setValue($end_date);
						}

						if (isset($htmlStartDate)){
							$pageForm->append($htmlStartDate);
						}
						if (isset($htmlEndDate)){
							 $pageForm->append($htmlEndDate);
						}
						if (isset($htmlPickup)){
							 $pageForm->append($htmlPickup);
						}
						if (isset($htmlDropoff)){
							$pageForm->append($htmlDropoff);
						}
						$pageForm->append($htmlRentalQty);
						$pageForm->append($htmlProductsId);
						$pageForm->append($payPerRentalButton);

						if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
							$pageForm->append($htmlEventDate)
							->append($htmlEventName);
						}

						if (Session::exists('isppr_shipping_method')){
							$htmlShippingDays = htmlBase::newElement('input')
							->setType('hidden')
							->setName('rental_shipping')
							->setValue("zonereservation_" . Session::get('isppr_shipping_method'));
							$pageForm->append($htmlShippingDays);
						}

						$tableRow[1] = '<tr>
									<td class="main"><nobr>Price:</nobr></td>
									<td class="main">' . $pricing  . '</td>
								</tr>';
					}

					if (sizeof($tableRow) > 0){
						$tableRow[0] = '<tr>
						   <td class="main" colspan="2" style="font-size:.8em;" align="center">' .  $pageForm->draw() . '</td>
						  </tr>';
						ksort($tableRow);
					}
				}

				if(!$isav){
					$payPerRentalButton = htmlBase::newElement('button')->setType('submit')->setText(sysLanguage::get('TEXT_BUTTON_RESERVE'))->setId('noDatesSelected')->setName('no_dates_selected');

					EventManager::notify('ProductListingModuleShowBeforeShow', 'reservation', $productClass, &$payPerRentalButton);

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