<?php
	if(isset($Plan)){

		$Plan->ppr_prod_group = (isset($_POST['ppr_prod_group']) ? serialize($_POST['ppr_prod_group']) : '');

		/*$QProductGroups = Doctrine_Query::create()
		->from('ProductsGroups')
		->where('product_group_id = ?', $_POST['ppr_prod_group'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$Plan->ppr_rentals = $QProductGroups[0]['product_group_limit'];*/

		$Plan->save();
	}
?>