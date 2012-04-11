<?php
	class customerWishlist_catalog_account_default extends Extension_customerWishlist {
		public function __construct(){
			global $App;
			parent::__construct();

			if ($App->getAppName() != 'account' || ($App->getAppName() == 'account' && $App->getPageName() != 'default')){
				$this->enabled = false;
			}
		}

		public function load(){
			if ($this->enabled === false) return;

			EventManager::attachEvent('AccountDefaultMyAccountAddLink', null, $this);
		}

		public function AccountDefaultMyAccountAddLink(){

			$links = htmlBase::newElement('a')
			->html(sysLanguage::get('VIEW_WISHLIST'))
			->setHref(itw_app_link('appExt=customerWishlist', 'account_addon', 'manage_wishlist', 'SSL'));

			return $links->draw();
		}
	}
?>