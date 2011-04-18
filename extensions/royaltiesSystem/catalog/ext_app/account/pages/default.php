<?php
	class royaltiesSystem_catalog_account_default extends Extension_royaltiesSystem {
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
			$Customer = Doctrine::getTable('Customers')->find($userAccount->getCustomerId());
			if ($Customer->is_content_provider == 0){
				return '';
			}
			$links = htmlBase::newElement('a')
			->html(sysLanguage::get('VIEW_ROYALTIES'))
			->setHref(itw_app_link('appExt=royaltiesSystem', 'account_addon', 'view_royalties', 'SSL'));

			return $links->draw();
		}
	}
?>