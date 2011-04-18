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
 	echo '<b>'.sysLanguage::get('PRODUCT_LISTING_PAGE').':</b> ' . $pager;
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
<script>
/*
$(document).ready(function (){
	var imagesToLoad = [];
	var i=0;
	$('.designerImage').each(function (){
		imagesToLoad.push(this);
		$(this).bind('loadImage', function (){
			var self = this;
			var newImage = new Image();
			newImage.src = $(this).attr('imgSrc');
			$(newImage).bind('load', function (){
				$(self).replaceWith($(newImage));
				i++;
				if (imagesToLoad[i]){
					$(imagesToLoad[i]).trigger('loadImage');
				}
			});
		});
	});

	$(imagesToLoad[0]).trigger('loadImage');
});
*/
</script>
 <div class="productListingColContents">
 <?php foreach($listingData as $pClass){ ?>
  <div class="productListingColBoxContainer">
   <div class="productListingColBoxTitle"><a href="<?php echo itw_app_link('products_id=' . $pClass->getId(), 'product', 'info');?>"><?php echo $pClass->getName();?></a></div>
   <div class="productListingColBoxContentContainer ui-corner-all-big">
  	<div class="productListingColBoxContent ui-corner-all-big">
  	 <div class="productListingColBoxContent_image"><a href="<?php echo itw_app_link('products_id=' . $pClass->getId(), 'product', 'info');?>"><?php
  	 $image = $pClass->getImage();
  	 EventManager::notify('ProductListingProductsImageShow', &$image, &$pClass, 200, 200);

  	 $imageHtml = htmlBase::newElement('image')->setWidth(200)->setHeight(200);
  	 if ($pClass->productInfo['product_designable'] == '1'){
  	 	$imageHtml/*->addClass('designerImage')
  	 	->setSource('ext/jQuery/themes/icons/ajax_loader_normal.gif')*/
  	 	->setSource($image)
  	 	->attr('imgSrc', $image)
  	 	//->setWidth(36)->setHeight(36)
  	 	->thumbnailImage(false);
  	 }else{
  	 	$imageHtml->setSource($image)
  	 	->thumbnailImage(true);
  	 }
  	 echo $imageHtml->draw();
  	 ?></a></div>
  	</div>
   </div>
   <div class="productListingColBoxContent_price ui-corner-bottom-big"><?php
    $discountsExt = $appExtension->getExtension('quantityDiscount');
    //echo ($discountsExt !== false) . ' && ' . ($pClass->canBuy('new'));
    $purchaseTypeClass = $pClass->getPurchaseType('new');
    if ($discountsExt !== false && $purchaseTypeClass->hasInventory()){
    	$discounts = $discountsExt->getProductsDiscounts($pClass->getID());
    	if ($discounts->count() > 0){
    		echo '<span style="font-size:.75em;">' . $purchaseTypeClass->displayPrice() . '</span><br><span style="color:#FF0000;font-size:.55em">Bulk Order - ' . $currencies->format($discounts[0]->price) . '</span>';
    	}else{
  			echo $purchaseTypeClass->displayPrice();
    	}
    }else{
    	echo $purchaseTypeClass->displayPrice();
    }
   ?></div>
  </div>
 <?php } ?>
 </div>
<?php
if (isset($pager) || isset($sorter)){
?>
 <div class="productListingColPager ui-corner-all"><?php
 if (isset($pager)){
 	echo '<b>'.sysLanguage::get('PRODUCT_LISTING_PAGE').':</b> ' . $pager;
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