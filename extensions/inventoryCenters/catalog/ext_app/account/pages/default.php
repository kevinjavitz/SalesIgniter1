<?php
	class inventoryCenters_catalog_account_default extends Extension_inventoryCenters {
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
			global $userAccount;
			if ($userAccount->isProvider() === false) return '';

			$links = htmlBase::newElement('a')
			->html(sysLanguage::get('VIEW_ORDERS'))
			->setHref(itw_app_link('appExt=inventoryCenters', 'account_addon', 'view_orders_inventory', 'SSL'));

			return $links->draw();
		}
	}
?>