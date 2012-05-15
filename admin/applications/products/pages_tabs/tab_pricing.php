 <?php
 	$contents = EventManager::notifyWithReturn('NewProductPricingTabTop', (isset($Product) ? $Product : false));
 	if (!empty($contents)){
 		foreach($contents as $content){
			echo $content;
		}
	}
 ?>
 <div class="main"><?php
	echo sysLanguage::get('TEXT_PRODUCTS_TAX_CLASS') . 
	     tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . 
	     tep_draw_pull_down_menu('products_tax_class_id', $tax_class_array, (isset($Product) ? $Product['products_tax_class_id'] : ''), ' id="tax_class_id"');
?></div>
 <hr>
 <?php
 	$pricingTabsInfo = array(
 		'new' => 'New',
 		'used' => 'Used',
 		'stream' => 'Streaming',
         'member_stream' => sysLanguage::get('TEXT_STREAMING_MEMBERSHIP'),
 		'download' => 'Download',
 		'rental' => 'Member Rental'
 	);
	 if (isset($Product)){
		 $currentTypes = explode(',', $Product['products_type']);
	 }

	 if (isset($Product)){
		 $currentAllowOverbooking = explode(',', $Product['allow_overbooking']);
	 }


 	$pricingTabsObj = htmlBase::newElement('tabs')
 	->setId('pricingTabs');
 	foreach($pricingTabsInfo as $pricingTypeName => $pricingTypeText){
		$productTypeEnabled = htmlBase::newElement('checkbox')
				->setName('products_type[]')
				->setValue($pricingTypeName);

		if (isset($currentTypes) && in_array($pricingTypeName, $currentTypes)){
			$productTypeEnabled->setChecked(true);
		}
		$inputTable = htmlBase::newElement('table')
			->setCellPadding(2)
			->setCellSpacing(0);

		$inputTable->addBodyRow(array(
				'columns' => array(
					array('text' => sysLanguage::get('TEXT_PRODUCTS_ENABLED')),
					array('text' => $productTypeEnabled->draw())
				)
			));

		if($pricingTypeName == 'new' || $pricingTypeName == 'used'){
			$allowOverbookingeEnabled = htmlBase::newElement('checkbox')
				->setName('allow_overbooking[]')
				->setValue($pricingTypeName);

			if (isset($currentAllowOverbooking) && in_array($pricingTypeName, $currentAllowOverbooking)){
				$allowOverbookingeEnabled->setChecked(true);
			}
			$inputTable->addBodyRow(array(
					'columns' => array(
						array('text' => sysLanguage::get('TEXT_PRODUCTS_ALLOW_OVERBOOKING')),
						array('text' => $allowOverbookingeEnabled->draw())
					)
			));
		}

        if($pricingTypeName !== 'rental' && $pricingTypeName !== 'member_stream' && $pricingTypeName !== 'acquisition_cost'){
			$inputNet = htmlBase::newElement('input')->addClass('netPricing');
			$inputGross = htmlBase::newElement('input')->addClass('grossPricing');
			if ($pricingTypeName == 'new'){
				$inputNet->setName('products_price')
				->setId('products_price')
				->val((isset($Product) ? $Product['products_price'] : ''));

				$inputGross->setName('products_price_gross')
				->setId('products_price_gross')
				->val((isset($Product) ? $Product['products_price'] : ''));
			}else{
				$inputNet->setName('products_price_' . $pricingTypeName)
				->setId('products_price_' . $pricingTypeName)
				->val((isset($Product) ? $Product['products_price_' . $pricingTypeName] : ''));

				$inputGross->setName('products_price_' . $pricingTypeName . '_gross')
				->setId('products_price_' . $pricingTypeName . '_gross')
				->val((isset($Product) ? $Product['products_price_' . $pricingTypeName] : ''));
			}

			$inputTable->addBodyRow(array(
			                             'columns' => array(
				                             array('text' => sysLanguage::get('TEXT_PRODUCTS_PRICE_NET')),
				                             array('text' => $inputNet->draw())
			                             )
			                        ));
			$inputTable->addBodyRow(array(
			                             'columns' => array(
				                             array('text' => sysLanguage::get('TEXT_PRODUCTS_PRICE_GROSS')),
				                             array('text' => $inputGross->draw())
			                             )
			                        ));

		}elseif($pricingTypeName == 'rental'){
	        $inputNet = htmlBase::newElement('input')->addClass('netPricing');
	        $inputNet->setName('products_keepit_price')
		        ->setId('products_keepit_price')
		        ->val((isset($Product) ? $Product['products_keepit_price'] : ''));

			$inputTable->addBodyRow(array(
					'columns' => array(
						array('text' => sysLanguage::get('TEXT_PRODUCTS_PRICE_NET')),
						array('text' => $inputNet->draw())
					)
				));

                }

		EventManager::notify('NewProductPricingTabBottom', (isset($Product) ? $Product : false), &$inputTable, &$pricingTypeName);
		
 		$pricingTabsObj->addTabHeader('productPricingTab_' . $pricingTypeName, array('text' => $pricingTypeText))
 		->addTabPage('productPricingTab_' . $pricingTypeName, array('text' => $inputTable));
 	}
	 $multiStore = $appExtension->getExtension('multiStore');
	 if ($multiStore !== false && $multiStore->isEnabled() === true){
		 if (isset($multiStore->pagePlugin)){
			 $multiStore->pagePlugin->loadTabsPricing($pricingTabsObj);
		 }
	 }

 	echo $pricingTabsObj->draw();
?>