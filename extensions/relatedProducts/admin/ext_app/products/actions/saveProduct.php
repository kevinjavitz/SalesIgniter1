<?php
	$Product->related_products = '';
	if (isset($_POST['related_products'])){
		$Product->related_products = implode(',', $_POST['related_products']);
	}
	
	$Product->save();
?>