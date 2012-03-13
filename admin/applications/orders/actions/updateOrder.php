<?php
	$status = (int)$_POST['status'];
	$comments = addslashes($_POST['comments']);

	$order_updated = false;
	
	$Qcheck = Doctrine_Query::create()
	->select('orders_status, customers_id')
	->from('Orders')
	->where('orders_id = ?', (int)$_GET['oID'])
	->execute();

	if($status == sysConfig::get('ORDERS_STATUS_CANCELLED_ID')){//cancel order
		$QOrdersQuery = Doctrine_Query::create()
		->from('Orders o')
		->leftJoin('o.OrdersAddresses oa')
		->leftJoin('o.OrdersProducts op')
		->leftJoin('op.OrdersProductsReservation opr')
		->leftJoin('opr.ProductsInventoryBarcodes ib')
		->leftJoin('ib.ProductsInventory ibi')
		->leftJoin('opr.ProductsInventoryQuantity iq')
		->leftJoin('iq.ProductsInventory iqi')
		->where('o.orders_id = ?', $oID)
		->andWhere('oa.address_type = ?', 'customer')
		->andWhere('parent_id IS NULL');

		$Qorders = $QOrdersQuery->execute();
		foreach($Qorders as $oInfo){
			foreach($oInfo->OrdersProducts as $opInfo){
				$productClass = new product($opInfo['products_id']);
				$purchaseClass = $productClass->getPurchaseType($opInfo['purchase_type']); //what happens for rental
				$trackMethod = $purchaseClass->getTrackMethod();
				$invItems = $purchaseClass->getInventoryItems();
				if ($opInfo['purchase_type'] == 'new ' || $opInfo['purchase_type'] == 'used') {
					if (!empty($opInfo['barcode_id']) && $trackMethod == 'barcode') {
						$ProductInventoryBarcodes = Doctrine_Core::getTable('ProductsInventoryBarcodes')->findOneByBarcodeId($opInfo['barcode_id']);
						$ProductInventoryBarcodes->status = 'A';
						$ProductInventoryBarcodes->save();
					} else if ($trackMethod == 'quantity') {
						$invId = $invItems[0]['inventory_id'];
						if (!empty($invId)) {
							$ProductsInventoryQuantity = Doctrine_Core::getTable('ProductsInventoryQuantity')->findOneByInventoryId($invId);
							$ProductsInventoryQuantity->purchased--;
							$ProductsInventoryQuantity->available++;
							$ProductsInventoryQuantity->save();
						}
					}
				}
			}
		}
	}
	if ($Qcheck[0]['orders_status'] != $status || !empty($comments)){
		$userAccount = new rentalStoreUser($Qcheck[0]['customers_id']);
		$userAccount->loadPlugins();
		$addressBook =& $userAccount->plugins['addressBook'];
		$membership =& $userAccount->plugins['membership'];
		
		$Order = Doctrine_Core::getTable('Orders')->findOneByOrdersId((int)$_GET['oID']);

		$newHistory =& $Order->OrdersStatusHistory;
		$idx = $newHistory->count();
		$Order->OrdersStatusHistory[$idx]->orders_status_id = $status;
		$Order->OrdersStatusHistory[$idx]->customer_notified = (isset($_POST['notify']) ? '1' : '0');
		$Order->OrdersStatusHistory[$idx]->comments = $comments;

		$Order->orders_status = $status;
		$Order->usps_track_num = $_POST['usps_track_num'];
		$Order->usps_track_num2 = $_POST['usps_track_num2'];
		$Order->ups_track_num = $_POST['ups_track_num'];
		$Order->ups_track_num2 = $_POST['ups_track_num2'];
		$Order->fedex_track_num = $_POST['fedex_track_num'];
		$Order->fedex_track_num2 = $_POST['fedex_track_num2'];
		$Order->dhl_track_num = $_POST['dhl_track_num'];
		$Order->dhl_track_num2 = $_POST['dhl_track_num2'];

		$Order->save();

		$order_updated = true;
		
		$customer_notified = '0';
		if (isset($_POST['notify'])){
			$adminComments = false;
			//if (isset($_POST['notify_comments']) && !empty($_POST['notify_comments'])) {
				$adminComments = sprintf(sysLanguage::get('EMAIL_TEXT_COMMENTS_UPDATE'), $comments) . "\n\n";
			//}

			if (tep_not_null($Order->usps_track_num) || tep_not_null($Order->usps_track_num2) || tep_not_null($Order->ups_track_num) || tep_not_null($Order->ups_track_num2) || tep_not_null($Order->fedex_track_num) || tep_not_null($Order->fedex_track_num2) || tep_not_null($Order->dhl_track_num) || tep_not_null($Order->dhl_track_num2)){
				$trackingLinks = "\n" . sysLanguage::get('EMAIL_TEXT_TRACKING_NUMBER') . "\n";

				if (tep_not_null($Order->usps_track_num)){
					$trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_USPS1') . ' ' .
					'<a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=' . str_replace(' ', '', $Order->usps_track_num) . '">' . $Order->usps_track_num . '</a>' . "\n";
				}

				if (tep_not_null($Order->usps_track_num2)){
					$trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_USPS2') . ' ' .
					'<a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=' . str_replace(' ', '', $Order->usps_track_num2) . '">' . $Order->usps_track_num2 . '</a>' . "\n";
				}

				if (tep_not_null($Order->ups_track_num)) {
					$trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_UPS1') . ' ' .
					'<a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . str_replace(' ', '', $Order->ups_track_num) . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package">' . $Order->ups_track_num . '</a>' . "\n";
				}

				if (tep_not_null($Order->ups_track_num2)) {
					$trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_UPS2') . ' ' .
					'<a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . str_replace(' ', '', $Order->ups_track_num2) . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package">' . $Order->ups_track_num2 . '</a>' . "\n";
				}

				if (tep_not_null($Order->fedex_track_num)) {
					$trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_FEDEX1') . ' ' .
					'<a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=' . str_replace(' ', '', $Order->fedex_track_num) . '&action=track&language=english&cntry_code=us">' . $Order->fedex_track_num . '</a>' . "\n";
				}

				if (tep_not_null($Order->fedex_track_num2)) {
					$trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_FEDEX2') . ' ' .
					'<a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=' . str_replace(' ', '', $Order->fedex_track_num2) . '&action=track&language=english&cntry_code=us">' . $Order->fedex_track_num2 . '</a>' . "\n";
				}

				if (tep_not_null($Order->dhl_track_num)) {
					$trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_DHL1') . ' ' .
					'<a target="_blank" href="http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=' . str_replace(' ', '', $Order->dhl_track_num) . '&action=track&language=english&cntry_code=us">' . $Order->dhl_track_num . '</a>' . "\n";
				}

				if (tep_not_null($Order->dhl_track_num2)) {
					$trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_DHL2') . ' ' .
					'<a target="_blank" href="http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=' . str_replace(' ', '', $Order->dhl_track_num2) . '&action=track&language=english&cntry_code=us">' . $Order->dhl_track_num2 . '</a>' . "\n";
				}

				$trackingLinks .="\n\n";
			}
			require(sysConfig::getDirFsCatalog(). 'includes/classes/order.php');
			$order = new OrderProcessor($oID);

			$orderedProducts = $order->getProductsOrdered();

			$orderTotals = '';
			for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++){
				$orderTotals .= "\n" . $order->totals[$i]['title'] . "\t" . $order->totals[$i]['text'] . "\t" . "\n";
			}

			$emailEvent = new emailEvent(null, $userAccount->getLanguageId());
			if ($status == 2){
				$emailEvent->setEvent('order_process');
			}else{
				$emailEvent->setEvent('order_update');
			}

			$DatePurchased = new DateTime($Order->date_purchased);
			$emailEvent->setVars(array(
				'full_name' => $userAccount->getFullName(),
				'orderID' => (int)$_GET['oID'],
				'status' => $orders_status_array[$status],
				'datePurchased' => strftime(sysLanguage::getDateFormat('long'), $DatePurchased->getTimestamp()),
				'trackingLinks' => $trackingLinks,
				'adminComments' => $adminComments,
				'historyLink' => false,
				'orderedProducts' => $orderedProducts,
				'orderTotals' => $orderTotals
			));

			$emailEvent->sendEmail(array(
				'email' => $Order->customers_email_address,
				'name' => $userAccount->getFullName()
			));

			$customer_notified = '1';
		}
	}

	if ($order_updated == true){
		$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_ORDER_UPDATED'), 'success');
	}else{
		$messageStack->addSession('pageStack', sysLanguage::get('WARNING_ORDER_NOT_UPDATED'), 'warning');
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action')), null, 'details'), 'redirect');
?>