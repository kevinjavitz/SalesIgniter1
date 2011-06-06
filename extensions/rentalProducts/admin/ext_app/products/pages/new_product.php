<?php
class rentalProducts_admin_products_new_product extends Extension_rentalProducts {

	public function __construct(){
		parent::__construct();
	}

	public function load(){
		if ($this->enabled === false) return;

		EventManager::attachEvents(array(
				'NewProductPricingTabBottom'
			), null, $this);
	}

	public function NewProductPricingTabBottom($Product, &$inputTable, &$purchaseType){
		if ($purchaseType->getCode() == 'rental'){
			$rentalPeriod = $Product['ProductsRentalSettings']['rental_period'];
			if ($rentalPeriod == '' || $rentalPeriod <= 0){
				$rentalPeriod = $purchaseType->getConfigData('MAXIMUM_ALLOWED_OUT');
			}

			$inputTable->addBodyRow(array(
					'columns' => array(
						array('text' => sysLanguage::get('TEXT_ENTRY_RENTAL_PERIOD')),
						array('text' => $rentalPeriod . sysLanguage::get('TEXT_DAYS'))
					)
				));
		}
	}
}