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
<?php
	$productsImage = '<div style="text-align:center;float:left;margin:1em;margin-right:2em;" class="ui-widget ui-widget-content ui-corner-all">' .
                     '<div style="margin:.5em;text-align:center;">';
	if(sysConfig::get('SHOW_ENLAGE_IMAGE_TEXT') == 'true') {
		$productsImage .= '<a id="productsImage" class="fancyBox" href="' . $image . '">' .
						  '<img class="jqzoom" src="' . $image . '&width=250&height=250" alt="' . $image . '" /><br />' .
						  sysLanguage::get('TEXT_CLICK_TO_ENLARGE') .
						  '</a>';
	} else {
		$productsImage .= '<img src="' . $image . '&width=250&height=250" alt="' . $image . '" /><br />';
	}
	$productsImage .= rating_bar($productName,$productID) .
                  '</div>';

	$QadditionalImages = Doctrine_Query::create()
	->select('file_name')
	->from('ProductsAdditionalImages')
	->where('products_id = ?', $productID)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($QadditionalImages){
		$productsImage .= '<div style="margin:.5em;" class="productImageGallery">' . 
			'<div class="ui-widget ui-widget-content ui-corner-all" style="overflow:none;">' . 
				'<a class="fancyBox ui-state-active" index="0" rel="gallery" href="' . $image . '"><img class="additionalImage" imgSrc="' . $image . '&width=250&height=250" src="' . $image . '&width=50&height=50"></a>';

		$imgSrc =  'images/';
		$ind = 0;
		foreach($QadditionalImages as $imgInfo){
			$addImage = $thumbUrl . $imgSrc . $imgInfo['file_name'];
			$productImageSrc = $addImage . '&width=250&height=250';
			$thumbSrc = $addImage . '&width=50&height=50';
			$ind++;
			
			$productsImage .= '<a class="fancyBox" index="'.$ind.'" rel="gallery" href="' . $addImage . '"><img class="additionalImage" imgSrc="' . $productImageSrc . '" src="' . $thumbSrc . '"></a>';
		}
		
		$productsImage .= '</div>' . 
		'</div>' . 
		'<div class="main" style="margin:.5em;">Click Image To Select</div>';
	}else{
		$productsImage .= '<a class="fancyBox ui-state-active" style="display:none" index="0" rel="gallery" href="' . $image . '"><img class="additionalImage" imgSrc="' . $image . '&width=250&height=250" src="' . $image . '&width=50&height=50"></a>';
	}

	EventManager::notify('ProductInfoAfterShowImages', $product, &$productsImage);

	$productsImage .= '</div>';
	
	echo $productsImage;
	
	echo '<p>';
	
	$contents = EventManager::notifyWithReturn('ProductInfoBeforeDescription', &$product);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
	
	echo $product->getDescription() . '<br />';

	$contents = EventManager::notifyWithReturn('ProductInfoAfterDescription', &$product);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
	
	echo '</p>' . 
	'<div style="clear:both;"></div>';
	
	$purchaseBoxes = array();
	$purchaseTypes = array();
	foreach($product->productInfo['typeArr'] as $typeName){
		$purchaseTypes[$typeName] = $product->getPurchaseType($typeName);
		if ($purchaseTypes[$typeName]){
			$settings = $purchaseTypes[$typeName]->getPurchaseHtml('product_info');
			if (is_null($settings) === false){
  				EventManager::notify('ProductInfoPurchaseBoxOnLoad', &$settings, $typeName, $purchaseTypes);
				$purchaseBoxes[] = $settings;
			}
		}
	}
	
	$extDiscounts = $appExtension->getExtension('quantityDiscount');
	$extAttributes = $appExtension->getExtension('attributes');
	
	$purchaseTable = htmlBase::newElement('table')
	->addClass('ui-widget')
	->css('width', '100%')
	->setCellPadding(5)
	->setCellSpacing(0);
	
	$columns = array();
	foreach($purchaseBoxes as $boxInfo){
		if ($extAttributes !== false){
			$boxInfo['content'] .= $extAttributes->pagePlugin->drawAttributes(array(
				'productClass' => $product,
				'purchase_type' => $boxInfo['purchase_type']
			));
		}
		
		if ($extDiscounts !== false && $purchaseTypes[$boxInfo['purchase_type']]->hasInventory()){
			$boxInfo['content'] .= $extDiscounts->showQuantityTable(array(
				'productClass' => $product,
				'purchase_type' => $boxInfo['purchase_type'],
				'product_id' => $product->getId()
			));
		}
		
		$boxInfo['content'] .= tep_draw_hidden_field('products_id', $productID);

		$boxObj = htmlBase::newElement('infobox')
		->setForm(array(
			'name' => 'cart_quantity',
			'action' => $boxInfo['form_action']
		))
		->css('width', 'auto')->removeCss('margin-left')->removeCss('margin-right')
		->setHeader($boxInfo['header'])
		->setButtonBarLocation('bottom');

		if ($boxInfo['allowQty'] === true){
			$qtyInput = htmlBase::newElement('input')
			->css('margin-right', '1em')
			->setSize(3)
			->setName('quantity[' . $boxInfo['purchase_type'] . ']')
			->setLabel('Quantity:')
			->setValue(1)
			->setLabelPosition('before');
			
			$boxObj->addButton($qtyInput);
		}
		if(isset($boxInfo['button']) && is_object($boxInfo['button'])){
			$boxObj->addButton($boxInfo['button']);
		}

		EventManager::notifyWithReturn('ProductInfoTabImageBeforeDrawPurchaseType', &$product, &$boxObj, &$boxInfo);

		$boxObj->addContentRow($boxInfo['content']);

		$columns[] = array(
			'align' => 'center',
			'valign' => 'top',
			'text' => $boxObj->draw()
		);

		if (sizeof($columns) > 1){
			$purchaseTable->addBodyRow(array(
				'columns' => $columns
			));
			$columns = array();
		}
	}
	
	if (sizeof($columns) > 0){
		$columns[0]['colspan'] = 2;
		$purchaseTable->addBodyRow(array(
			'columns' => $columns
		));
	}
	
	
	echo '<div style="text-align:center;">' . 
		$purchaseTable->draw() . 
		'<div style="clear:both;"></div>' . 
	'</div>' . 
	'<div style="clear:both;"></div>';

	$contents = EventManager::notifyWithReturn('ProductInfoAfterPurchaseTypes', &$product);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}

	if ($product->isBox()){
		$discs = $product->getDiscs(false, true);
		$totalDiscs = $product->getTotalDiscs();
		$pageContents .= '<br><div><h4>This product is part of a set (click product name to view details):</h4></div>';
		$hasRentals = false;
	}elseif ($product->isInBox()){
		$discNumber = $product->getDiscNumber($product->getID());
		$totalDiscs = $product->getTotalDiscs();
		
		echo '<br><div class="main"' . $style . '>' . sprintf(
			sysLanguage::get('TEXT_BS_SERIES'),
			$discNumber,
			$totalDiscs,
			'<a href="' . itw_app_link('products_id='.$product->getBoxID(), 'product', 'info') . '">' .$product->getBoxName() . '</a>'
		) .
		'</div>';
		
		$discs = $product->getDiscs($product->getID(), true);
		if (sizeof($discs) > 0){
			echo '<br><div><h4>' .sysLanguage::get('TEXT_BS_OTHER_DISCS') . '</h4></div>';
		}
	}
	
	if (isset($discs) && sizeof($discs) > 0){
		if(sysConfig::get('PRODUCT_LISTING_TYPE') == 'row'){
			$productListing = new productListing_row();
		} else {
			$productListing = new productListing_col();
		}

		$productListing->disablePaging()
		->disableSorting()
		->dontShowWhenEmpty()
		->setData($discs);
		
		echo $productListing->draw();
		
		if ($product->isBox() && sizeof($discs) > 1){
			$queueForm = htmlBase::newElement('form')
			->attr('name', 'cart_quantity')
			->attr('action', itw_app_link(tep_get_all_get_params(array('action')), null, null, 'SSL'))
			->attr('method', 'post');
			
			$prodId = htmlBase::newElement('input')
			->setType('hidden')
			->setName('products_id')
			->setValue($productID);
			
			$queueAll = htmlBase::newElement('button')
			->setText(sysLanguage::get('TEXT_BUTTON_IN_QUEUE_SERIES'))
			->setType('submit')
			->setName('add_queue_all');
			
			$queueForm->append($prodId)
			->append($queueAll);
			
			echo '<div style="text-align:right;margin-top:.3em;">' .
				$queueForm->draw() .
			'</div>';
		}
	}
	
	echo '<div style="text-align:center;">';
	
	if ($product->hasURL()) {
		echo '<div>' . 
			sprintf(
				sysLanguage::get('TEXT_MORE_INFORMATION'),
				itw_app_link('action=url&goto=' . urlencode($product->getURL()), 'redirect', 'default', 'NONSSL')
			) . 
		'</div>';
	}
	
	if ($product->isNotAvailable()) {
		echo '<div>' . 
			sprintf(
				sysLanguage::get('TEXT_DATE_AVAILABLE'),
				tep_date_long($product->getAvailableDate())
			) . 
		'</div>';
	} else {
		/*
		echo '<div>' . 
			sprintf(
				sysLanguage::get('TEXT_DATE_ADDED'),
				tep_date_long($product->getDateAdded())
			) . 
		'</div>';
		*/
	}
	
	echo '</div>';

    //echo '<div style="text-align:center">';
	$contents = EventManager::notifyWithReturn('ProductInfoTabImageAfterInfo', &$product);
		if (!empty($contents)){
			foreach($contents as $content){
				echo $content;
			}
	}
	//echo '</div>';
?>