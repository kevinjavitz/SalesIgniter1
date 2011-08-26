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

		return $selectSortKeys;
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
			}elseif (sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_SELECTION') != 'Using calendar after browsing products and clicking Reserve'){
				$isav = false;
				$deleteS = false;
				$isdouble = false;
				if(Session::exists('isppr_inventory_pickup') === false && Session::exists('isppr_city') === true && Session::get('isppr_city') != ''){
					$Qproducts = Doctrine_Query::create()
					->from('ProductsInventoryBarcodes b')
					->leftJoin('b.ProductsInventory i')
					->leftJoin('i.Products p')
					->leftJoin('b.ProductsInventoryBarcodesToInventoryCenters b2c')
					->leftJoin('b2c.ProductsInventoryCenters ic');

					$Qproducts->where('p.products_id=?', $productClass->getID());
					$Qproducts->andWhere('i.use_center = ?', '1');

					if (Session::exists('isppr_continent') === true && Session::get('isppr_continent') != '') {
						$Qproducts->andWhere('ic.inventory_center_continent = ?', Session::get('isppr_continent'));
					}
					if (Session::exists('isppr_country') === true && Session::get('isppr_country') != '') {
						$Qproducts->andWhere('ic.inventory_center_country = ?', Session::get('isppr_country'));
					}
					if (Session::exists('isppr_state') === true && Session::get('isppr_state') != '') {
						$Qproducts->andWhere('ic.inventory_center_state = ?', Session::get('isppr_state'));
					}
					if (Session::exists('isppr_city') === true && Session::get('isppr_city') != '') {
						$Qproducts->andWhere('ic.inventory_center_city = ?', Session::get('isppr_city'));
					}
					$Qproducts = $Qproducts->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					$invCenter = -1;
					//print_r($Qproducts);
					foreach($Qproducts as $iProduct){
						if($invCenter == -1){
							$invCenter = $iProduct['ProductsInventoryBarcodesToInventoryCenters']['ProductsInventoryCenters']['inventory_center_id'];
						}elseif($iProduct['ProductsInventoryBarcodesToInventoryCenters']['ProductsInventoryCenters']['inventory_center_id'] != $invCenter){
							$isdouble = true;
							break;
						}

					}


					if(!$isdouble){
						Session::set('isppr_inventory_pickup', $Qproducts[0]['ProductsInventoryBarcodesToInventoryCenters']['ProductsInventoryCenters']['inventory_center_id']);
						$deleteS = true;
					}
				}
				$hasInventory = $purchaseTypeClass->hasInventory();
				if(Session::exists('isppr_selected') && Session::get('isppr_selected') == true && $hasInventory){
					$start_date = '';
					$end_date = '';
					$event_date = '';
					$event_name = '';
					$event_gate = '';
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
					if (Session::exists('isppr_event_gate')){
						$event_gate = Session::get('isppr_event_gate');
					}
					if (Session::exists('isppr_inventory_pickup')){
						$pickup = Session::get('isppr_inventory_pickup');
					}else{
						//check the inventory center for this one $productClass->getID()
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
					if (Session::exists('isppr_shipping_cost')) {
						$ship_cost = (float) Session::get('isppr_shipping_cost');
					}
					$depositAmount = $purchaseTypeClass->getDepositAmount();
					$thePrice = 0;

					$price = $purchaseTypeClass->getReservationPrice($start_date, $end_date);
					$thePrice += $price['price'];
					if(Session::exists('isppr_event_multiple_dates')){
						$thePrice = 0;
						$datesArr = Session::get('isppr_event_multiple_dates');

						foreach($datesArr as $iDate){
							$price = $purchaseTypeClass->getReservationPrice($iDate, $iDate);
							$thePrice += $price['price'];
						}

					}

					$pricing = $currencies->format($qtyVal * $thePrice - $qtyVal * $depositAmount + $ship_cost);

					$pageForm = htmlBase::newElement('form')
					->attr('name', 'build_reservation')
					->attr('action', itw_app_link('appExt=payPerRentals&products_id=' . $productClass->getID(), 'build_reservation', 'default'))
					->attr('method', 'post');

					if (isset($start_date)) {
						$htmlStartDate = htmlBase::newElement('input')
						->setType('hidden')
						->setName('start_date')
						->setValue($start_date);
					}

					if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True') {
						$htmlEventDate = htmlBase::newElement('input')
						->setType('hidden')
						->setName('event_date')
						->setValue($event_date);
						$htmlEventName = htmlBase::newElement('input')
						->setType('hidden')
						->setName('event_name')
						->setValue($event_name);
						if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True') {
							$htmlEventGates = htmlBase::newElement('input')
							->setType('hidden')
							->setName('event_gate')
							->setValue($event_gate);
						}
					}
					if (isset($pickup)) {
						$htmlPickup = htmlBase::newElement('input')
						->setType('hidden')
						->setName('pickup')
						->setValue($pickup);
					}
					if (isset($dropoff)) {
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
					if (isset($end_date)) {
						$htmlEndDate = htmlBase::newElement('input')
								->setType('hidden')
								->setName('end_date')
								->setValue($end_date);
					}

					if (isset($htmlStartDate)) {
						$pageForm->append($htmlStartDate);
					}
					if (isset($htmlEndDate)) {
						$pageForm->append($htmlEndDate);
					}
					if (isset($htmlPickup)) {
						$pageForm->append($htmlPickup);
					}
					if (isset($htmlDropoff)) {
						$pageForm->append($htmlDropoff);
					}
					$pageForm->append($htmlRentalQty);
					$pageForm->append($htmlProductsId);
					$pageForm->append($payPerRentalButton);

					if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True') {
						$pageForm->append($htmlEventDate)
						->append($htmlEventName);
						if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True') {
							$pageForm->append($htmlEventGates);
						}
					}

					if (Session::exists('isppr_shipping_method')) {
						$htmlShippingDays = htmlBase::newElement('input')
						->setType('hidden')
						->setName('rental_shipping')
						->setValue("zonereservation_" . Session::get('isppr_shipping_method'));
						$pageForm->append($htmlShippingDays);
					}

					$tableRow[1] = '<tr>
									<td class="main"><nobr>Price:</nobr></td>
									<td class="main">' . $pricing . '</td>
								</tr>';
					if (sizeof($tableRow) > 0){
						$tableRow[0] = '<tr>
						   <td class="main" colspan="2" style="font-size:.8em;" align="center">' .  $pageForm->draw() . '</td>
						  </tr>';
						ksort($tableRow);
					}
				}
				if($isdouble){
					unset($tableRow);
					$isav = true;
					$start_date = '';
					$end_date = '';
					$ship_cost = 0;
					$depositAmount = 0;
					if (Session::exists('isppr_date_start')){
						$start_date = Session::get('isppr_date_start');
					}
					if (Session::exists('isppr_date_end')){
						$end_date = Session::get('isppr_date_end');
					}
					if (Session::exists('isppr_shipping_cost')) {
						$ship_cost = (float) Session::get('isppr_shipping_cost');
					}
					if (Session::exists('isppr_product_qty')){
						$qtyVal = (int)Session::get('isppr_product_qty');
					}else{
						$qtyVal = 1;
					}
					if($start_date != '' && $end_date != ''){
						$depositAmount = $purchaseTypeClass->getDepositAmount();
						$thePrice = 0;

						$price = $purchaseTypeClass->getReservationPrice($start_date, $end_date);
						$thePrice += $price['price'];
						if(Session::exists('isppr_event_multiple_dates')){
							$thePrice = 0;
							$datesArr = Session::get('isppr_event_multiple_dates');

							foreach($datesArr as $iDate){
								$price = $purchaseTypeClass->getReservationPrice($iDate, $iDate);
								$thePrice += $price['price'];
							}

						}


						$pricing = $currencies->format($qtyVal * $thePrice - $qtyVal * $depositAmount + $ship_cost);
						$tableRow[1] = '<tr>
									<td class="main"><nobr>Price:</nobr></td>
									<td class="main">' . $pricing . '</td>
								</tr>';
					}
					$payPerRentalButton = htmlBase::newElement('button')
					->setType('submit')
					->setText(sysLanguage::get('TEXT_BUTTON_RESERVE'))
					->setId('doubleDatesSelected')
					->setName('double_dates_selected');

					$tableRow[0] = '<tr>
					   <td class="main" colspan="2" style="font-size:.8em;" align="center">' .  $payPerRentalButton->draw() . '</td>
					  </tr>';
					ksort($tableRow);
				}
				if($deleteS){
					//Session::remove('isppr_selected');
					Session::remove('isppr_inventory_pickup');
				}
				if(!$isav){
					$payPerRentalButton = htmlBase::newElement('button')
					->setType('submit')
					->setText(sysLanguage::get('TEXT_BUTTON_RESERVE'));

					if($hasInventory){
						$payPerRentalButton->setId('noDatesSelected')
						->setName('no_dates_selected');
					}else{
						$payPerRentalButton->setId('noInventory')
						->setName('no_inventory');
					}

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