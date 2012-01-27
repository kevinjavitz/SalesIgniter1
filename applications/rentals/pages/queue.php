<?php
/*
	SalesIgniter E-Commerce System v1

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2010 I.T. Web Experts

	This script and it's source is not redistributable
*/

//here i check for parent
	if (!$userAccount->isLoggedIn()){
		tep_redirect(itw_app_link(null,'account','default'));
	}else{
		$pageContents = '';
		
		$Qshipped = Doctrine_Query::create()
		->from('RentedQueue r')
		->where('r.return_date <= ?', '0000-00-00 00:00:00')
		->andWhere('r.customers_id = ?', $userAccount->getCustomerId())
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$info_box_contents = array();
		$info_box_contents[0][] = array(
			'align' => 'left',
			'params' => 'class="ui-widget-header"',
			'text' => sysLanguage::get('TABLE_HEADING_TITLE')
		);

		$info_box_contents[0][] = array(
			'align' => 'left',
			'params' => 'class="ui-widget-header"',
			'text' => sysLanguage::get('TABLE_HEADING_SHIPMENT_DATE')
		);

		$info_box_contents[0][] = array(
			'align' => 'left',
			'params' => 'class="ui-widget-header"',
			'text' => sysLanguage::get('TABLE_HEADING_ARRIVAL_DATE')
		);

	$QPickupRequests = Doctrine_Query::create()
		->from('PickupRequests pr')
		->leftJoin('pr.PickupRequestsTypes prt')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	if(count($QPickupRequests) > 0){
		/*$info_box_contents[0][] = array(
			   'align' => 'left',
			   'params' => 'class="ui-widget-header"',
			   'text' => sysLanguage::get('TABLE_HEADING_PICKUP_REQUEST')
		   );*/
		$updatePickup = htmlBase::newElement('button')
			->setType('submit')
			->addClass('updatePickupButton')
			->setText(sysLanguage::get('TEXT_BUTTON_UPDATE_PICKUP_REQUESTS'));
	}

	$i = 1;
	$p = 1;
	if ($Qshipped){
		foreach($Qshipped as $sInfo){
			if ($i % 2 == 1) {
				$info_box_contents[] = array('params' => 'class="productListing-even"');
			}else{
				$info_box_contents[] = array('params' => 'class="productListing-odd"');
			}

			$productsName = '<a target="_blank" href="'.itw_app_link('products_id='.$sInfo['products_id'],'product','info').'">'.tep_get_products_name($sInfo['products_id']).'</a>';
			$Product = new product($sInfo['products_id']);
			if($p == 1 && $Product->getKeepPrice() > 0){
				$info_box_contents[0][] = array(
					'align' => 'left',
					'params' => 'class="ui-widget-header"',
					'text' => sysLanguage::get('TABLE_HEADING_KEEP_IT')
				);
				$p++;
			}
			$shipDate = $sInfo['shipment_date'];
			$shipDateString = date("m/d/Y", strtotime($shipDate));
			$arrivalDate = $sInfo['arrival_date'];
			$arrivalDateString = date("m/d/Y", strtotime($arrivalDate));

				$info_box_contents[$i][] = array(
					'params' => 'class="productListing-data"',
					'text' => $productsName
				);

				$info_box_contents[$i][] = array(
					'align' => 'left',
					'params' => 'class="productListing-data" valign="top"',
					'text' => $shipDateString
				);

			$info_box_contents[$i][] = array(
				'align' => 'left',
				'params' => 'class="productListing-data" valign="top"',
				'text' => $arrivalDateString
			);

			$keepCol = '';
			if($Product->getKeepPrice() > 0){
				$buyNowButton = htmlBase::newElement('button')
					->setText(sysLanguage::get('TEXT_BUTTON_BUY_NOW'))
					->setHref(itw_app_link(tep_get_all_get_params(array('action', 'products_id')) . 'action=keepIt&products_id=' .$sInfo['products_id'].'&customers_queue_id='.$sInfo['customers_queue_id'] ), true);
				$keepCol = 'Price: ' . $currencies->format($Product->getKeepPrice()) .'&nbsp;'. $buyNowButton->draw();
			}

			//if($p > 1){
			$info_box_contents[$i][] = array(
				'align' => 'left',
				'params' => 'class="productListing-data" valign="top"',
				'text' => $keepCol
			);
			//}



			$i++;

		}
	}
	$shippedProducts = $i - 1;

	if(count($QPickupRequests) > 0){
		$QCustomersToPickupRequest = Doctrine_Query::create()
			->from('PickupRequests pr')
			->leftJoin('pr.PickupRequestsTypes prt')
			->leftJoin('pr.CustomersToPickupRequests rptpr')
			//->where('pr.start_date <= now()')
			->andWhere('rptpr.customers_id = ?', $userAccount->getCustomerId())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$pickupReqCol = htmlBase::newElement('selectbox')
		//->setName('pickupRequest['.$sInfo['customers_queue_id'].']');
		->setName('pickupRequest')
		->setId('pickupRequest');
		$pickupReqCol->addOption('-1','Select Pickup Request');
		if(count($QCustomersToPickupRequest) > 0){
			$pickupReqCol->selectOptionByValue($QCustomersToPickupRequest[0]['pickup_requests_id']);
		}
		foreach($QPickupRequests as $req){
			if(strtotime($req['start_date']) >= strtotime('+'.sysConfig::get('REQUEST_PICKUP_BEFORE_DAYS').' DAY',time())){
				$pickupReqCol->addOption($req['pickup_requests_id'], strftime(sysLanguage::getDateFormat('short'), strtotime($req['start_date'])).'&nbsp;'.$req['PickupRequestsTypes']['type_name']);
			}
		}

	}

	if ($shippedProducts){
		ob_start();
		echo  '<div><b>' . sysLanguage::get('TEXT_SHIPPED_MOVIES') . ' - ' . $shippedProducts . '</b><br><br>';
		new productListingBox($info_box_contents);
		echo '</div><br/>';
		$pageContents .= ob_get_contents();
		ob_end_clean();
	}

	$Qrental = Doctrine_Query::create()
		->from('RentalQueueTable')
		->where('customers_id = ?', $userAccount->getCustomerId());
		EventManager::notify('RentalQueueBeforeExecute', &$Qrental);
		$Qrental = $Qrental->orderBy('priority')

		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if (count($Qrental) > 0){
			ob_start();
?>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
		</tr>
		<tr>
			<td class="main"><b><?php
			echo sysLanguage::get('TEXT_REQUESTED_MOVIES') . " - " . count($Qrental);

			?></b></td>
		</tr>
		<tr>
			<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
		</tr>
		<tr>
			<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
		</tr>
		<tr>
			<td>
			</td>
		</tr>
		<tr>
			<td>
				<?php
				$info_box_contents = array();
				$info_box_contents[0][] = array(
					'align' => 'center',
					'params' => 'class="ui-widget-header"',
					'text' => sysLanguage::get('TABLE_HEADING_PRIORITY')
				);

				$info_box_contents[0][] = array(
					'align' => 'center',
					'params' => 'class="ui-widget-header"',
					'text' => sysLanguage::get('TABLE_HEADING_TITLE')
				);

				if (sysConfig::get('RENTAL_AVAILABILITY_RENTAL_QUEUE') == 'true'){
					$info_box_contents[0][] = array(
						'align' => 'center',
						'params' => 'class="ui-widget-header"',
						'text' => sysLanguage::get('TABLE_HEADING_AVAILABILITY')
					);
				}

				$info_box_contents[0][] = array(
					'align' => 'center',
					'params' => 'class="ui-widget-header"',
					'text' => sysLanguage::get('TABLE_HEADING_REMOVE')
				);

				EventManager::notify('ListingRentalQueueHeader', &$info_box_contents[0]);

				$i = 1;
				foreach($Qrental as $rInfo){
					if ($i % 2 == 1) {
						$info_box_contents[] = array('params' => 'class="productListing-even"');
					}else{
						$info_box_contents[] = array('params' => 'class="productListing-odd"');
					}

					$productsId = $rInfo['products_id'];

					$priority = htmlBase::newElement('input')
					->setName('queue_priority['.$productsId.']')
					->setSize(2)
					->setValue($rInfo['priority']);

					$QproductsInQueue = Doctrine_Query::create()
					->from('RentalQueueTable')
					->where('products_id = ?', $productsId)
					->andWhere('customers_id = ?', $userAccount->getCustomerId())
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					$QAvailability = Doctrine_Query::create()
					->from('RentalAvailability r')
					->leftJoin('r.RentalAvailabilityDescription rad')
					->where('rad.language_id = ?', Session::get('languages_id'))
					->orderBy('ratio')
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					$productClass = new product($productsId);
					$productsName = '<a target="_blank" href="'.itw_app_link('products_id='.$productsId,'product','info').'">'.$productClass->getName().'</a>';
					$purchaseTypeRental = $productClass->getPurchaseType('rental', true);
					$availability = count($QproductsInQueue) - $purchaseTypeRental->getCurrentStock();
					$availabilityName = null;

					if ($QAvailability){
						foreach($QAvailability as $aInfo){
							if ($availability <= $aInfo['ratio']){
								$availabilityName = $aInfo['RentalAvailabilityDescription'][0]['name'];
								break;
							}
						}
					}

					$remove	= htmlBase::newElement('checkbox')
					->setName('queue_delete[]')
					->setValue($productsId);

					$info_box_contents[$i][] = array(
						'align' => 'center',
						'params' => 'class="productListing-data"',
						'text' => $priority->draw()
					);

					$info_box_contents[$i][] = array(
						'align' => 'center',
						'params' => 'class="productListing-data" valign="top"',
						'text' => $productsName
					);

					if (sysConfig::get('RENTAL_AVAILABILITY_RENTAL_QUEUE') == 'true'){
						$info_box_contents[$i][] = array(
							'align' => 'center',
							'params' => 'class="productListing-data" valign="top"',
							'text' => $availabilityName
						);
					}

					$info_box_contents[$i][] = array(
						'align' => 'center',
						'params' => 'class="productListing-data" valign="top"',
						'text' => $remove->draw()
					);

					EventManager::notify('ListingRentalQueue', &$info_box_contents[$i], $rInfo);

					$i++;
				}

				new productListingBox($info_box_contents);
				?>
			</td>
		</tr>
	</table>
			                    <?php
   	$pageContents .= ob_get_contents();
		ob_end_clean();
	$pageContents .= '<div style="margin-top:20px;">'
			. (isset($updatePickup)?$pickupReqCol->draw().'<br/>'. $updatePickup->draw().'<br/>':'')
			.'</div>';
		$pageContent->set('pageForm', array(
				'name' => 'update_rental_queue',
				'action' => itw_app_link('action=updateQueue', 'rentals', 'queue'),
				'method' => 'post'
			));

	$link = itw_app_link(null, 'products', 'all');
	if (isset($navigation->snapshot['get']) && sizeof($navigation->snapshot['get']) > 0){
		if (isset($navigation->snapshot['get']['cPath'])){
			$link = itw_app_link('cPath=' . $navigation->snapshot['get']['cPath'], 'index', 'default');
		}
	}

	$continueButtonHtml = htmlBase::newElement('button')
	->setName('continue')
	->setText(sysLanguage::get('TEXT_BUTTON_CONTINUE_CART'))
	->setHref($link);

	$updateQueueButton = htmlBase::newElement('button')
	->setText(sysLanguage::get('TEXT_BUTTON_UPDATE_QUEUE'))
	->setType('submit');

	$pageButtons = $continueButtonHtml->draw(). ' '.$updateQueueButton->draw();
}else{
	$pageContents .= sysLanguage::get('TEXT_QUEUE_EMPTY');

	$pageContents .= '<div style="margin-top:20px;">'
		. (isset($updatePickup)?$pickupReqCol->draw().'<br/>'. $updatePickup->draw().'<br/>':'')
		.'</div>';
	$pageContent->set('pageForm', array(
			'name' => 'update_rental_queue',
			'action' => itw_app_link('action=updateQueue', 'rentals', 'queue'),
			'method' => 'post'
		));

	$link = itw_app_link(null, 'products', 'all');
	if (isset($navigation->snapshot['get']) && sizeof($navigation->snapshot['get']) > 0){
		if (isset($navigation->snapshot['get']['cPath'])){
			$link = itw_app_link('cPath=' . $navigation->snapshot['get']['cPath'], 'index', 'default');
		}
	}

	$continueButtonHtml = htmlBase::newElement('button')
		->setName('continue')
		->setText(sysLanguage::get('TEXT_BUTTON_CONTINUE_CART'))
		->setHref($link);

	$pageButtons = $continueButtonHtml->draw();
}
	}

	$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE'));
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
?>