<?php
$productID = $product->getID();
$productID_string = $_GET['products_id'];
$productName = $product->getName();
$productImage = $product->getImage();
$thumbUrl = 'imagick_thumb.php?path=rel&imgSrc=';

$image = $thumbUrl . $productImage;
EventManager::notify('ProductInfoProductsImageShow', &$image, &$product);
?>
<style>
.productImageGallery a { border:1px solid transparent;display:inline-block;vertical-align:middle;margin:.2em; }
</style>
 <div>
   <div style="text-align:center;float:left;margin:1em;margin-right:2em;" class="ui-widget ui-widget-content ui-corner-all">
    <div style="margin:.5em;"><a id="productsImage" class="fancyBox" href="<?php echo $image;?>"><?php
      echo '<img class="jqzoom" src="' . $image . '&width=250&height=250" alt="' . $image . '" /><br />' . sysLanguage::get('TEXT_CLICK_TO_ENLARGE');
    ?></a></div>
    <div style="margin:.5em;"><?php echo rating_bar($productName,$productID);?></div>
<?php
    $QadditionalImages = Doctrine_Query::create()
    ->select('file_name')
    ->from('ProductsAdditionalImages')
    ->where('products_id = ?', $productID)
    ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    if ($QadditionalImages){
?>
    <div style="margin:.5em;" class="productImageGallery">
     <div class="ui-widget ui-widget-content ui-corner-all" style="overflow:none;">
      <a class="fancyBox ui-state-active" index="0" rel="gallery" href="<?php echo $image;?>"><img class="additionalImage" imgSrc="<?php echo $image;?>&width=250&height=250" src="<?php echo $image;?>&width=50&height=50"></a>
<?php
   		$imgSrc = sysConfig::getDirWsCatalog() . sysConfig::get('DIR_WS_IMAGES');
	     $ind = 0;
    	foreach($QadditionalImages as $imgInfo){
    		$addImage = $thumbUrl . $imgSrc . $imgInfo['file_name'];
    		$productImageSrc = $addImage . '&width=250&height=250';
    		$thumbSrc = $addImage . '&width=50&height=50';
    		$ind++;
    		echo '<a class="fancyBox" index="'.$ind.'" rel="gallery" href="' . $addImage . '"><img class="additionalImage" imgSrc="' . $productImageSrc . '" src="' . $thumbSrc . '"></a>';		    
    	}
?>
     </div>
    </div>
    <div class="main" style="margin:.5em;">Click Image To Select</div>
<?php
    }
?>
   </div>

   <div style="text-align:center;"><h1><?php
   echo $productName;
   if ($product->hasModel()){
  	echo '<br><span class="smallText">[' . $product->getModel() . ']</span>';
   }
   ?></h1></div>
   <?php
   $contents = EventManager::notifyWithReturn('ProductInfoBeforeDescription', &$product);
   if (!empty($contents)){
	   foreach($contents as $content){
		   echo $content;
	   }
   }
   ?>
     <?php echo $product->getDescription();?>
     <br />
   <?php
   $contents = EventManager::notifyWithReturn('ProductInfoAfterDescription', &$product);
   if (!empty($contents)){
	   foreach($contents as $content){
		   echo $content;
	   }
   }
   ?>

  <div style="clear:both;"></div>

  <div style="text-align:center;"><?php

  if ($product->isNotAvailable()) {
  	echo '<div>' . sprintf(sysLanguage::get('TEXT_DATE_AVAILABLE'), tep_date_long($product->getAvailableDate())) . '</div>';
  } else {
  	//echo '<div>' . sprintf(sysLanguage::get('TEXT_DATE_ADDED'), tep_date_long($product->getDateAdded())) . '</div>';
  }
  ?></div>
  <div style="clear:both;"></div>
 </div>