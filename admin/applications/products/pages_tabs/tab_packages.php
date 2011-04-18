<?php
 $addButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_ADD'))->addClass('addPackageProduct');
 $deleteButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_DELETE'))->addClass('deletePackageProduct');
 $updateButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_UPDATE'))->addClass('updatePackageProduct');
?>
 <div id="tabs_packages">
  <ul>
   <li><a href="#package_reservation"><span>Pay Per Rental</span></a></li>
  </ul>
 
  <div id="package_reservation">
   <p class="main">*Only "Pay per rental" products can be added to this package</p>
   <table cellpadding="3" cellspacing="0" border="0" width="95%">
    <tr>
     <td class="main" style="white-space:nowrap;"><b>Number in package</b></td>
     <td class="main" width="50%"><b>Product</b></td>
     <td class="main" style="white-space:nowrap;"><b>Purchase Type</b></td>
     <td class="rightAlign main"><b>Action</b></td>
    </tr>
    <tr>
     <td class="main"><?php echo tep_draw_input_field('packageQuantity', '', 'id="packageQuantity" size="4"');?></td>
     <td class="main"><?php echo tep_draw_input_field('packageProductName', '', 'id="packageProductName" style="width:75%"');?></td>
     <td class="main"><select name="packageProductType" id="packageProductType"></select></td>
     <td class="rightAlign main"><?php echo $addButton->draw();?></td>
    </tr>
   </table>
   <div class="main"><small>*Products are dynamically added and do not require the product to be updated</small></div>
   <hr>
   <div class="centerAlign main"><h3>Current Packaged Products</h3></div>
   <table cellpadding="3" cellspacing="0" border="0" width="95%" id="packageProducts">
    <tr>
     <td class="main" style="white-space:nowrap;"><b>Number in package</b></td>
     <td class="main" width="50%"><b>Product</b></td>
     <td class="centerAlign main" style="white-space:nowrap;"><b>Purchase Type</b></td>
     <td class="rightAlign main"><b>Action</b></td>
    </tr>
    <?php
	$QpackageProducts = Doctrine_Query::create()
	->from('ProductsPackages')
	->where('parent_id = ?', $Product['products_id'])
	->orderBy('purchase_type, quantity desc')
	->execute();
	if ($QpackageProducts){
		$row = 0;
		foreach($QpackageProducts->toArray() as $packageProduct){
			if ($row % 2){
				$class = 'rowEven';
			}else{
				$class = 'rowOdd';
			}
			echo '<tr class="' . $class . '">
			 <td class="main"><input type="text" name="packageQuantity" value="' . $packageProduct['quantity'] . '" size="4"></td>
			 <td class="main">' . tep_get_products_name($packageProduct['products_id']) . '</td>
			 <td class="centerAlign main">' . $typeNames[$packageProduct['purchase_type']] . '</td>
			 <td class="rightAlign main">
			  ' . $deleteButton->draw() . '
			  ' . $updateButton->draw() . '
			  <input type="hidden" name="packageProductID" value="' . $packageProduct['products_id'] . '">
			  <input type="hidden" name="packageParentID" value="' . $Product['products_id'] . '">
			  <input type="hidden" name="packageProductType" value="' . $packageProduct['purchase_type'] . '">
			 </td>
			</tr>';
			$row ++;
		}
		unset($class);
		unset($row);
		unset($packageProduct);
	}
	$QpackageProducts->free();
	unset($QpackageProducts);
    ?>
   </table>
  </div>
  <div id="productsDropWindow" class="ui-helper-hidden"><select name="productsDrop" id="productDropMenu"><?php
   $Qproducts = Doctrine_Query::create()
   ->select('p.products_id, p.products_type, pd.products_name')
   ->from('Products p')
   ->leftJoin('p.ProductsDescription pd')
   ->where('pd.language_id = ?', (int)Session::get('languages_id'))
   ->orderBy('pd.products_name')
   ->execute();
   foreach($Qproducts->toArray() as $products){
       $typesArr = explode(',', $products['products_type']);
       if (in_array('reservation', $typesArr)){
           $productTypes = array('reservation,' . $typeNames['reservation']);
           /*foreach($typesArr as $type){
               if (!empty($type)){
                   $productTypes[] = $type . ',' . $typeNames[$type];
               }
           }*/
           echo '<option productTypes="' . implode(';', $productTypes) . '" value="' . $products['products_id'] . '">' . 
                 $products['ProductsDescription'][Session::get('languages_id')]['products_name'] . 
                '</option>';
       }
   }
   unset($Qproducts);
   unset($products);
  ?></select></div>
 </div>