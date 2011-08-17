<?php
class referral_catalog_checkout_default extends Extension_referral {
	public function __construct(){
		parent::__construct();
	}

	public function load(){
		if ($this->enabled === false) return;

		EventManager::attachEvents(array(
		                                'CheckoutAddBlockAfterCart',
		                                'CheckoutProcessPostProcess'
		                           ), null, $this);
	}

	public function CheckoutAddBlockAfterCart(){
		$htmlTable = htmlBase::newElement('table')
				->setCellPadding(2)
				->setCellSpacing(0)
				->addClass('ui-widget')
				->css(array(
				           'width' => '100%'
				      ));
		$referral = htmlBase::newElement('input')
				->setName('referral')
				->setType('text');
		$referralRedeemButton = htmlBase::newElement('button')
				->css(array('padding'=>'5px'))
				->setName('redeemReferral')
				->setId('redeemReferral')
				->setText(sysLanguage::get('TEXT_REDEEM_REFERRAL'));
		$htmlTable->addBodyRow(array(
		                            'columns' => array(
			                            array('addCls' => 'main',
			                                  'css'=>array('text-align'=>'left', 'font-weight'=>'bold'),
			                                  'text' => sysLanguage::get('TEXT_REFERRAL'))
		                            )));
		$htmlTable->addBodyRow(array(
		                            'columns' => array(
			                            array('addCls' => 'main',
			                                  'css'=>array('text-align'=>'left'),
			                                  'text' => sysLanguage::get('TEXT_ENTER_REFERRAL') . '&nbsp;&nbsp;' . $referral->draw() . '&nbsp;&nbsp;' . $referralRedeemButton->draw())
		                            )));
		echo $htmlTable->draw();
	}

	public function sendReferralEarnedEmail(){
		$referralCode = '';
		$cid = @end(explode('!',$referralCode));
		if($cid <= 0)
			return false;

		$userAccount = RentalStoreUser($cid);
		$firstName = $userAccount->customerInfo['firstName'];
		$lastName = $userAccount->customerInfo['lastName'];
		$emailAddress = $this->customerInfo['emailAddress'];
		$fullName = $userAccount->customerInfo['firstName'] . ' ' . $this->customerInfo['lastName'];


		$emailEvent = new emailEvent('create_account');

		$emailEvent->setVars(array(
		                          'email_address' => $emailAddress,
		                          'pointsEarned'  => sysConfig::get('EXTENSION_REFFERAL_SYSTEM_REWARD_POINTS'),
		                          'firstname'     => $firstName,
		                          'lastname'      => $lastName,
		                          'full_name'     => $fullName
		                     ));

		if (isset($this->newCustomerEmailVars)){
			foreach($this->newCustomerEmailVars as $var => $val){
				$emailEvent->setVar($var, $val);
			}
		}

		$emailEvent->sendEmail(array(
		                            'email' => $emailAddress,
		                            'name'  => $fullName
		                       ));
	}

	public function CheckoutProcessPostProcess(&$order){
		//var_dump($_POST);
		if($_POST){
			$this->sendReferralEarnedEmail();
		}
	}
}
?>