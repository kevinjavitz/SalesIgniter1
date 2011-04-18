<?php
/*
	SalesIgniter E-Commerce System v1

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2010 I.T. Web Experts

	This script and it's source is not redistributable
*/
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

		$i = 1;
		if ($Qshipped){
			foreach($Qshipped as $sInfo){
				if ($i % 2 == 1) {
					$info_box_contents[] = array('params' => 'class="productListing-even"');
				}else{
					$info_box_contents[] = array('params' => 'class="productListing-odd"');
				}

				$productsName = tep_get_products_name($sInfo['products_id']);
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
				$i++;

			}
		}
		$shippedProducts = $i - 1;
		
		if ($shippedProducts){
			ob_start();
			echo '<div><b>' . sysLanguage::get('TEXT_SHIPPED_MOVIES') . ' - ' . $shippedProducts . '</b><br><br>';
			new productListingBox($info_box_contents);
			echo '</div><br>';
			$pageContents .= ob_get_contents();
			ob_end_clean();
		}
		
		$Qrental = Doctrine_Query::create()
		->from('RentalQueueTable')
		->where('customers_id = ?', $userAccount->getCustomerId())
		->orderBy('priority')
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
					$productsName = $productClass->getName();
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
	
	$pageContent->set('pageForm', array(
		'name' => 'update_rental_queue',
		'action' => itw_app_link('action=updateQueue', 'rentals', 'queue'),
		'method' => 'post'
	));
	
	$pageButtons = htmlBase::newElement('button')
	->setText(sysLanguage::get('TEXT_BUTTON_UPDATE_QUEUE'))
	->setType('submit')
	->draw();
}else{
	$pageContents .= sysLanguage::get('TEXT_QUEUE_EMPTY');

	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null,'index','default'))
	->draw();
}
	}

	$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE'));
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
?>