<?php

class postaffiliatepro_admin_membership_packages extends Extension_postaffiliatepro {

	public function __construct(){
		parent::__construct('postaffiliatepro');
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'MembershipPackageEditWindowBeforeDraw'
		), null, $this);
	}
	
	public function MembershipPackageEditWindowBeforeDraw(&$infoBox, &$Package){
		$isAffiliate = htmlBase::newElement('checkbox')
		->setName('is_affiliate')
		->val('1')
		->setChecked(($Package->is_affiliate == '1'));
		
		$infoBox->addContentRow('<hr><br>Affiliate Info');
		$infoBox->addContentRow('Can be affiliate' . '<br>' . $isAffiliate->draw());

	}
}
