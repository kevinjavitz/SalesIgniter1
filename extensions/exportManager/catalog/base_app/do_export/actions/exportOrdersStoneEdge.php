<?php
 function ProcessOrderCount()
	{
		$response = '';
		if (isset($_REQUEST['lastorder'])) {
			//either an order number or 'All'
			if ($_REQUEST['lastorder'] == 'All') {
				//return a count of all orders in the database
				$count = StoneEdgeCount('orders','');
				$response = "SetiResponse: ordercount=$count";
			} else {
				//return a count of all orders after the last order passed to SEOM
				//make sure that this isn't a SQL injection attempt.
				if (is_numeric($_REQUEST['lastorder'])) {
					$previousend = $_REQUEST['lastorder'];
				} else {
					//Stone Edge Order Manager will always send lastorder. If we get here, ignore their request and change the last order to 1.
					$previousend = 1;
				}
				$count = StoneEdgeCount('orders','orders_id > '. $previousend);
				$response = "SetiResponse: ordercount=$count";
			}
		} else {
			//either a date in 10/Jun/2003 format or 'All'
			if ($_REQUEST['lastdate'] == 'All') {
				//return a count of all orders in the database
				$count = StoneEdgeCount('orders','');
				$response = "SetiResponse: ordercount=$count";
			} else {
				//return the orders after the last timestamp.
				//convert their date to our date structure
				list($day,$month,$year) = explode('/',$_REQUEST['lastdate']);
				$date = strtotime("$day $month $year");
				//make sure that this isn't a SQL injection attempt.
				if (is_numeric($date)) {
					$count = StoneEdgeCount('orders','date_purchased >= '. $date);
				} else {
					//then something went wrong so return all orders. Always return something.
					$count = StoneEdgeCount('orders','');
				}
				$response = "SetiResponse: ordercount=$count";
			}
		}
		return $response;
	}

	/**
	 * Download orders in the database to SEOM
	 *
	 * Method will process the posted data and create XML to be displayed.
	 *
	 * @access 
	 * @return string XML response to display on the page for orders requested
	 */

	 function DownloadOrders()
	{


		$start = 0;
		$numresults = 0;
		$Qorders = Doctrine_Query::create()
		->select('*')
		->from('Orders o')
		->leftJoin('o.OrdersAddresses oa')
		->leftJoin('oa.Zones z')
		->leftJoin('oa.Countries c')
		->leftJoin('o.OrdersTotal ot')
		->leftJoin('o.OrdersPaymentsHistory oph')
		->leftJoin('o.OrdersStatusHistory osh')
		->leftJoin('osh.OrdersStatus oshs on oshs.orders_status_id = osh.orders_status_id and oshs.language_id = "' . Session::get('languages_id') . '"')
		->leftJoin('o.OrdersStatus os on os.orders_status_id = o.orders_status and os.language_id = "' . Session::get('languages_id') . '"')
		->leftJoin('o.OrdersProducts op')
		->leftJoin('op.OrdersProductsAttributes op_a')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);



		$xml = new SimpleXMLElement('<?xml version="1.0"?><SETIOrders />');

		if (isset($_REQUEST['startnum']) && (int)$_REQUEST['startnum'] > 0 && isset($_REQUEST['batchsize']) && (int)$_REQUEST['batchsize']  > 0) {
			$start = (int)$_REQUEST['startnum'] - 1;
			$numresults = (int)$_REQUEST['batchsize'];
		}

		// should we limit the number of results in this query?
		//$limitQuery = '';
		if($start >= 0 && $numresults > 0) {
			//$limitQuery = ' LIMIT '. $start .', '. $numresults;
			$Qorders->limit($numresults)->offset($start);
		}

		if (isset($_REQUEST['lastorder'])) {
			if (strtolower($_REQUEST['lastorder']) == 'all') {
				$previousend = 0;
			} else {
				$previousend = (int)$_REQUEST['lastorder'];
			}

			// check to see if we should start the query from a particular order number
			if (is_numeric($previousend) && $previousend > 0) {
				$Qorders->andWhere('o.orders_id > ?', $previousend);
				//$lastOrderCondition = 'AND o.orderid > ' . $previousend;
			}
		} else {
			die('No lastorder field received');
		}

		// build our query


		if ($Qorders) {
			//then we have at least one result, so let's build the XML
			//header response
			$responseNode = $xml->addChild('Response');
			$responseNode->addChild('ResponseCode', 1);
			$responseNode->addChild('ResponseDescription', 'Success');
			
			//content
			foreach($Qorders as $row) {
				$orderNode = $xml->addChild('Order');
				$orderNode->addChild('OrderNumber', $row['orders_id']);
				$orderNode->addChild('OrderDate', date('Y-m-d H:i:s', $row['date_purchased']));
				$BillingDetails = $orderNode->addChild('Billing');

				$bila =  $row['OrdersAddresses'];
				foreach($bila as $bil){
					if($bil['address_type'] == 'billing'){
							$BillingDetails->addChild('FullName', $bil['entry_name']);
							if(isset($bil['entry_company']) && $bil['entry_company'] != ''){
								$BillingDetails->addChild('Company', $row['entry_company']);
							}
							//$BillingDetails->addChild('Phone', '');
							$BillingDetails->addChild('Email', $row['customers_email_address']);
							$AddressBilling = $BillingDetails->addChild('Address');
							$AddressBilling->addChild('Street1', $bil['entry_street_address']);
							/*if(isset($row['street2']) && $row[''] != ''){
								$AddressBilling->addChild('Street2', $row['']);
							}*/
							$AddressBilling->addChild('City', $bil['entry_city']);
							$AddressBilling->addChild('State', $bil['entry_state']);
							$AddressBilling->addChild('Code', $bil['entry_postcode']);
							$AddressBilling->addChild('Country', $row['entry_country']);
					}
					if($bil['address_type'] == 'delivery'){
						$ShippingDetails = $orderNode->addChild('Shipping');
						$ShippingDetails->addChild('FullName', $bil['entry_name']);
						if(isset($bil['entry_company']) && $bil['entry_company'] != ''){
							$ShippingDetails->addChild('Company', $bil['entry_company']);
						}
						//$ShippingDetails->addChild('Phone', $row['ordshipphone']);
						$ShippingDetails->addChild('Email', $row['customer_email_address']);
						$AddressShipping = $ShippingDetails->addChild('Address');
						$AddressShipping->addChild('Street1', $bil['entry_street_address']);
						/*if(isset($row['ordshipstreet2']) && $row['ordshipstreet2'] != ''){
							$AddressShipping->addChild('Street2', $row['ordshipstreet2']);
						}*/
						$AddressShipping->addChild('City', $bil['entry_city']);
						$AddressShipping->addChild('State', $bil['entry_state']);//abbreviated state?

						$AddressShipping->addChild('Code', $bil['entry_postcode']);
						$AddressShipping->addChild('Country', $bil['entry_country']);//2 letters country?
					}
				}

				//set fixed variables for later to save some server time
				$OrderID = $row['orders_id'];
				$orders_total = $row['OrdersTotal'];

				foreach($orders_total as $ordert){
					if($ordert['class'] == 'ot_subtotal')
						$OrderSubTotal = $ordert['value'];

					if($ordert['class'] == 'ot_tax'){
						$OrderTaxTotal = $ordert['value'];
					//$OrderTaxRate = $row['ordtaxrate'];
						$OrderTaxName = $ordert['text'];
					}

					if($ordert['class'] == 'ot_shipping'){
						$OrderShippingCost = $ordert['value'];
						$OrderShippingMethod = $ordert['text'];
						$OrderShippingModule = $row['shipping_module'];
					}
					//$OrderHandlingCost = $row['ordhandlingcost'];

					if($ordert['module_type'] == 'ot_total' || $ordert['module_type'] == 'total')
						$OrderTotal = $ordert['value'];
				}

				$order_hist =$row['OrdersStatusHistory'];

				$OrderComments = $order_hist[0]['comments'];//get from orders_status_history

				$order_cust = Doctrine_Core::getTable('OrdersCustomeFieldsToOrders')->findBy('orders_id',$OrderID);

				foreach($order_cust as $orderc){
					$OrderInstructions .= $orderc['field_label']. ': '.$orderc['value'].'\n';//concat custom order fields
				}

				//$OrderIP = $row['ordipaddress'];

				//Time to get product information for this order from order_products and products tables
				$OrdersProducts = $row['OrdersProducts'];
		foreach($OrdersProducts as $pInfo){

			//$productClass = new product($pInfo['products_id'], $pInfo['purchase_type']);


					if ($pInfo['products_tax'] > 0) {
						$taxable = 'Yes';
					} else {
						$taxable = 'No';
					}
					//assign SKU for default product
					$sku = $pInfo['products_id']."-".$pInfo['products_name'];
			
					$prodType = $pInfo['purchase_type'];



					$productNode = $ShippingDetails->addChild('Product');
					$productNode->addChild('SKU', htmlentities($sku));
					
					$ProductName = htmlentities($pInfo['products_name']);
					$productNode->addChild('Name', $ProductName);
					$productNode->addChild('Quantity', $pInfo['products_quantity']);
					$productNode->addChild('ItemPrice', number_format($pInfo['products_price'],2));
					//$productNode->addChild('Weight', number_format($pInfo['ordprodweight'],2));
					$productNode->addChild('ProdType', $prodType);
					$productNode->addChild('Taxable', $taxable); //Yes or No
					$total = number_format($pInfo['final_price'] ,2);
					$productNode->addChild('Total', number_format($total,2));

					/*$dimension = $productNode->addChild('Dimension');
					$dimension->addChild('Length', number_format($prodReturn['prodwidth'],2));
					$dimension->addChild('Width', number_format($prodReturn['proddepth'],2));
					$dimension->addChild('Height', number_format($prodReturn['prodheight'],2));*/

					$varsprod = $pInfo['OrdersProductsAttributes'];
					if ($varsprod) {
						//with this query, the vc fields will be the same every time. The vo fields will be different each time. 1 vc to many vo's.
						foreach ($varsprod as $varReturn) {
								$variationNode = $orderNode->addChild('OrderOption');
								$variationNode->addChild('OptionName', $varReturn['products_options']);
								$variationNode->addChild('SelectedOption',$varReturn['products_options_values']);
								$variationNode->addChild('OptionPrice', $varReturn['options_values_price']);
								//$variationNode->addChild('OptionWeight', 0);
						}
					}


						//check for configurable fields
						/*	$configurableNode = $orderNode->addChild('OrderOption');
							$configurableNode->addChild('OptionName', $fieldName);
							$configurableNode->addChild('SelectedOption', $fieldValue);
						*/

				}


				$OrdersPaymentHistory = $row['OrdersPaymentsHistory'];

		foreach($OrdersPaymentHistory as $phInfo){
			//this should be only one payment
							$paymentNode = $orderNode->addChild('Payment');
							$genericNode = $paymentNode->addChild('Generic1');
							$genericNode->addChild('Name', 'Generic 1');
							$genericNode->addChild('Description', $phInfo['payment_method']);
}
				$totalNode = $orderNode->addChild('Totals');
				$totalNode->addChild('ProductTotal', number_format($OrderSubTotal,2));
				$totalNode->addChild('SubTotal', number_format($OrderSubTotal,2));
				if ($OrderTaxTotal > $OrderSubTotal) {
					$taxNode = $totalNode->addChild('Tax');
					$TaxAmount = $OrderTaxTotal - $OrderSubTotal;
					$taxNode->addChild('TaxAmount', number_format($TaxAmount,2));
				}
				$totalNode->addChild('GrandTotal', number_format($OrderTotal,2));
				$shipTotal = $totalNode->addChild('ShippingTotal');
				$shipTotal->addChild('Total', number_format($OrderShippingCost,2));
				$shipTotal->addChild('Description', $OrderShippingModule . " " . $OrderShippingMethod);

				$otherNode = $orderNode->addChild('Other');
				if ($OrderInstructions != '') {
					$otherNode->addChild('OrderInstructions', $OrderInstructions);
				}
				if ($OrderComments != '') {
					$otherNode->addChild('Comments', $OrderComments);
				}
				//$otherNode->addChild('IpHostname', $OrderIP);

			}
		} else {
			//no results, give a response code of 2 to let SEOM know
			$responseNode = $xml->addChild('Response');
			$responseNode->addChild('ResponseCode', 2);
			$responseNode->addChild('ResponseDescription', 'Success');
		}

		//return $xml->asXML();
		 return $xml->count();
	} //end function DownloadOrders
?>