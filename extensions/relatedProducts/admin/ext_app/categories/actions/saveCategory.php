<?php
	$Category->related_products = '';
	if (isset($_POST['related_products'])){
		$Category->related_products = implode(',', $_POST['related_products']);
	}
	
	$Category->save();

	if (isset($_POST['related_productsGlobal'])){

		$QrelatedGlobal = Doctrine_Core::getTable('ProductsRelatedGlobal');
               	$related = $QrelatedGlobal->findOneByType('C');
		if($related->type != 'C'){    
			$related = $QrelatedGlobal->create();
			$related->type = 'C';
		}
		
        	$related->related_global = implode(',', $_POST['related_productsGlobal']);
				
		$related->save();
	}
?>