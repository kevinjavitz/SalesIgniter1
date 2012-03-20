<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
class InfoBoxSignupBox extends InfoBoxAbstract {
	public function __construct(){
		global $App;
		$this->init('signupBox');
		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_SIGNUPBOX'));
	}

	public function show(){
		global $App, $userAccount;
		$boxWidgetProperties = $this->getWidgetProperties();
		$dontShow = array('login',
			'create');
		if(!in_array($App->getAppName(), $dontShow) && $userAccount->isLoggedIn() === false){
			if($userAccount->isLoggedIn() === false){
				$loginboxcontent = htmlBase::newElement('form')
					->attr('action', itw_app_link('checkoutType=rental', 'checkout', 'default', 'SSL'))
					->css(array('text-align' => 'left'))
					->attr('id', 'signupform')
					->attr('method', 'post');
				$loginTable = htmlBase::newElement('table')
					->setCellPadding(2)
					->setCellSpacing(0)
					->css(array('width' => '95%',
					'margin' => '10px auto'));
				$loginEmailAddress = htmlBase::newElement('input')
					->css('width', '90%')
					->setName('email_address1');
				$loginTable->addBodyRow(array('columns' => array(array('text' => sysLanguage::get('INFOBOX_LOGINBOX_EMAIL')))));
				$loginTable->addBodyRow(array('columns' => array(array('text' => $loginEmailAddress))));
				$loginEmailAddress1 = htmlBase::newElement('input')
					->css('width', '90%')
					->setName('email_address2');
				$loginTable->addBodyRow(array('columns' => array(array('text' => sysLanguage::get('INFOBOX_LOGINBOX_RE_EMAIL')))));
				$loginTable->addBodyRow(array('columns' => array(array('text' => $loginEmailAddress1))));
				$loginPassword = htmlBase::newElement('input')
					->css('width', '90%')
					->setType('password')
					->setName('password1');
				$loginTable->addBodyRow(array('columns' => array(array('text' => sysLanguage::get('INFOBOX_LOGINBOX_PASSWORD')))));
				$loginTable->addBodyRow(array('columns' => array(array('text' => $loginPassword))));
				$loginPassword1 = htmlBase::newElement('input')
					->css('width', '90%')
					->setType('password')
					->setName('password2');
				$loginTable->addBodyRow(array('columns' => array(array('text' => sysLanguage::get('INFOBOX_LOGINBOX_RE_PASSWORD')))));
				$loginTable->addBodyRow(array('columns' => array(array('text' => $loginPassword1))));
				$loginSubmit = htmlBase::newElement('button')
					->setType('submit')
					->usePreset('save')
					->setName('saveBtn')
					->setText(sysLanguage::get('INFOBOX_LOGINBOX_LOGIN'));
				$loginTable->addBodyRow(array('columns' => array(array('align' => 'center',
					'text' => $loginSubmit))));
				$loginTable->addBodyRow(array('columns' => array(array('css' => array('font-size' => '9px'),
					'text' => sysLanguage::get('INFOBOX_LOGINBOX_INFO')))));
				$loginboxcontent->append($loginTable);
				$boxContent = $loginboxcontent->draw();
			} else {
				// If you want to display anything when the user IS logged in, put it
				// in here...  Possibly a "You are logged in as :" box or something.
			}
			// WebMakers.com Added: My Account Info Box
		} else {
			if($userAccount->isLoggedIn() === true){
				$this->setBoxHeading(sysLanguage::get('INFOBOX_LOGINBOX_MY_ACCOUNT'));
				$boxContent = '<a href="' . itw_app_link(null, 'account', 'default', 'SSL') . '">' . sysLanguage::get('INFOBOX_LOGINBOX_MY_ACCOUNT') . '</a><br>' . '<a href="' . itw_app_link(null, 'account', 'edit', 'SSL') . '">' . sysLanguage::get('INFOBOX_LOGINBOX_ACCOUNT_EDIT') . '</a><br>' . '<a href="' . itw_app_link(null, 'account', 'history', 'SSL') . '">' . sysLanguage::get('INFOBOX_LOGINBOX_ACCOUNT_HISTORY') . '</a><br>' . '<a href="' . itw_app_link(null, 'account', 'address_book', 'SSL') . '">' . sysLanguage::get('INFOBOX_LOGINBOX_ADDRESS_BOOK') . '</a><br>' . '<a href="' . itw_app_link(null, 'account', 'logoff') . '">' . sysLanguage::get('INFOBOX_LOGINBOX_LOGOFF') . '</a>';
			}
		}
		if(isset($boxContent)){
			$this->setBoxContent($boxContent);
			if($userAccount->isRentalMember() === false){
				$contentTable = htmlBase::newElement('table')
					->setCellPadding(2)
					->setCellSpacing(0)
					->css(array('width' => '100%',
					'margin' => '5px 4px'));
				$contentTable->addBodyRow(array('columns' => array(/* array('css' => array('width' => '460px', 'vertical-align' => 'top'), 'text' => '<img src="/templates/faithflix/images/bannerbig.jpg">'),
												   array('css' => array('width' => '200px', 'vertical-align' => 'top'), 'text' => '<div>
		<span style="font-size:20px;"><span style="color:#0196cf;">Featuring Family Approved, Faith Asserting Movies</span></span></div>
	<div>
		&nbsp;</div>
	<div>
		<span style="font-size:20px;"><span style="color:#0196cf;">Plans Start At Just</span> <span style="color:#b22222;">$7.99</span> <span style="color:#0196cf;">Per Month</span></span></div>
	<div>
		&nbsp;</div>
	<div>
		<span style="font-size:20px;"><span style="color:#0196cf;">No Late Fees</span></span></div>'),*/
					array('css' => array('width' => '280px',
						'vertical-align' => 'top'),
						'text' => $this->draw()))));
				return $contentTable->draw();
			}
		}
		return false;
	}

	public function buildJavascript(){
		$boxWidgetProperties = $this->getWidgetProperties();
		$js = '';
		$js .= //'	$(document).ready(function (){' . "\n" .
			'	    $("#signupform").submit(function() {

		 			var haserror = false;

		 			var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

		 			var email1 = $("input[name=email_address1]").val();

					var email2 = $("input[name=email_address2]").val();

					var pass1 = $("input[name=password1]").val();

					var pass2 = $("input[name=password2]").val();



					if(email1 == "" || email2=="" || pass1=="" || pass2==""){

							alert("All fields are required, please fill the form again.");

							haserror=true;

					}

					if(!emailReg.test(email1)) {

						alert("Please enter a valid email.");

						haserror = true;

					}

					if(email1 != email2 ){

						alert("The email fields must match.");

						haserror = true;

					}

					if(pass1.length < 5){

						alert("Password Length must be 5 or greater.");

						haserror = true;

					}

					if( pass1 != pass2){

						alert("The passwords must match.");

						haserror = true;

					}

					if(haserror == true){

						return false;

					}else{

						return true;

					}

				});

			';
		//	});' . "\n";
		return $js;
	}
}
?>