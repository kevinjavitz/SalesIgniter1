<?php
	$Product->addon_products = '';
	if (isset($_POST['addon_products'])){
		$array = array_filter($_POST['addon_products']);
		if(count($array) > 0){
			$Product->addon_products = implode(',', $array);
		}
	}

	$Product->optional_addon_products = '';
	if (isset($_POST['optional_addon_products'])){
		$array = array_filter($_POST['optional_addon_products']);
		if(count($array) > 0){
			$Product->optional_addon_products = implode(',', $array);
		}
	}
	
	$Product->save();
?>