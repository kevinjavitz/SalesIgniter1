<?php
	class pointsRewards_catalog_account_default extends Extension_pointsRewards {
		public function __construct(){
			global $App;
			parent::__construct();

			if ($App->getAppName() != 'account' || ($App->getAppName() == 'account' && $App->getPageName() != 'default')){
				$this->enabled = false;
			}
		}

		public function load(){
			if ($this->enabled === false) return;

			EventManager::attachEvents(array(
			                                'AccountDefaultMyAccountAddLink',
			                                'AccountDefaultAddLinksBlock'
			                           ), null, $this);
		}

		public function AccountDefaultMyAccountAddLink(){
			global $userAccount;
			$Customer = Doctrine::getTable('Customers')->find($userAccount->getCustomerId());

			$links[] = htmlBase::newElement('a')
			->html(sysLanguage::get('VIEW_POINTS'))
			->setHref(itw_app_link('appExt=pointsRewards', 'account_addon', 'view_points_history', 'SSL'))
			->draw();



			return $links;
		}
	
		public function AccountDefaultAddLinksBlock($pageContents){
			global $appExtension,$currencies;

			$extRewards = $appExtension->getExtension('pointsRewards');
			$pageContents = '<div id="headerText" style="margin-top:1em;">' .
							'<b>' . sprintf(sysLanguage::get('POINTS_EARNED'), $currencies->currencies[DEFAULT_CURRENCY]['symbol_left'].$extRewards->getPointsEarned()) . '</b>' .
							'<br><br></div>' .
			                $pageContents;
		}
	}
?>