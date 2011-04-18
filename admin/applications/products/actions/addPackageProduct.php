<?php
	$productID = (int)$_GET['packageProductID'];
	$parentID = (int)$_GET['packageParentID'];
	$quantity = (int)$_GET['packageQuantity'];
	$purchaseType = $_GET['packageProductType'];

	$Qcheck = Doctrine_Query::create()
	->select('count(products_id) as total')
	->from('ProductsPackages')
	->where('products_id = ?', $productID)
	->andWhere('parent_id = ?', $parentID)
	->andWhere('purchase_type = ?', $purchaseType)
	->execute();
	if ($Qcheck && $check[0]['total'] > 0){
		EventManager::attachActionResponse(array(
			'success' => true,
			'errMsg'  => 'This product is already in this package'
		), 'json');
	}else{
		$Product = new ProductsPackages();
		$Product->parent_id = $parentID;
		$Product->products_id = $productID;
		$Product->purchase_type = $purchaseType;
		$Product->quantity = $quantity;
		$Product->save();

		$tableRow = '<tr>' .
		'<td class="main"><input type="text" name="packageProductQuantity" class="packageProduct" value="' . $quantity . '" size="4"></td>' .
		'<td class="main">' . tep_get_products_name($productID) . '</td>' .
		'<td class="centerAlign main">' . $typeNames[$purchaseType] . '</td>' .
		'<td class="rightAlign main">' .
		'<input type="button" value="Delete" class="deletePackageProduct">&nbsp;' .
		'<input type="button" value="Update" class="updatePackageProduct">' .
		'<input type="hidden" name="packageProductID" id="packageProductID" value="' . $productID . '">' .
		'</td>' .
		'</tr>';

		EventManager::attachActionResponse(array(
			'success'  => true,
			'tableRow' => $tableRow
		), 'json');
	}
?>