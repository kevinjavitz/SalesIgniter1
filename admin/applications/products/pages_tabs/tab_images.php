<?php
	$ProductImage = htmlBase::newElement('uploadManagerInput')
	->setName('products_image')
	->setFileType('image')
	->autoUpload(true)
	->showPreview(true)
	->showMaxUploadSize(true);
	
	$productDesigner = $appExtension->getExtension('productDesigner');
	if ($productDesigner !== false && $productDesigner->isEnabled() === true){
		$ProductImageBack = htmlBase::newElement('uploadManagerInput')
		->setName('products_image_back')
		->setFileType('image')
		->autoUpload(true)
		->showPreview(true)
		->showMaxUploadSize(true);
	}
		
	if (isset($Product)){
		$ProductImage->setPreviewFile($Product['products_image']);
		if (isset($ProductImageBack)){
			$ProductImageBack->setPreviewFile($Product['products_image_back']);
		}
	}
	
	if (isset($Product)){
		$zoomIcon = htmlBase::newElement('icon')->setType('zoomIn');
		$deleteIcon = htmlBase::newElement('icon')->setType('closeThick')->addClass('deleteImage');
		$imgSrc = sysConfig::getDirWsCatalog() . 'images/';
		$thumbSrc = 'imagick_thumb.php?width=80&height=80&imgSrc=' . sysConfig::getDirFsCatalog() . 'images/';
	}
?>
 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_PRODUCTS_IMAGE'); ?></td>
   <td class="main" valign="top"><?php echo $ProductImage->draw();?></td>
  </tr>
<?php
	if (isset($ProductImageBack)){
?>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo 'Products Image Back:'; ?></td>
   <td class="main" valign="top"><?php echo $ProductImageBack->draw(); ?></td>
  </tr>
<?php		
	}
?>
  <tr>
   <td colspan="2"><hr /></td>
  </tr>
  <tr>
   <td colspan="2"><table cellpadding="3" cellspacing="0" border="0">
    <thead>
     <tr>
      <th><b>Additional Images</b></th>
     </tr>
    </thead>
    <tbody>
     <tr>
      <td colspan="2"><?php
		$additionalImage = htmlBase::newElement('uploadManagerInput')
		->setName('additional_images')
		->setFileType('image')
		->autoUpload(true)
		->showPreview(true)
		->showMaxUploadSize(true)
		->allowMultipleUploads(true);
      foreach($Product['ProductsAdditionalImages'] as $imgInfo){
      	$additionalImage->setPreviewFile($imgInfo['file_name']);
      }
      echo $additionalImage->draw();
      ?></td>
     </tr>
    </tbody>
   </table></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 </table>