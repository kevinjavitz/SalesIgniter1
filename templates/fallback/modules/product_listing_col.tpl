<?php
	global $currencies;

if (is_array($listingData)) {
	$listingTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0)->attr('width', '100%');
	foreach ($listingData as $row => $rowInfo) {
		if (!is_array($rowInfo)) continue;

		$rowColumns = array();
		foreach ($rowInfo as $col => $colInfo) {
			if (!is_array($colInfo)) continue;

			$rowColumns[$col] = array(
				'text' => $colInfo['text']
			);

			if (isset($colInfo['align'])) {
				$rowColumns[$col]['align'] = $colInfo['align'];
			}
			if (isset($colInfo['valign'])) {
				$rowColumns[$col]['valign'] = $colInfo['valign'];
			} else {
				$rowColumns[$col]['valign'] = 'top';
			}

			if (isset($colInfo['addCls'])) {
				$rowColumns[$col]['addCls'] = $colInfo['addCls'];
			}
		}

		if ($row == 0) {
			$listingTable->addHeaderRow(array(
			                                 'addCls' => (isset($rowInfo['addCls']) ? $rowInfo['addCls'] : false),
			                                 'columns' => $rowColumns
			                            ));
		} else {
			$listingTable->addBodyRow(array(
			                               'addCls' => (isset($rowInfo['addCls']) ? $rowInfo['addCls'] : false),
			                               'columns' => $rowColumns
			                          ));
		}
	}
	?>
