<?php
$saveForm = htmlBase::newElement('form')
		->attr('name', 'purchaseTypeSelect')
		->attr('action', itw_app_link('appExt=pointsRewards&action=save', 'purchase_type_configuration_options', 'default'))
		->attr('method', 'post');

$purchaseTypes = array(
 		'new' => 'New',
 		'used' => 'Used'
 	);
if (defined('EXTENSION_PAY_PER_RENTALS_ENABLED') && EXTENSION_PAY_PER_RENTALS_ENABLED == 'True'){
	$purchaseTypes['reservation'] = 'Pay per rental';
}
if (defined('EXTENSION_STREAMPRODUCTS_ENABLED') && EXTENSION_STREAMPRODUCTS_ENABLED == 'True'){
	$purchaseTypes['stream'] = 'Streaming';
}
if (defined('EXTENSION_DOWNLOADPRODUCTS_ENABLED') && EXTENSION_DOWNLOADPRODUCTS_ENABLED == 'True'){
	$purchaseTypes['download'] = 'Download';
}

$purchaseTypeTabsObj = htmlBase::newElement('tabs')
 	->setId('purchaseTypeTabs');
 	foreach($purchaseTypes as $purchaseTypeName => $purchaseTypeText){


		$productTypeEnabled = htmlBase::newElement('checkbox')
				->setName('enabled[' . $purchaseTypeName . ']')
				->setValue($purchaseTypeName);
		$percentage = htmlBase::newElement('input')
				->setName('percentage[' . $purchaseTypeName . ']');
		$threshold = htmlBase::newElement('input')
				->setName('threshold[' . $purchaseTypeName . ']');
		$conversionRatio = htmlBase::newElement('input')
				->setName('conversionratio[' . $purchaseTypeName . ']');
		$pointsRewardsPurchaseTypes = Doctrine_Core::getTable('pointsRewardsPurchaseTypes')->findOneByPurchaseType($purchaseTypeName);
		if (isset($pointsRewardsPurchaseTypes) && $pointsRewardsPurchaseTypes != null){
			$productTypeEnabled->setChecked(true);
			$percentage->setValue($pointsRewardsPurchaseTypes->percentage);
			$threshold->setValue($pointsRewardsPurchaseTypes->threshold);
			$conversionRatio->setValue($pointsRewardsPurchaseTypes->conversionRatio);
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
		$inputTable->addBodyRow(array(
		                             'columns' => array(
			                             array('text' => sysLanguage::get('TEXT_PERCENTAGE_REWARD')),
			                             array('text' => $percentage->draw())
		                             )
		                        ));
		$inputTable->addBodyRow(array(
		                             'columns' => array(
			                             array('text' => sysLanguage::get('TEXT_REDEEM_THRESHOLD')),
			                             array('text' => $threshold->draw())
		                             )
		                        ));
		$inputTable->addBodyRow(array(
									 'columns' => array(
										 array('text' => sysLanguage::get('TEXT_REDEEM_CONVERSION_RATIO')),
										 array('text' => $conversionRatio->draw())
									 )
								));
 		$purchaseTypeTabsObj->addTabHeader('purchaseTypeTab_' . $purchaseTypeName, array('text' => $purchaseTypeText))
 		->addTabPage('purchaseTypeTab_' . $purchaseTypeName, array('text' => $inputTable));
 	}
	$saveButton = htmlBase::newElement('button')->setText('Save')->addClass('newButton')->setType('submit');
	$saveForm->append($saveButton);
	$saveForm->append($purchaseTypeTabsObj);
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<br />
<div style="text-align:right;" class="gridContainer">
	<?php echo $saveForm->draw(); ?>
</div>