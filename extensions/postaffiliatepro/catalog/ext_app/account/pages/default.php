<?php
	class postaffiliatepro_catalog_account_default extends Extension_postaffiliatepro {
		public function __construct(){
			global $App;
			parent::__construct();

			if ($App->getAppName() != 'account' || ($App->getAppName() == 'account' && $App->getPageName() != 'default')){
				$this->enabled = false;
			}
		}

		public function load(){
			if ($this->isEnabled() === false) return;

			EventManager::attachEvents(array(
				'AccountDefaultAddLinksBlock'
			), null, $this);
		}


		public function AccountDefaultAddLinksBlock(&$pageContents){
			global $userAccount;

			//here I get the affiliate data and set the link to affiliate panel
			if(file_exists(sysConfig::getDirFsCatalog().'ext/pap/api/PapApi.class.php')){
				include_once(sysConfig::getDirFsCatalog().'ext/pap/api/PapApi.class.php');
				$session = new Gpf_Api_Session(sysConfig::get('EXTENSION_PAP_URL').'scripts/server.php');
				if(!$session->login(sysConfig::get('EXTENSION_PAP_MERCH'), sysConfig::get('EXTENSION_PAP_PASS'))) {
					return;
				}
				$affiliate = new Pap_Api_Affiliate($session);
				$affiliate->setUsername($userAccount->getEmailAddress());
				$error = false;
				try {
					$affiliate->load();
				} catch (Exception $e) {
					$error = true;
					//die("Cannot load record: ".$affiliate->getMessage());
				}
				if(!$error){
				$session1 = new Gpf_Api_Session(sysConfig::get('EXTENSION_PAP_URL').'scripts/server.php');
				if(!$session1->login($affiliate->getUsername(), $affiliate->getPassword(), Gpf_Api_Session::AFFILIATE)) {

				}



					$links = htmlBase::newElement('a')
					->html(sysLanguage::get('TEXT_LOGIN_PAP4'))
					->setHref($session1->getUrlWithSessionInfo(sysConfig::get('EXTENSION_PAP_URL').'affiliates/panel.php'));

					$links1 = htmlBase::newElement('a')
					->html(sysLanguage::get('TEXT_CHANGE_USERNAME'))
					->setHref(itw_app_link('appExt=postaffiliatepro','change_username','default'));

					$linkList = htmlBase::newElement('list')
					->css(array(
						'list-style' => 'none',
						'margin' => '1em',
						'padding' => 0
					))
					->addItem('', $links)
					->addItem('', $links1);

					$headingDiv = htmlBase::newElement('div')
					->addClass('main')
					->css(array(
						'font-weight' => 'bold',
						'margin-top' => '1em'
					))
					->html(sysLanguage::get('TEXT_PAP4'));

					$contentDiv = htmlBase::newElement('div')
					->addClass('ui-widget ui-widget-content ui-corner-all')
					->append($linkList);

					$html = $headingDiv->draw() . $contentDiv->draw();

					$QUsernameIds = Doctrine_Core::getTable('UsernamesToIds')->find($affiliate->getRefid());
					$link = sysConfig::get('HTTP_SERVER').'/pap/'.$QUsernameIds->username;
					$pageContents .= sprintf(sysLanguage::get('TEXT_GO_AFFILIATE'),$link, $link).'<br/>'.$html;
				}else{
					$pageContents .= '';
				}
			}
		}
	}
?>