<?php
	$ProductStatusEnabled = htmlBase::newElement('radio')
	->setName('products_status')
	->setLabel(sysLanguage::get('TEXT_PRODUCT_AVAILABLE'))
	->setValue('1');
	
	$ProductStatusDisabled = htmlBase::newElement('radio')
	->setName('products_status')
	->setLabel(sysLanguage::get('TEXT_PRODUCT_NOT_AVAILABLE'))
	->setValue('0');
	
	$ProductFeaturedStatusEnabled = htmlBase::newElement('radio')
	->setName('products_featured')
	->setLabel(sysLanguage::get('TEXT_PRODUCT_FEATURED'))
	->setValue('1');
	
	$ProductFeaturedStatusDisabled = htmlBase::newElement('radio')
	->setName('products_featured')
	->setLabel(sysLanguage::get('TEXT_PRODUCT_NON_FEATURED'))
	->setValue('0');
$ProductFeaturedHidden = htmlBase::newElement('radio')
	->setName('is_hidden')
	->setLabel(sysLanguage::get('TEXT_PRODUCT_HIDDEN'))
	->setValue('1');
$ProductFeaturedNotHidden = htmlBase::newElement('radio')
	->setName('is_hidden')
	->setLabel(sysLanguage::get('TEXT_PRODUCT_NON_HIDDEN'))
	->setValue('0');
	
	$ProductDateAvailable = htmlBase::newElement('input')
	->setName('products_date_available')
	->addClass('useDatepicker');
	

	
	$ProductOnOrder = htmlBase::newElement('checkbox')
	->setId('productOnOrder')
	->setName('products_on_order')
	->setValue('1');
	
	$ProductDateOrdered = htmlBase::newElement('input')
	->setName('products_date_ordered')
	->addClass('useDatepicker');
	
	$ProductModel = htmlBase::newElement('input')
	->setName('products_model');
	
	$ProductPreview = htmlBase::newElement('uploadManagerInput')
	->setName('movie_preview')
	->setFileType('image') // @TODO: change to movie when all the upload types are supported.
	->autoUpload(true)
	->showPreview(false)
	->showMaxUploadSize(true)
	->allowMultipleUploads(false)
	->allowLocalSelection(true);
	
	$ProductWeight = htmlBase::newElement('input')
	->setName('products_weight');
	
	if (isset($Product)){
		if ($Product['products_status'] == '1'){
			$ProductStatusEnabled->setChecked(true);
		}else{
			if (!isset($_GET['pID'])){
				$ProductStatusEnabled->setChecked(true);
			}else{
				$ProductStatusDisabled->setChecked(true);
			}
		}
		
		if ($Product['products_featured'] == '1'){
			$ProductFeaturedStatusEnabled->setChecked(true);
		}else{
			$ProductFeaturedStatusDisabled->setChecked(true);
		}
		if ($Product['is_hidden'] == '1'){
			$ProductFeaturedHidden->setChecked(true);
		}else{
			$ProductFeaturedNotHidden->setChecked(true);
		}
		
		if ($Product['products_on_order'] == '1'){
			$ProductOnOrder->setChecked(true);
		}else{
			$ProductOnOrder->setChecked(false);
		}
		
		$ProductDateAvailable->setValue($Product['products_date_available']);
		$ProductDateOrdered->setValue($Product['products_date_ordered']);
		$ProductModel->setValue($Product['products_model']);
		$ProductPreview->setValue($Product['movie_preview']);
		$ProductWeight->setValue($Product['products_weight']);
	}
?>
 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_PRODUCTS_STATUS'); ?></td>
   <td class="main"><?php echo $ProductStatusEnabled->draw() . $ProductStatusDisabled->draw(); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_PRODUCTS_FEATURED'); ?></td>
   <td class="main"><?php echo $ProductFeaturedStatusEnabled->draw() . $ProductFeaturedStatusDisabled->draw(); ?></td>
  </tr>
<tr>
		 <td class="main"><?php echo sysLanguage::get('TEXT_PRODUCTS_HIDDEN'); ?></td>
		 <td class="main"><?php echo $ProductFeaturedHidden->draw() . $ProductFeaturedNotHidden->draw(); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_PRODUCTS_DATE_AVAILABLE'); ?><br><small>(YYYY-MM-DD)</small></td>
   <td class="main"><?php echo $ProductDateAvailable->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_PRODUCT_ON_ORDER'); ?></td>
   <td class="main"><?php echo $ProductOnOrder->draw(); ?></td>
  </tr>
  <tr id="productOnOrderCal" style="display:<?php echo ($ProductOnOrder->attr('checked') == 'true' ? 'block' : 'none');?>;">
   <td class="main"><?php echo sysLanguage::get('TEXT_PRODUCT_DATE_ORDERED'); ?><br><small>(YYYY-MM-DD)</small></td>
   <td class="main"><?php echo $ProductDateOrdered->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_PRODUCTS_MODEL'); ?></td>
   <td class="main"><?php echo $ProductModel->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_PRODUCTS_MOVIE_PREVIEW'); ?></td>
   <td class="main"><?php echo $ProductPreview->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_PRODUCTS_WEIGHT'); ?></td>
   <td class="main"><?php echo $ProductWeight->draw(); ?></td>
  </tr>
 </table>