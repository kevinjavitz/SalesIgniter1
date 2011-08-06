<?php
	if (isset($_POST['products_type']) && (in_array('reservation', $_POST['products_type']))){
		$PayPerRental = $Product->ProductsPayPerRental;

		$PayPerRental->max_period = (int)$_POST['reservation_max_period'];
		$PayPerRental->max_type = (int)$_POST['reservation_max_type'];
		$PayPerRental->deposit_amount = (float)$_POST['reservation_deposit_amount'];
		$PayPerRental->insurance = (float)$_POST['reservation_insurance'];
        $PayPerRental->min_period = (int)$_POST['reservation_min_period'];
		$PayPerRental->min_type = (int)$_POST['reservation_min_type'];


		if (isset($_POST['reservation_price_period'])){
			$Period = Doctrine_Core::getTable('ProductsPayPerPeriods');
			if ($Product->products_id > 0){
				$Period = $Period->findByProductsId($Product->products_id);
				$Period->delete();
			}//else{
			//	$Period = $Period->getRecord();
			//}

			foreach($_POST['reservation_price_period'] as $period => $price){
				$ProductPeriods = new ProductsPayPerPeriods;
				$ProductPeriods->products_id = $Product->products_id;
				$ProductPeriods->period_id = $period;
				$ProductPeriods->price = $price;
				$ProductPeriods->save();
			}
		}
		
		if (isset($_POST['reservation_shipping'])){
			if (is_array($_POST['reservation_shipping'])){
				$PayPerRental->shipping = implode(',', $_POST['reservation_shipping']);
			}else{
				$PayPerRental->shipping = $_POST['reservation_shipping'];
			}
		}
		
		if (isset($_POST['reservation_overbooking'])){
			$PayPerRental->overbooking = (int)$_POST['reservation_overbooking'];
		}else{
			$PayPerRental->overbooking = '0';
		}
		
		$Product->save();

	/*Period Metrics*/
	    $PricePerRentalPerProducts = Doctrine_Core::getTable('PricePerRentalPerProducts');
		$saveArray = array();
		Doctrine_Query::create()
		->delete('PricePerRentalPerProducts')
		->andWhere('pay_per_rental_id =?',$Product->ProductsPayPerRental->pay_per_rental_id)
		->execute();
		if (isset($_POST['pprp'])){
			foreach($_POST['pprp'] as $pprid => $iPrice){

				$PricePerProduct = $PricePerRentalPerProducts->create();
				$Description = $PricePerProduct->PricePayPerRentalPerProductsDescription;
				if(isset($iPrice['details'])){
					foreach($iPrice['details'] as $langId => $Name){
						if (isset($Name) && !empty($Name)){
							$Description[$langId]->language_id = $langId;
							$Description[$langId]->price_per_rental_per_products_name = $Name;
						}
					}
				}

				$PricePerProduct->price = $iPrice['price'];
				$PricePerProduct->number_of = $iPrice['number_of'];
				$PricePerProduct->pay_per_rental_types_id = $iPrice['type'];
				$PricePerProduct->pay_per_rental_id = $Product->ProductsPayPerRental->pay_per_rental_id;
				$PricePerProduct->save();
			}
		}
		/*End Period Metrics*/

	    /*Hidden dates*/
		$PayPerRentalHiddenDatesTable = Doctrine_Core::getTable('PayPerRentalHiddenDates');
	    Doctrine_Query::create()
		->delete('PayPerRentalHiddenDates')
			//->whereNotIn('price_per_rental_per_products_id', $saveArray)
		->andWhere('products_id =?', $Product->products_id)
		->execute();

		if(isset($_POST['pprhidden'])){
			foreach($_POST['pprhidden'] as $hiddenid => $iHidden){
				$PayPerRentalHiddenDates = $PayPerRentalHiddenDatesTable->create();
				$PayPerRentalHiddenDates->hidden_start_date = $iHidden['start_date'];
				$PayPerRentalHiddenDates->hidden_end_date = $iHidden['end_date'];
				$PayPerRentalHiddenDates->products_id = $Product->products_id;
				$PayPerRentalHiddenDates->save();
			}
		}
		/*End Hidden Dates*/
	}
?>