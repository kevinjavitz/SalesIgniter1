<?php
	class labelMaker_reservation {
		public function __construct(){
		}
		
		public function toArray($products, $settings){
			$prodArr = array();
			
			$result = $this->loadProducts($products, $settings);
			foreach($result as $product){
				//echo '<pre>';print_r($product);
				$CustomerAddress = $product['OrdersAddresses']['delivery'];
				
				$prodArr[] = array(
					'products_id'          => $product['OrdersProducts'][0]['products_id'],
					'products_name'        => $product['OrdersProducts'][0]['products_name'],
					'barcode_id'           => $product['OrdersProducts'][0]['OrdersProductsReservation'][0]['ProductsInventoryBarcodes']['barcode_id'],
					'barcode'              => $product['OrdersProducts'][0]['OrdersProductsReservation'][0]['ProductsInventoryBarcodes']['barcode'],
					'products_description' => stripslashes(strip_tags($product['OrdersProducts'][0]['Products']['ProductsDescription'][Session::get('languages_id')]['products_description'])),
					'customers_address'    => array(
						'name'           => $CustomerAddress['entry_name'],
						'street_address' => $CustomerAddress['entry_street_address'],
						'city'           => $CustomerAddress['entry_city'],
						'state'          => $CustomerAddress['entry_state'],
						'country'        => $CustomerAddress['entry_country'],
						'postcode'       => $CustomerAddress['entry_postcode'],
						'company'        => $CustomerAddress['entry_company'],
						'format_id'      => $CustomerAddress['entry_format_id']
					)
				);
			}
			return $prodArr;
		}
		
		private function loadProducts($products = array(), $settings = array()){
			$query = Doctrine_Query::create()
			//->select('ib.barcode_id, ib.barcode, oa.*, opr.orders_products_reservations_id, opr.date_shipped, opr.barcode_id, opr.quantity_id, oa.entry_name as customers_name, op.products_name, op.products_id, "reservation" as products_type, o.customers_id, p.products_id, pd.products_description')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsReservation opr')
			->leftJoin('opr.ProductsInventoryBarcodes ib')
			->leftJoin('op.Products p')
			->leftJoin('p.ProductsDescription pd')
			->leftJoin('o.OrdersAddresses oa')
			->where('oa.address_type = ?', 'delivery')
			->orderBy('opr.date_shipped asc, op.products_name asc');

			if (isset($settings['startDate']) && isset($settings['endDate'])){
				$query->andWhere('opr.date_shipped BETWEEN CAST("' . $settings['startDate'] . '" as DATE) AND CAST("' . $settings['endDate'] . '" as DATE)');
			}

			if (is_array($products) && sizeof($products) > 0){
				$query->andWhereIn('opr.orders_products_reservations_id', $products);
			}else{
				$query->andWhere('opr.parent_id is null');
			}
			
			return $query->execute()->toArray(true);
		}
	}
?>