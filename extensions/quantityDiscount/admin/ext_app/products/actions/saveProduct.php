<?php
	$Discounts =& $Product->ProductsQuantityDiscounts;
	$Discounts->delete();
	
	$i=0;
	foreach($_POST['discount_qty_from'] as $purchaseType => $qtyInfo){
		foreach($qtyInfo as $idx => $fromQty){
			$toQty = $_POST['discount_qty_to'][$purchaseType][$idx];
			if ($fromQty != '' && $toQty != ''){
				$price = $_POST['discount_price'][$purchaseType][$idx];
				
				$Discounts[$i]->quantity_from = $fromQty;
				$Discounts[$i]->quantity_to = $toQty;
				$Discounts[$i]->price = $price;
				$Discounts[$i]->purchase_type = $purchaseType;
				
				$i++;
			}
		}
	}		
	$Product->save();
?>