<div class="productListingColContainer ui-corner-all-big">
	<?php
 if (isset($sorter)) {
	if (sysConfig::get('PRODUCT_LISTING_SHOW_PRODUCT_NAME_FILTER') == 'True') {
		$selectedCss = array(
			'font-weight' => 'bold',
			'color' => '#333333'
		);
		$allLink = htmlBase::newElement('a')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'starts_with'))))
				->html(sysLanguage::get('PRODUCT_LISTING_ALL'));
		if (!isset($_GET['starts_with']) || $_GET['starts_with'] == '') {
			$allLink->css($selectedCss);
		}

		$numLink = htmlBase::newElement('a')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'starts_with')) . 'starts_with=num'))
				->html('0-9');
		if (isset($_GET['starts_with']) && $_GET['starts_with'] == 'num') {
			$numLink->css($selectedCss);
		}

		$letterLinks = array();
		foreach (range('A', 'Z') as $letter) {
			$letterLink = htmlBase::newElement('a')
					->setHref(itw_app_link(tep_get_all_get_params(array('action', 'starts_with')) . 'starts_with=' . $letter))
					->html($letter);
			if (isset($_GET['starts_with']) && $_GET['starts_with'] == $letter) {
				$letterLink->css($selectedCss);
			}

			$letterLinks[] = $letterLink->draw();
		}
	}

	if (sysConfig::get('PRODUCT_LISTING_ALLOW_RESULT_LIMIT') == 'True') {
		$getVars = tep_get_all_get_params(array('action', 'limit'));
		parse_str($getVars, $getArr);
		$hiddenFields = '';
		foreach ($getArr as $k => $v) {
			$hiddenFields .= '<input type="hidden" name="' . $k . '" value="' . $v . '" />';
		}

		$resultsPerPageMenu = htmlBase::newElement('selectbox')
				->setName('limit')
				->attr('onchange', 'this.form.submit()');

		$resultsPerPageMenu->addOption(10, 10);
		$resultsPerPageMenu->addOption(25, 25);
		$resultsPerPageMenu->addOption(50, 50);
		$resultsPerPageMenu->addOption(75, 75);
		$resultsPerPageMenu->addOption(100, 100);

		$resultsPerPageMenu->selectOptionByValue((isset($_GET['limit']) ? $_GET['limit'] : 10));

		$perPageForm = htmlBase::newElement('form')
				->attr('name', 'limit')
				->attr('method', 'get')
				->attr('action', itw_app_link(tep_get_all_get_params(array('action', 'limit'))))
				->html($hiddenFields)
				->append($resultsPerPageMenu);
	}
    $topFilterBar = $productListingColPager = htmlBase::newElement('div')
            ->addClass('productListingColPager ui-corner-all');
    $topFilterBarTable = htmlBase::newElement('table')
            ->setCellPadding(3)
            ->setCellSpacing(0)
            ->attr('width', '100%');
    if (sysConfig::get('PRODUCT_LISTING_SHOW_PRODUCT_NAME_FILTER') == 'True') {
        $topFilterBarTable->addBodyRow(array(
                                            'columns' => array(
                                                array(
                                                    'text' => $allLink->draw() . ' | ' . $numLink->draw() . ' | ' . implode(' ', $letterLinks)
                                                )
                                            )
                                       ));
    }

    $topFilterBarTableTable = htmlBase::newElement('table')
            ->setCellPadding(3)
            ->setCellSpacing(0)
            ->attr('width', '100%');
    $topFilterBarTableTableColumns[] =
            array(
                'text' => '<b>' . sysLanguage::get('PRODUCT_LISTING_SORT_BY') . ':</b>'
            );
    $topFilterBarTableTableColumns[] =
            array(
                'align' => 'right',
                'text' => $sorter
            );

    if (sysConfig::get('PRODUCT_LISTING_ALLOW_RESULT_LIMIT') == 'True') {
        $topFilterBarTableTableColumns[] = array(
            'text' => '<b>' . sysLanguage::get('PRODUCT_LISTING_RESULTS_PER_PAGE') . ':</b>'
        );
        $topFilterBarTableTableColumns[] =
                array(
                    'align' => 'right',
                    'text' => $perPageForm->draw()
                );
    }
    $topFilterBarTableTable->addBodyRow(array(
                                             'columns' => $topFilterBarTableTableColumns
                                        ));
    $topFilterBarTable->addBodyRow(array(
                                        'align' => 'right',
                                        'columns' => array(
                                            array('text' => $topFilterBarTableTable->draw())
                                         )
                                   ));
    EventManager::notify('ProductListingFilterBarDraw', &$topFilterBarTable, &$listingData);
    $topFilterBar->append($topFilterBarTable);
    echo $topFilterBar->draw();
	?>
	
	<br/>
	<div class="productListingColContents"><?php echo $listingTable->draw();?></div>
	<?php
	}
	$totalWidth = sysConfig::get('PRODUCT_LISTING_TOTAL_WIDTH');
	$productsPerRow = sysConfig::get('PRODUCT_LISTING_PRODUCTS_COLUMNS');
	$imageWidth = ($totalWidth / $productsPerRow) - 3;
	$imageContainerWidth = ($totalWidth / $productsPerRow);
	if(isset($pager)) {

		$productListingColPager = htmlBase::newElement('div')
				->addClass('productListingColPager ui-corner-all')
				->html($pager);

		$productListingRowContents = htmlBase::newElement('div')
				->addClass('productListingColContents')
				->append($productListingColPager);

		echo '<br>' . $productListingRowContents->draw();
	}


	$productListingColContents = htmlBase::newElement('div')
			->addClass('productListingColContents');
	foreach ($listingData as $pClass) {
		$productLink = htmlBase::newElement('a')
				->setHref(itw_app_link('products_id=' . $pClass->getId(), 'product', 'info'))
				->setId($pClass->getId())
				->html($pClass->getName());
		$productListingColBoxTitle = htmlBase::newElement('div')
				->addClass('productListingColBoxTitle')
				->css(array('width' => $imageWidth . 'px'))
				->append($productLink);

		EventManager::notify('ProductListingProductsImageShow', &$image, &$pClass);
		$image = $pClass->getImage();



		$imageHtml = htmlBase::newElement('image')->setWidth($imageWidth)->setHeight(150);
		if ($pClass->productInfo['product_designable'] == '1') {
			$imageHtml /*->addClass('designerImage')
			//->setSource('ext/jQuery/themes/icons/ajax_loader_normal.gif')*/
					->setSource($image)
					->attr('imgSrc', $image)
			//->setWidth(36)->setHeight(36)
					->thumbnailImage(false);
		} else {
			$imageHtml->setSource($image)
					->thumbnailImage(true);
		}
		$productImageLink = htmlBase::newElement('a')
				->setHref(itw_app_link('products_id=' . $pClass->getId(), 'product', 'info'))
				->html($imageHtml->draw());
		$productListingColBoxContentImage = htmlBase::newElement('div')
				->addClass('productListingColBoxContent_image')
				->attr('pID',$pClass->getId())
				->append($productImageLink);
		$productListingColBoxContent = htmlBase::newElement('div')
				->addClass('productListingColBoxContent ui-corner-all-big')
				->append($productListingColBoxContentImage);
		$productListingColBoxContentContainer = htmlBase::newElement('div')
				->addClass('productListingColBoxContentContainer ui-corner-all-big')
				->append($productListingColBoxContent);
		$contents = EventManager::notifyWithReturn('ProductListingProductsBeforeShowPrice', &$pClass);
		$contentsText = '';
		if (!empty($contents)) {
			foreach ($contents as $content) {
				$contentsText .= $content;
			}
		}

		$discountsExt = $appExtension->getExtension('quantityDiscount');
		//echo ($discountsExt !== false) . ' && ' . ($pClass->canBuy('new'));
		$purchaseTypeClass = $pClass->getPurchaseType('new');
		if ($discountsExt !== false && $purchaseTypeClass->hasInventory()){
			$discounts = $discountsExt->getProductsDiscounts($pClass->getID());
			if ($discounts->count() > 0){
				$priceout = '<span style="font-size:.75em;">' . $purchaseTypeClass->displayPrice() . '</span><br><span style="color:#FF0000;font-size:.55em">Bulk Order - ' . $currencies->format($discounts[0]->price) . '</span>';
			}else{
				$priceout = $purchaseTypeClass->displayPrice();
			}
		}else{
			$priceout = $purchaseTypeClass->displayPrice();
		}
		$priceTable = htmlBase::newElement('table')
				->setCellPadding(3)
				->setCellSpacing(0);
		$priceTable->addBodyRow(array(
		                             'columns' => array(
			                             array('addCls' => 'productListingColBoxContent_price_tag', 'text' => '<b>Rental Price :</b>'),
			                             array('addCls' => 'productListingColBoxContent_price', 'text' => $priceout)
		                             )
		                        ));
		$productListingColBoxInner = htmlBase::newElement('div')
				->addClass('productListingColBoxInner')
				->css(array('min-height' => '240px'))
				->append($productListingColBoxTitle)
				->append($productListingColBoxContent)
				->html($contentsText);
				//->append($priceTable);
		$productListingColBoxContainer = htmlBase::newElement('div')
				->addClass('productListingColBoxContainer')
				->css(array(
					      'width' => $imageContainerWidth . 'px'
				      ))
				->append($productListingColBoxInner);
		$productListingColContents->append($productListingColBoxContainer);
	}
	echo htmlBase::newElement('div')
			->addClass('productListingColContents')
			->append($productListingColContents)
			->draw();
	?>

</div>
	<?php

} else {
	echo $listingData;
}
?>