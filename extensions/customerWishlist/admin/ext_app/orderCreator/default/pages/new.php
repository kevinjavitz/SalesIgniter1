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
class customerFavorites_admin_orderCreator_default_new extends Extension_customerFavorites {

	public function __construct(){
		parent::__construct();
	}

	public function load(){
		global $App;
		if ($this->enabled === false) return;

		EventManager::attachEvents(array(
			'OrderInfoBeforeProductListingEdit'
		), null, $this);

	}

	public function OrderInfoBeforeProductListingEdit($oID){
		$customerFavorites = htmlBase::newElement('button')
		->addClass('custFavoritesPopup')
		->setText(sysLanguage::get('ORDER_CREATOR_LOAD_CUSTOMER_FAVORITES'))
		->setName('custFavorites');

		echo $customerFavorites->draw(). '<div class="custDialog"></div><div class="selectDialogFavorites"></div>';
	}
}
?>