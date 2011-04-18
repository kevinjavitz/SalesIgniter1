<?php
class Extension_waitingList extends ExtensionBase {
			  
	public function __construct(){
		parent::__construct('waitingList');
	}
	
	public function init(){
		global $appExtension;
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'ProductInfoPurchaseBoxOnLoad',
			'ProductListingModuleShowBeforeShow',
			'ApplicationTopActionCheckPost',
			'ApplicationTopAction_notify_new_product',
			'ApplicationTopAction_notify_used_product',
			'ApplicationTopAction_notify_reservation_product'
		), null, $this);
	}
	
	public function ProductInfoPurchaseBoxOnLoad(&$settings, $typeName, $purchaseTypes){
		global $userAccount;
		if ($typeName == 'reservation'){
			$removeSessions = false;
			if (Session::exists('isppr_date_start') === false){
				$removeSessions = true;
				Session::set('isppr_date_start', date('Y-m-d', mktime(0,0,0,date('m'), date('d'), date('Y'))));
				Session::set('isppr_date_end', date('Y-m-d', mktime(0,0,0,date('m'), date('d')+1, date('Y'))));
			}
		}
		if ($purchaseTypes[$typeName]->hasInventory() === false){
			$settings['allowQty'] = false;
			$settings['button'] = htmlBase::newElement('button')
			->setType('submit')
			->setName('notify_' . $typeName . '_product')
			->setText('Notify Me When In Stock');

			if ($userAccount->isLoggedIn() === true){
				$settings['button']
					->attr('data-product_id', $purchaseTypes[$typeName]->getProductId())
					->attr('data-purchase_type', $typeName)
					->addClass('waitingListButton');
			}
		}
		if ($typeName == 'reservation'){
			if ($removeSessions === true){
				Session::remove('isppr_date_start');
				Session::remove('isppr_date_end');
			}
		}
	}
	
	public function ProductListingModuleShowBeforeShow($typeName, $productClass, &$Button){
		global $userAccount;
		if ($typeName == 'reservation'){
			$removeSessions = false;
			if (Session::exists('isppr_date_start') === false){
				$removeSessions = true;
				Session::set('isppr_date_start', date('Y-m-d', mktime(0,0,0,date('m'), date('d'), date('Y'))));
				Session::set('isppr_date_end', date('Y-m-d', mktime(0,0,0,date('m'), date('d')+1, date('Y'))));
			}
		}
		$PurchaseType = $productClass->getPurchaseType($typeName);
		if ($PurchaseType->hasInventory() === false){
			$Button = htmlBase::newElement('button')
			->setName('notify_' . $typeName . '_product')
			->setText('Notify');

			if ($userAccount->isLoggedIn() === true){
				$Button
					->attr('data-product_id', $PurchaseType->getProductId())
					->attr('data-purchase_type', $typeName)
					->addClass('waitingListButton');
			}else{
				$Button->setHref(itw_app_link('action=notify_' . $typeName . '_product&products_id=' . $PurchaseType->getProductId()));
			}
		}
		if ($typeName == 'reservation'){
			if ($removeSessions === true){
				Session::remove('isppr_date_start');
				Session::remove('isppr_date_end');
			}
		}
	}

	public function ApplicationTopActionCheckPost(&$action){
		if (isset($_POST['notify_new_product']))         $action = 'notify_new_product';
		if (isset($_POST['notify_used_product']))        $action = 'notify_used_product';
		if (isset($_POST['notify_reservation_product'])) $action = 'notify_reservation_product';
	}
	
	public function ApplicationTopAction_notify_new_product(){
		$productsId = (isset($_POST['products_id']) ? $_POST['products_id'] : (isset($_GET['products_id']) ? $_GET['products_id'] : null));

		tep_redirect(itw_app_link(
			'appExt=waitingList&pID=' . $productsId . '&purchaseType=new',
			'notify',
			'default'
		));
	}
	
	public function ApplicationTopAction_notify_used_product(){
		$productsId = (isset($_POST['products_id']) ? $_POST['products_id'] : (isset($_GET['products_id']) ? $_GET['products_id'] : null));

		tep_redirect(itw_app_link(
			'appExt=waitingList&pID=' . $productsId . '&purchaseType=used',
			'notify',
			'default'
		));
	}
	
	public function ApplicationTopAction_notify_reservation_product(){
		$productsId = (isset($_POST['products_id']) ? $_POST['products_id'] : (isset($_GET['products_id']) ? $_GET['products_id'] : null));

		tep_redirect(itw_app_link(
			'appExt=waitingList&pID=' . $productsId . '&purchaseType=reservation',
			'notify',
			'default'
		));
	}
}
?>