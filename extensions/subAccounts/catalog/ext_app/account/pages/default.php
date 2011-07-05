<?php
	class subAccounts_catalog_account_default extends Extension_subAccounts {
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
				'AccountDefaultAddLinksBlock'
			), null, $this);
		}


		public function AccountDefaultAddLinksBlock(&$pageContents){
			global $userAccount;
			if(Session::exists('childrenAccount') === false){
				$links = htmlBase::newElement('a')
				->html(sysLanguage::get('TEXT_VIEW_SUBACCOUNTS'))
				->setHref(itw_app_link('appExt=subAccounts', 'manage', 'default', 'SSL'));

				$linkList = htmlBase::newElement('list')
				->css(array(
					'list-style' => 'none',
					'margin' => '1em',
					'padding' => 0
				))
				->addItem('', $links);

				$headingDiv = htmlBase::newElement('div')
				->addClass('main')
				->css(array(
					'font-weight' => 'bold',
					'margin-top' => '1em'
				))
				->html(sysLanguage::get('TEXT_SUBACCOUNTS'));

				$contentDiv = htmlBase::newElement('div')
				->addClass('ui-widget ui-widget-content ui-corner-all')
				->append($linkList);

				$html = $headingDiv->draw() . $contentDiv->draw();
				$pageContents .= $html;
			}
		}
	}
?>