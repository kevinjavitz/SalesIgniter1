<?php
	class inventoryCenters_catalog_account_history_info extends Extension_inventoryCenters {
		public function __construct(){
			global $App;
			parent::__construct();

			if ($App->getAppName() != 'account' || ($App->getAppName() == 'account' && $App->getPageName() != 'history_info')){
				$this->enabled = false;
			}
		}

		public function load(){
			if ($this->enabled === false) return;

			EventManager::attachEvent('AccountHistoryBeforeShowOrderHistory', null, $this);
		}

		public function AccountHistoryBeforeShowOrderHistory(){
			global $userAccount;
			if ($userAccount->isProvider() === false) return '';

			$content = '';
			$pickupz = Doctrine_Query::create()
						->from('Orders o')
						->leftJoin('o.OrdersProducts op')
						->leftJoin('op.OrdersProductsReservation ops')
						->where('o.orders_id =?', (int)$_GET['order_id'])
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if (isset($pickupz[0]['OrdersProducts'][0]['OrdersProductsReservation'][0]['inventory_center_pickup'])){
				$Qinv = Doctrine_Core::getTable('ProductsInventoryCenters')->findOneByInventoryCenterId($pickupz[0]['OrdersProducts'][0]['OrdersProductsReservation'][0]['inventory_center_pickup']);
				$deliveryInstructions = $Qinv->inventory_center_delivery_instructions;

				$content =
					'<tr>
						<td class="main"><b>Delivery Instructions</b></td>
					 </tr>
					 <tr>
						<td>'.tep_draw_separator("pixel_trans.gif", "100%", "10").'</td>
					  </tr>
					  <tr>
						<td>';

				$content .= $deliveryInstructions;
				$content .=
						'</td>
					  </tr>';
			}

			return $content;
		}
	}
?>