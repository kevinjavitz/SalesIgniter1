<?php

class productGroups_admin_membership_packages extends Extension_productGroups {

	public function __construct(){
		parent::__construct('productGroups');
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'MembershipPackageEditWindowBeforeDraw'
		), null, $this);
	}
	
	public function MembershipPackageEditWindowBeforeDraw(&$infoBox, &$Package){
		if(sysConfig::get('EXTENSION_PAY_PER_RENTAL_ALLOW_MEMBERSHIP') == 'True'){

			$QProductGroups = Doctrine_Query::create()
				->from('ProductsGroups')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$infoBox->addContentRow('<hr><br>Tie to product group');
			$pprG = unserialize($Package->ppr_prod_group);
			foreach($QProductGroups as $pGroup){
				$prodGroup = htmlBase::newElement('input')
				->setName('ppr_prod_group['.$pGroup['product_group_id'].']')
				->setValue($pprG[$pGroup['product_group_id']]);


				$infoBox->addContentRow('Limit for Group: '.$prodGroup->draw().' - '.$pGroup['product_group_name']);
			}


		}

	}
}
