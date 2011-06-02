<?php
	class customerFavorites_catalog_product_info extends Extension_customerFavorites {
		public function __construct(){
			global $App;
			parent::__construct();

		}

		public function load(){
			if ($this->enabled === false) return;

			EventManager::attachEvent('ProductInfoTabImageBeforeDrawPurchaseType', null, $this);
		}
		public function ProductInfoTabImageBeforeDrawPurchaseType(&$product, &$boxObj, &$boxInfo){

			$purchaseType = htmlBase::newElement('input')
			->setType('hidden')
			->setName('favPurchaseType')
			->setValue($boxInfo['purchase_type']);



			$addFavorites = htmlBase::newElement('button')
			->setText(sysLanguage::get('TEXT_BUTTON_ADD_TO_FAVORITES'))
			->setType('submit')
			->addClass('addToFavorites')
			->setName('add_to_favorites');

			$boxInfo['content'] .= $purchaseType->draw();

			$boxObj->addButton($addFavorites);
		}

	}
?>