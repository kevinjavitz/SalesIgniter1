 <table cellpadding="0" cellspacing="0" border="0">
<?php
	$productsInBox = 0;
	if ($Product['products_id'] > 0){
	   	$Qitems = Doctrine_Query::create()
	   	->select('count(products_id) as total')
	   	->from('ProductsToBox')
	   	->where('box_id = ?', $Product['products_id'])
	   	->groupBy('box_id')
	   	->execute();
	   	if ($Qitems->count() > 0){
	   		$productsInBox = $Qitems->count();
	   	}
	   	$Qitems->free();
	   	unset($Qitems);
	}

	if ($productsInBox <= 0){
		if (isset($boxes_array)){
?>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_PRODUCTS_BOX'); ?></td>
   <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('products_in_box', $is_box_array, $Product['products_in_box']); ?><br><?php echo tep_draw_separator('pixel_trans.gif', '24', '5'); ?><br><div id="box_panel" name="box_panel"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . sysLanguage::get('TEXT_PRODUCT_DISC_LABEL') . '&nbsp;' . tep_draw_input_field('disc_label', $disc_label, ' style="width:30px"');?><br><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . TEXT_PRODUCT_IS_BOX . '&nbsp;' . tep_draw_pull_down_menu('box_ex', $boxes_array, $box_id);?></div></td>
  </tr>
<?php
		}
	}else{
		if ($Product['products_id'] > 0){
		   	$Qitems = Doctrine_Query::create()
		   	->select('p.products_id, pd.products_name, p2b.disc')
		   	->from('ProductsToBox p2b')
		   	->leftJoin('p2b.Products p')
		   	->leftJoin('p.ProductsDescription pd')
		   	->where('p2b.box_id = ?', $Product['products_id'])
		   	->andWhere('pd.language_id = ?', Session::get('languages_id'))
		   //	->groupBy('box_id')
		   	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		   	
		   	$boxProducts = false;
		   	if (sizeof($Qitems) > 0){
		   		$boxProducts = $Qitems;
		   	}
		   	//$Qitems->free();
		   	unset($Qitems);
		}
?>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_PRODUCTS_BOX_EX'); ?></td>
   <td class="main" valign="top"><table border="0" cellspacing="0" cellpadding="2">
<?php
		if ($boxProducts !== false){
			foreach($boxProducts as $boxProduct){
?>
    <tr>
     <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . sysLanguage::get('TEXT_PRODUCT_DISC_LABEL') . '&nbsp;' . $boxProduct['disc'] . ' - <a href="' . itw_app_link('pID=' . $boxProduct['Products']['products_id'], null, 'default') . '">' . $boxProduct['Products']['ProductsDescription'][0]['products_name'] . '</a>'; ?></td>
    </tr>
<?php
			}
			unset($boxProducts);
		}
?>
   </table></td>
  </tr>
<?php
	}
?>
 </table>