<?php
	if ($success === true){
		if (!isset($_GET['oID'])){
			$NewOrder->OrdersToStores->stores_id = 1;
			$NewOrder->Customers->CustomersToStores[0]->stores_id = 1;
			$NewOrder->save();
		}
	}
?>