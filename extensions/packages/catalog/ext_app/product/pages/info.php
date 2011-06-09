<?php
	class packages_catalog_product_info extends Extension_packages {
		public function __construct(){
			global $App;
			parent::__construct();

		}

		public function load(){
			if ($this->enabled === false) return;

			EventManager::attachEvent('ProductInfoTabImageBeforeDrawPurchaseType', null, $this);
		}

	}
?>