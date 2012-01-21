<div id="tabAlsoPurchased"><?php
	if (isset($_GET['products_id'])){
		$QOrders = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc("select p.products_id from orders_products opa, orders_products opb, orders o,
			 products p where opa.products_id = '" . (int)$_GET['products_id'] . "' and opa.orders_id = opb.orders_id and
			  opb.products_id != '" . (int)$_GET['products_id'] . "' and opb.products_id = p.products_id and
			  opb.orders_id = o.orders_id and p.products_status = '1' group by p.products_id
			  order by o.date_purchased desc limit " . sysConfig::get('MAX_DISPLAY_ALSO_PURCHASED'));
		$num_products_ordered = sizeof($QOrders);
		if ($num_products_ordered >= sysConfig::get('MIN_DISPLAY_ALSO_PURCHASED')){
			$listingData = array();
			foreach($QOrders as $pInfo){
				$listingData[] = $pInfo['products_id'];
			}
			$productListing = new productListing_row();
			$productListing->disablePaging()
				->disableSorting()
				->dontShowWhenEmpty()
				->setData($listingData);

			echo $productListing->draw();
		}
	}
	?>
	
</div>