<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class payPerRentals_admin_data_manager_default extends Extension_payPerRentals {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		global $appExtension;
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'DataExportFullQueryBeforeExecute',
			'DataExportFullQueryFileLayoutHeader',
			'DataExportBeforeFileLineCommit',
			'DataImportBeforeSave',
			'DataImportAfterSave',
			'DataImportProductLogBeforeExecute',
		), null, $this);
	}
	
	public function DataImportProductLogBeforeExecute(&$Product, &$productLogArr){
		$productLogArr = array_merge($productLogArr, array(
			'Pay Per Rental Price Daily:'         => $Product->ProductsPayPerRental->price_daily,
			'Pay Per Rental Price Weekly:'        => $Product->ProductsPayPerRental->price_weekly,
			'Pay Per Rental Price Monthly:'       => $Product->ProductsPayPerRental->price_monthly,
			'Pay Per Rental Price 6 Month:'       => $Product->ProductsPayPerRental->price_six_month,
			'Pay Per Rental Price Year:'          => $Product->ProductsPayPerRental->price_year,
			'Pay Per Rental Price 3 Year:'        => $Product->ProductsPayPerRental->price_three_year,
			//'Pay Per Rental Auth Method:'         => $Product->products_auth_method,
			'Pay Per Rental Insurance:'           => $Product->ProductsPayPerRental->insurance,
			'Pay Per Rental Deposit Amount:'      => $Product->ProductsPayPerRental->deposit_amount,
			'Pay Per Rental Shipping Methods:'    => $Product->ProductsPayPerRental->shipping,
			'Pay Per Rental Max Days:'            => $Product->ProductsPayPerRental->max_days,
			'Pay Per Rental Max Months:'          => $Product->ProductsPayPerRental->max_months,
			'Pay Per Rental Overbooking Allowed:' => $Product->ProductsPayPerRental->overbooking,
		));

	}
	
	public function DataExportFullQueryBeforeExecute(&$query){
		$query->leftJoin('p.ProductsPayPerRental ppr')
//		->addSelect('ppr.price_daily as v_pay_per_rental_price_daily')
//		->addSelect('ppr.price_weekly as v_pay_per_rental_price_weekly')
//		->addSelect('ppr.price_monthly as v_pay_per_rental_price_monthly')
//		->addSelect('ppr.price_six_month as v_pay_per_rental_price_six_month')
//		->addSelect('ppr.price_year as v_pay_per_rental_price_year')
//		->addSelect('ppr.price_three_year as v_pay_per_rental_price_three_year')
//		->addSelect('p.products_auth_method as v_pay_per_rental_auth_method')
//		->addSelect('p.products_auth_charge as v_pay_per_rental_auth_charge')
		->addSelect('ppr.shipping as v_pay_per_rental_shipping')
		->addSelect('ppr.max_days as v_pay_per_rental_max_days')
		->addSelect('ppr.max_months as v_pay_per_rental_max_months')
		->addSelect('ppr.overbooking as v_pay_per_rental_overbooking')
		->addSelect('ppr.insurance as v_pay_per_rental_insurance')
		->addSelect('ppr.deposit_amount as v_pay_per_rental_deposit_amount');

	}
	
	public function DataExportFullQueryFileLayoutHeader(&$dataExport){

		/*export hidden dates*/
		$QHiddenDatesMAX = Doctrine_Query::create()
			->select('COUNT(*) as hiddenmax')
			->from('PayPerRentalHiddenDates')
			->groupby('products_id')
			//->where('pay_per_rental_id =?',$Product['ProductsPayPerRental']['pay_per_rental_id'])
			//->orderBy('price_per_rental_per_products_id')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$maxVal = -1;
		foreach($QHiddenDatesMAX as $iMax){
			if($iMax['hiddenmax'] > $maxVal){
				$maxVal = $iMax['hiddenmax'];
			}
		}

		for($j=0;$j<$maxVal;$j++){
			$dataExport->setHeaders(array(
				'v_pay_per_rental_hidden_start_date_'. $j,
				'v_pay_per_rental_hidden_end_date_'. $j
			));
		}
		/*end of export*/

		$QPricePerRentalProductsMAX = Doctrine_Query::create()
			->select('COUNT(*) as pprmax')
			->from('PricePerRentalPerProducts')
			->where('pay_per_rental_id > 0')
			->groupBy('pay_per_rental_id')
			//->where('pay_per_rental_id =?',$Product['ProductsPayPerRental']['pay_per_rental_id'])
			//->orderBy('price_per_rental_per_products_id')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$maxVal = -1;
		foreach($QPricePerRentalProductsMAX as $iMax){
			if($iMax['pprmax'] > $maxVal){
				$maxVal = $iMax['pprmax'];
			}
		}

		for($j=0;$j<$maxVal;$j++){
			$dataExport->setHeaders(array(
				'v_pay_per_rental_time_period_number_of_'. $j,
				'v_pay_per_rental_time_period_type_name_'. $j,
				'v_pay_per_rental_time_period_price_'. $j
			));
			foreach(sysLanguage::getLanguages() as $lInfo){
				$dataExport->setHeaders(array(
					'v_pay_per_rental_time_period_desc_'.$lInfo['id'].'_'. $j
				));
			}
		}

		$dataExport->setHeaders(array(
			//'v_pay_per_rental_price_daily',
			//'v_pay_per_rental_price_weekly',
			//'v_pay_per_rental_price_monthly',
			//'v_pay_per_rental_price_six_month',
			//'v_pay_per_rental_price_year',
			//'v_pay_per_rental_price_three_year',
			'v_pay_per_rental_deposit_amount',
//			'v_pay_per_rental_auth_method',
//			'v_pay_per_rental_auth_charge',
			'v_pay_per_rental_shipping',
			'v_pay_per_rental_max_days',
			'v_pay_per_rental_max_months',
			'v_pay_per_rental_insurance',
			'v_pay_per_rental_overbooking'
		));
		$QPeriods = Doctrine_Query::create()
		->from('PayPerRentalPeriods')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$i = 0;
		foreach($QPeriods as $iPeriod){
			$dataExport->setHeaders(array(
				'v_pay_per_rental_period_'. $i,
				'v_pay_per_rental_period_price_'. $i
			));
			$i++;
		}

	}
	
	public function DataExportBeforeFileLineCommit(&$productRow){
		if ($productRow['v_pay_per_rental_overbooking'] == '0'){
			$productRow['v_pay_per_rental_overbooking'] = 'No';
		}else{
			$productRow['v_pay_per_rental_overbooking'] = 'Yes';
		}
		$product_id = $productRow['products_id'];

		/*export hidden dates*/
		$QHiddsenDates = Doctrine_Query::create()
		->from('PayPerRentalHiddenDates')
		->where('products_id=?', $product_id)
		->execute(array(),  Doctrine_Core::HYDRATE_ARRAY);
		$j = 0;
		foreach($QHiddsenDates as $iHidden){
			$productRow['v_pay_per_rental_hidden_start_date_'.$j] = $iHidden['hidden_start_date'];
			$productRow['v_pay_per_rental_hidden_end_date_'.$j] = $iHidden['hidden_end_date'];
			$j++;
		}

		/*end export hidden dates*/

		$QPPR = Doctrine_Query::create()
		->from('ProductsPayPerRental pprp')
		->where('products_id=?', $product_id)
		->execute(array(),  Doctrine_Core::HYDRATE_ARRAY);

		if(isset($QPPR[0]['pay_per_rental_id'])){
			$QPricePerRentalProducts = Doctrine_Query::create()
			->from('PricePerRentalPerProducts pprp')
			->leftJoin('pprp.PricePayPerRentalPerProductsDescription pprpd')
			->where('pay_per_rental_id =?',$QPPR[0]['pay_per_rental_id'])
			->orderBy('price_per_rental_per_products_id')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$j=0;
			$QPayPerRentalTypes = Doctrine_Query::create()
			->from('PayPerRentalTypes')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$htypes = array();
			foreach($QPayPerRentalTypes as $iType){
				$htypes[$iType['pay_per_rental_types_id']] = $iType['pay_per_rental_types_name'];
			}
			foreach($QPricePerRentalProducts as $iPrice){
				$productRow['v_pay_per_rental_time_period_number_of_'.$j] = $iPrice['number_of'];
				$productRow['v_pay_per_rental_time_period_type_name_'.$j] = $htypes[$iPrice['pay_per_rental_types_id']];
				$productRow['v_pay_per_rental_time_period_price_'.$j] = $iPrice['price'];
				foreach(sysLanguage::getLanguages() as $lInfo){

					foreach($iPrice['PricePayPerRentalPerProductsDescription'] as $desc){
						if($lInfo['id'] == $desc['language_id']){
							$productRow['v_pay_per_rental_time_period_desc_'.$lInfo['id'].'_'. $j] = $desc['price_per_rental_per_products_name'];
						}
					}
				}
				$j++;
			}
		}


		$QPeriods = Doctrine_Query::create()
		->from('ProductsPayPerPeriods')
		->where('products_id=?', $product_id)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$QPeriodsNames = Doctrine_Query::create()
		->from('PayPerRentalPeriods')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$periodNames =  array();
		foreach($QPeriodsNames as $iPeriod){
			$periodNames[$iPeriod['period_id']] = $iPeriod['period_name'];
		}

		$i = 0;
		foreach($QPeriods as $iPeriod){
			$productRow['v_pay_per_rental_period_'.$i] = $periodNames[$iPeriod['period_id']];
			$productRow['v_pay_per_rental_period_price_'.$i] = $iPeriod['price'];
			$i++;
		}
		/*while($i < count($periodNames)){

		}*/
	}
	
	public function DataImportBeforeSave(&$items, &$Product){
		$depositAmount = (isset($items['v_pay_per_rental_deposit_amount']) ? $items['v_pay_per_rental_deposit_amount'] : false);
		$insurance = (isset($items['v_pay_per_rental_insurance']) ? $items['v_pay_per_rental_insurance'] : false);
		$shippingMethods = (isset($items['v_pay_per_rental_shipping']) ? $items['v_pay_per_rental_shipping'] : false);

		$PayPerRental =& $Product->ProductsPayPerRental;
		if (isset($items['v_pay_per_rental_overbooking'])) {
			if ($items['v_pay_per_rental_overbooking'] == 'No') {
				$PayPerRental->overbooking = '0';
			} else {
				$PayPerRental->overbooking = '1';
			}
		} else {
			$PayPerRental->overbooking = '0';
		}

		$Product->products_auth_method = (
		isset($items['v_pay_per_rental_auth_method'])
				? $items['v_pay_per_rental_auth_method']
				: 'auth'
		);

		$Product->products_auth_charge = (
		isset($items['v_pay_per_rental_auth_charge'])
				? $items['v_pay_per_rental_auth_charge']
				: '0.0000'
		);

		$PayPerRental->deposit_amount = (float) ($depositAmount !== false ? $depositAmount : '0');
		$PayPerRental->insurance = (float) ($insurance !== false ? $insurance : '0');
		$PayPerRental->shipping = $shippingMethods !== false ? $shippingMethods : '';
		$PayPerRental->save();

		/*import hidden dates*/
		Doctrine_Query::create()
		->delete('PayPerRentalHiddenDates')
		->andWhere('products_id =?', $Product->products_id)
		->execute();
		$j = 0;
		$PayPerRentalHiddenDatesTable = Doctrine_Core::getTable('PayPerRentalHiddenDates');
		while(true){
			if(isset($items['v_pay_per_rental_hidden_start_date_'.$j])){
				if(!empty($items['v_pay_per_rental_hidden_start_date_'.$j])){
					$PayPerRentalHiddenDates = $PayPerRentalHiddenDatesTable->create();
					$PayPerRentalHiddenDates->hidden_start_date = date('Y-m-d', strtotime($items['v_pay_per_rental_hidden_start_date_'.$j]));
					$PayPerRentalHiddenDates->hidden_end_date = date('Y-m-d', strtotime($items['v_pay_per_rental_hidden_end_date_'.$j]));
					$PayPerRentalHiddenDates->products_id = $Product->products_id;
					$PayPerRentalHiddenDates->save();
				}
			}else{
				break;
			}
			$j++;
		}
		/*end import hidden dates*/
	    $i = 0;
		while (true) {

			if (isset($items['v_pay_per_rental_period_' . $i])) {
				if (!empty($items['v_pay_per_rental_period_' . $i])) {
					$Periods = Doctrine_Core::getTable('PayPerRentalPeriods');
					$PeriodPrices = Doctrine_Core::getTable('ProductsPayPerPeriods');
					$Period = $Periods->findOneByPeriodName($items['v_pay_per_rental_period_' . $i]);
					if (!$Period) {
						$Period = $Periods->getRecord();
						$Period->period_name = $items['v_pay_per_rental_period_' . $i];
						$Period->save();
						$PeriodPrice = $PeriodPrices->getRecord();
					} else {
						$PeriodPrice = $PeriodPrices->findOneByPeriodIdAndProductsId($Period->period_id, $Product->products_id);
						if (!$PeriodPrice) {
							$PeriodPrice = $PeriodPrices->getRecord();
						}
					}
					$PeriodPrice->products_id = $Product->products_id;
					$PeriodPrice->period_id = $Period->period_id;
					$PeriodPrice->price = $items['v_pay_per_rental_period_price_' . $i];
					$PeriodPrice->save();
				}
			} else {
				break;
			}
			$i++;
		}


	}
	public function DataImportAfterSave(&$items, &$PayPerRental){
		$j=0;
		$PricePerRentalPerProducts = Doctrine_Core::getTable('PricePerRentalPerProducts');
		Doctrine_Query::create()
		->delete('PricePerRentalPerProducts')
		->andWhere('pay_per_rental_id =?', $PayPerRental->pay_per_rental_id)
		->execute();
		$QPayPerRentalTypes = Doctrine_Query::create()
		->from('PayPerRentalTypes')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$htypes = array();
		foreach ($QPayPerRentalTypes as $iType) {
			$htypes[$iType['pay_per_rental_types_id']] = $iType['pay_per_rental_types_name'];
		}

		while (true) {
			if (isset($items['v_pay_per_rental_time_period_number_of_' . $j])) {
				if (!empty($items['v_pay_per_rental_time_period_number_of_' . $j])) {

					$PricePerProduct = $PricePerRentalPerProducts->create();
					$Description = $PricePerProduct->PricePayPerRentalPerProductsDescription;

					foreach (sysLanguage::getLanguages() as $lInfo) {
						$langId = $lInfo['id'];
						if (isset($items['v_pay_per_rental_time_period_desc_' . $langId . '_' . $j]) && !empty($items['v_pay_per_rental_time_period_desc_' . $langId . '_' . $j])) {
							$Description[$langId]->language_id = $langId;
							$Description[$langId]->price_per_rental_per_products_name = $items['v_pay_per_rental_time_period_desc_' . $langId . '_' . $j];
						}
					}

					$type = '';
					foreach ($htypes as $itypeID => $itypeName) {
						if ($itypeName == $items['v_pay_per_rental_time_period_type_name_' . $j]) {
							$type = $itypeID;
							break;
						}
					}

					$PricePerProduct->price = $items['v_pay_per_rental_time_period_price_' . $j];
					$PricePerProduct->number_of = $items['v_pay_per_rental_time_period_number_of_' . $j];
					$PricePerProduct->pay_per_rental_types_id = $type;
					$PricePerProduct->pay_per_rental_id = $PayPerRental->pay_per_rental_id;
					$PricePerProduct->save();
					$PricePerProduct->free();
					unset($PricePerProduct);
				}
			} else {
				break;
			}
			$j++;
		}

	}
}
?>