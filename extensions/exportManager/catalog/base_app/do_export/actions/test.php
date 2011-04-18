<?php
$Qproducts = Doctrine_Query::create()
			->select('piq.*,pi.*,p.*,pd.*')
			->from('Products p')
			->leftJoin('p.ProductsInventory pi')
			->leftJoin('pi.ProductsInventoryQuantity piq')
			->leftJoin('p.ProductsDescription pd')
			->where('pd.language_id = ?', Session::get('languages_id'))
			->andWhere('pi.type = "new" OR pi.type = "used"')
			->andWhere('piq.available > 0')
			->andWhere('p.products_status = 1')
			->orderBy('p.products_id')
			->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

print_r($Qproducts);
?>