<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of new
 *
 * @author Stephen
 */
class productAddons_admin_orderCreator_default_new extends Extension_productAddons {

	public function __construct(){
		parent::__construct();
	}

	public function load(){
		global $App;
		if ($this->enabled === false) return;

		EventManager::attachEvents(array(
			'OrderProductAfterProductNameEdit'
		), null, $this);

	}

	public function OrderProductAfterProductNameEdit($oID){
		$addonsPopup = htmlBase::newElement('button')
		->addClass('addonsPopup')
		->setText(sysLanguage::get('ORDER_CREATOR_LOAD_CUSTOMER_FAVORITES'))
		->setName('addonProducts');

		return $addonsPopup->draw();
	}
}
?>