<?php
	$QProducts = Doctrine_Query::create()
    ->from('Products')
	->where('products_model = ?', '')
	->execute();

	foreach($QProducts as $product){
		$product->products_model = tep_create_random_value(8);
		$product->save();
	}
?>
<h1>Product Models were filled</h1>