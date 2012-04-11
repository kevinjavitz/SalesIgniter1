<?php
	class customerWishlist_catalog_product_info extends Extension_customerWishlist {
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



			$addWishlist = htmlBase::newElement('button')
			->setText(sysLanguage::get('TEXT_BUTTON_ADD_TO_WISHLIST'))
			->setType('submit')
			->addClass('addToWishlist')
			->setName('add_to_wishlist');

			$boxInfo['content'] .= $purchaseType->draw();

			$boxObj->addButton($addWishlist);
		}

	}
?>