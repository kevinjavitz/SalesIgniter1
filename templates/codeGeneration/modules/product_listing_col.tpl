<?php
	global $currencies;

	if (is_array($listingData)){
?>
<div class="productListingColContainer ui-corner-all-big">
<?php
if (isset($pager) || isset($sorter)){
?>
 <div class="productListingColPager ui-corner-all"><?php
 if (isset($pager)){
 	echo '<b>Page:</b> ' . $pager;
 }

 if (isset($sorter)){
 	if (isset($pager)){
 		echo '<div style="top:23%;right:1em;position:absolute;"><b>'.sysLanguage::get('PRODUCT_LISTING_SORT_BY').':</b>' . $sorter . '</div>';
 	}else{
 		echo '<div style="top:23%;right:1em;position:relative;"><b>'.sysLanguage::get('PRODUCT_LISTING_SORT_BY').':</b>' . $sorter . '</div>';
 	}
 }
 ?></div>
<?php
}
?>
 <div class="productListingColContents">
 <?php foreach($listingData as $pClass){ ?>
  <div class="productListingColBoxContainer">
   <div class="productListingColBoxTitle"><a href="<?php echo itw_app_link('products_id=' . $pClass->getId(), 'product', 'info');?>"><?php echo $pClass->getName();?></a></div>
   <div class="productListingColBoxContentContainer ui-corner-all-big">
  	<div class="productListingColBoxContent ui-corner-all-big">
  	 <div class="productListingColBoxContent_image"><a href="<?php echo itw_app_link('products_id=' . $pClass->getId(), 'product', 'info');?>"><?php
		$image = $pClass->getImage();
		if (Event::exists('productListing_productsImage_show')){
			Event::run('productListing_productsImage_show', &$image, &$pClass);
		}

		$imageHtml = htmlBase::newElement('image')
		->setSource($image)
		->setWidth(SMALL_IMAGE_WIDTH)
		->setHeight(SMALL_IMAGE_HEIGHT)
		->thumbnailImage(true);
  	    echo $imageHtml->draw();
  	  ?></a></div>
  	</div>
   </div>
  </div>
 <?php } ?>
 </div>
<?php
if (isset($pager) || isset($sorter)){
?>
 <div class="productListingColPager ui-corner-all"><?php
 if (isset($pager)){
 	echo '<b>Page:</b> ' . $pager;
 }

 if (isset($sorter)){
 	if (isset($pager)){
 		echo '<div style="top:23%;right:1em;position:absolute;"><b>'.sysLanguage::get('PRODUCT_LISTING_SORT_BY').':</b>' . $sorter . '</div>';
 	}else{
 		echo '<div style="top:23%;right:1em;position:relative;"><b>'.sysLanguage::get('PRODUCT_LISTING_SORT_BY').':</b>' . $sorter . '</div>';
 	}
 }
 ?></div>
<?php
}
?>
</div>
<?php
}else{
	echo $listingData;
}
?>