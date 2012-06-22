<?php
/*
	Referral System Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
class Extension_referral extends ExtensionBase {
	public function __construct(){
		parent::__construct('referral');
	}

	public function init(){
		global $appExtension;
		EventManager::attachEvents(array(
		                                'EmailEventPreParseTemplateText_create_account',
			                            'AccountDefaultAddLinksBlock'
		                           ), null, $this);
	}

	public function AccountDefaultAddLinksBlock($pageContents){
		global $currencies;

		$pageContents = '<div class="main" style="margin-top:1em;">' .
		                '<b>' . sprintf(sysLanguage::get('TEXT_USE_REFERRAL_CODE'), $this->getReferralCode(), $currencies->currencies[DEFAULT_CURRENCY]['symbol_left'].sysConfig::get('EXTENSION_REFFERAL_SYSTEM_REWARD_POINTS')) . '</b>' .
		                '</div>' .
		                $pageContents;
	}

	public function getReferralCode(){
		global $userAccount;
		if(is_object($userAccount)){
		$userAccount = &Session::getReference('userAccount');
		return $userAccount->getFirstName() . '!' . $userAccount->getCustomerId();
		}
		return false;
	}

	public function EmailEventPreParseTemplateText_create_account(&$templateText){

		$code = $this->getReferralCode();
		$emailText = str_replace('__CODE__',$code,sysConfig::get('EXTENSION_REFFERAL_SYSTEM_EMAIL_TEXT'));
		$templateText = str_replace('<--APPEND-->',"<--APPEND-->\n" . $emailText,$templateText);
	}

	public function sendReferralEarnedEmail($referralCode,&$Coupon){
		$refCode = explode('!',$referralCode);
		$customersFirstName = $refCode[0];
		$customersId = $refCode[1];

		$cInfo = Doctrine_Core::getTable('customers')
					->findOneByCustomersId($customersId);

		if($customersFirstName == $cInfo->customers_firstname && $customersId == $cInfo->customers_id){
			$thisUserAccount = &Session::getReference('userAccount');

			$userAccount = new rentalStoreUser($customersId);
			$firstName = $userAccount->getFirstName();
			$lastName = $userAccount->getLastName();
			$emailAddress = $userAccount->getEmailAddress();
			$fullName = $firstName . ' ' . $lastName;

			$couponEmailTrack = $Coupon->CouponEmailTrack;
			$couponEmailTrack->customer_id_sent = $customersId;
			$couponEmailTrack->sent_firstname = $firstName;
			$couponEmailTrack->sent_lastname = $lastName;
			$couponEmailTrack->emailed_to = $emailAddress;
			$couponEmailTrack->date_sent = date('Y-m-d');
			$Coupon->save();

			$emailEvent = new emailEvent('referral_earned_create_account');
			$emailEvent->setVars(array(
									  'email_address' => $emailAddress,
									  'pointsEarned'  => sysConfig::get('EXTENSION_REFFERAL_SYSTEM_REWARD_POINTS'),
									  'firstname'     => $thisUserAccount->getFirstName(),
									  'lastname'      => $thisUserAccount->getLastName(),
									  'fullName'     => $thisUserAccount->getFullName()
								 ));

			$emailEvent->sendEmail(array(
										'email' => $emailAddress,
										'name'  => $fullName
								   ));
			return true;
		}
		return false;
	}

	public function sendReferrerFreeShippingCouponEmail($cID,$coupon_code,$referral_code){
	$mInfo = Doctrine_Core::getTable('customers')
					->findOneByCustomersId($cID);

	$from = sysConfig::get('STORE_OWNER_EMAIL_ADDRESS');
	$subject = sysLanguage::get('TEXT_EMAIL_SUBJECT');
		$store = sprintf(sysLanguage::get('STORE_NAME'));
		$message = sysConfig::get('EXTENSION_REFFERAL_SYSTEM_REDEEM_COUPON_EMAIL_TEXT');
		$message .= sysLanguage::get('TEXT_VOUCHER_IS') . $coupon_code . "\n\n";
		//$message .= sysLanguage::get('TEXT_REFERRER_CODE').$referral_code . "\n\n";

		//Let's build a message object using the email class
		$mimemessage = new email(array('X-Mailer: osCommerce bulk mailer'));
		// add the message to the object
		$mimemessage->add_text($message);
		$mimemessage->build_message();
		$mimemessage->send($mInfo->customers_firstname . ' ' . $mInfo->customers_lastname, $mInfo->customers_email_address, '', $from, $subject);

	}
}
