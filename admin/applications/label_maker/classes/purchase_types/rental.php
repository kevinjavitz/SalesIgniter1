<?php
	class labelMaker_rental {
		public function __construct(){
		}
		
		public function toArray($products, $settings){
			$prodArr = array();
			
			$result = $this->loadProducts($products, $settings);
			foreach($result as $product){
				$Product = $product['Products'];
				$ProductDescription = $Product['ProductsDescription'][Session::get('languages_id')];
				$ProductsInventoryBarcodes = $product['ProductsInventoryBarcodes'];
				$Customer = $product['Customers'];

				if(isset($Customer['customers_delivery_address_id'])){
					$deliveryAdress = $Customer['customers_delivery_address_id'];
				}else{
					$deliveryAdress = $Customer['customers_default_address_id'];
				}

				$QAddress = Doctrine_Query::create()
				->from('AddressBook')
				->where('address_book_id = ?', $deliveryAdress)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);


				$prodArr[] = array(
					'products_id'          => $Product['products_id'],
					'products_name'        => $ProductDescription['products_name'],
					'barcode_id'           => $ProductsInventoryBarcodes['barcode_id'],
					'barcode'              => $ProductsInventoryBarcodes['barcode'],
					'products_description' => stripslashes(strip_tags($ProductDescription['products_description'])),
					'customers_address'    => $QAddress[0]
				);
			}
			return $prodArr;
		}
		
		private function loadProducts($products = array(), $settings = array()){
			$query = Doctrine_Query::create()
			//->select('ab.*, co.*, z.*, rq.customers_queue_id, rq.shipment_date as date_shipped, rq.products_barcode as barcode, concat(c.customers_firstname, " ", c.customers_lastname) as customers_name, p.products_id, pd.products_name, "rental" as products_type, c.customers_id, co.countries_id, ib.barcode_id')
			->from('RentedQueue rq')
			->leftJoin('rq.ProductsInventoryBarcodes ib')
			->leftJoin('rq.Customers c')
			->leftJoin('c.AddressBook ab')
			->leftJoin('ab.Countries co')
			->leftJoin('ab.Zones z')
			->leftJoin('rq.Products p')
			->leftJoin('p.ProductsDescription pd')
			->where('pd.language_id = ?', Session::get('languages_id'))
			->andWhere('ab.address_book_id = c.customers_default_address_id')
			->orderBy('rq.shipment_date asc, pd.products_name asc');

			if (isset($settings['startDate']) && isset($settings['endDate'])){
				$query->andWhere('rq.shipment_date between CAST("' . $settings['startDate'] . ' 00:00:00" as DATE) and CAST("' . $settings['endDate'] . ' 23:59:59" as DATE)');
			}

			if (is_array($products) && sizeof($products) > 0){
				$query->andWhereIn('rq.customers_queue_id', $products);
			}

			return $query->execute()->toArray(true);
		}
	}
?>