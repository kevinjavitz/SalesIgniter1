<?php
	$Product->related_products = '';
	if (isset($_POST['related_products'])){
		$Product->related_products = implode(',', $_POST['related_products']);
	}
	
	$Product->save();

	if (isset($_POST['related_productsGlobal'])){

		$QrelatedGlobal = Doctrine_Core::getTable('ProductsRelatedGlobal');
               $related = $QrelatedGlobal->findOneByType('P');
		if($related->type != 'P'){    
			$related = $QrelatedGlobal->create();
			$related->type = 'P';
		}
		
        $related->related_global = implode(',', $_POST['related_productsGlobal']);
				
		$related->save();
	}
	
?>