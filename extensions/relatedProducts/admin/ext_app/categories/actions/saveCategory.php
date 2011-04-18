<?php
	$Category->related_products = '';
	if (isset($_POST['related_products'])){
		$Category->related_products = implode(',', $_POST['related_products']);
	}
	
	$Category->save();
?>