<?php
	$InventoryCenter->inventory_center_stores = (isset($_POST['inventory_center_stores'])?implode(';', $_POST['inventory_center_stores']):'');
	$InventoryCenter->save();
?>