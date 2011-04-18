<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxLoginBox extends InfoBoxAbstract
{

	public function __construct() {
		global $App;
		$this->init('loginBox');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_LOGINBOX'));
	}

	public function show() {
		global $App, $userAccount;
		$dontShow = array(
			'login',
			'create'
		);
		if (!in_array($App->getAppName(), $dontShow) && $userAccount->isLoggedIn() === false){
			if ($userAccount->isLoggedIn() === false){
				$loginboxcontent = htmlBase::newElement('form')
					->attr('action', itw_app_link('action=processLogin', 'account', 'login', 'SSL'))
					->css(array(
						'text-align' => 'left'
					))
					->attr('method', 'post');

				$loginTable = htmlBase::newElement('table')
					->setCellPadding(2)
					->setCellSpacing(0)
					->css('width', '98%');

				$loginEmailAddress = htmlBase::newElement('input')
					->css('width', '100%')
					->setName('email_address');

				$loginTable->addBodyRow(array(
					'columns' => array(
						array('text' => sysLanguage::get('INFOBOX_LOGINBOX_EMAIL'))
					)
				));

				$loginTable->addBodyRow(array(
					'columns' => array(
						array('text' => $loginEmailAddress)
					)
				));

				$loginPassword = htmlBase::newElement('input')
					->css('width', '100%')
					->setType('password')
					->setName('password');

				$loginTable->addBodyRow(array(
					'columns' => array(
						array('text' => sysLanguage::get('INFOBOX_LOGINBOX_PASSWORD'))
					)
				));

				$loginTable->addBodyRow(array(
					'columns' => array(
						array('text' => $loginPassword)
					)
				));

				$loginSubmit = htmlBase::newElement('button')
					->setType('submit')
					->usePreset('save')
					->setText(sysLanguage::get('INFOBOX_LOGINBOX_LOGIN'));

				$loginTable->addBodyRow(array(
					'columns' => array(
						array('align' => 'right', 'text' => $loginSubmit)
					)
				));

				$loginboxcontent->append($loginTable);
				$boxContent = $loginboxcontent->draw();
			}
			else {
				// If you want to display anything when the user IS logged in, put it
				// in here...  Possibly a "You are logged in as :" box or something.
			}
			// WebMakers.com Added: My Account Info Box
		}
		else {
			if ($userAccount->isLoggedIn() === true){
				$this->setBoxHeading(sysLanguage::get('INFOBOX_LOGINBOX_MY_ACCOUNT'));
				$boxContent = '<a href="' . itw_app_link(null, 'account', 'default', 'SSL') . '">' . sysLanguage::get('INFOBOX_LOGINBOX_MY_ACCOUNT') . '</a><br>' .
					'<a href="' . itw_app_link(null, 'account', 'edit', 'SSL') . '">' . sysLanguage::get('INFOBOX_LOGINBOX_ACCOUNT_EDIT') . '</a><br>' .
					'<a href="' . itw_app_link(null, 'account', 'history', 'SSL') . '">' . sysLanguage::get('INFOBOX_LOGINBOX_ACCOUNT_HISTORY') . '</a><br>' .
					'<a href="' . itw_app_link(null, 'account', 'address_book', 'SSL') . '">' . sysLanguage::get('INFOBOX_LOGINBOX_ADDRESS_BOOK') . '</a><br>' .
					'<a href="' . itw_app_link(null, 'account', 'logoff') . '">' . sysLanguage::get('INFOBOX_LOGINBOX_LOGOFF') . '</a>';
			}
		}

		if (isset($boxContent)){
			$this->setBoxContent($boxContent);

			return $this->draw();
		}
		return false;
	}
}

?>