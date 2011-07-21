<?php
class Extension_orderCreator extends ExtensionBase {

	public function __construct(){
		parent::__construct('orderCreator');
	}

	public function preSessionInit(){
		$this->removeSession = true;
		if (isset($_GET['appExt']) && $_GET['appExt'] == 'orderCreator'){
			if (!isset($_GET['action']) && !isset($_POST['action']) && !isset($_GET['error'])){
				$this->removeSession = true;
			}else{
				$this->removeSession = false;
			}
			
			/* 
			 * Require any core classes
			 */
			require(sysConfig::getDirFsCatalog() . 'includes/classes/Order/Base.php');
			
			/*
			 * Require any extension specific classes
			 */
			require(dirname(__FILE__) . '/admin/classes/Order/Base.php');
		}
	}
	
	public function postSessionInit(){
		if (Session::exists('OrderCreator')){
			if (isset($this->removeSession) && $this->removeSession === true){
				Session::remove('OrderCreator');
			}
		}
	}
	
	public function init(){
		global $appExtension;
		EventManager::attachEvents(array(
			'OrdersGridButtonsBeforeAdd',
			'EstimatesGridButtonsBeforeAdd',
			'OrdersListingBeforeExecute',
			'OrdersProductsReservationListingBeforeExecute',
			'AdminOrdersListingBeforeExecute',
			'OrderQueryBeforeExecute',
			'ReservationCheckQueryBeforeExecute',
			'ProductInventoryReportsListingQueryBeforeExecute',
			'CustomerGroupsExportQueryBeforeExecute'

		), null, $this);

		if ($appExtension->isAdmin()){
			EventManager::attachEvent('BoxCustomersAddLink', null, $this);
		}
	}

	public function AdminOrdersListingBeforeExecute(&$Qorders){
		if(!isset($_GET['isEstimate'])){
			$Qorders->andWhere('o.orders_status != ?', sysConfig::get('ORDERS_STATUS_ESTIMATE_ID'));
		}
	}

	public function OrdersProductsReservationListingBeforeExecute(&$Qorders){
		if(!isset($_GET['isEstimate'])){
			$Qorders->andWhere('opr.is_estimate = ?', '0');
		}
	}

	public function OrdersListingBeforeExecute(&$Qorders){
		if(!isset($_GET['isEstimate'])){
			$Qorders->andWhere('o.orders_status != ?', sysConfig::get('ORDERS_STATUS_ESTIMATE_ID'));
		}
	}

	public function OrderQueryBeforeExecute(&$Qorders){
		if(!isset($_GET['isEstimate'])){
			$Qorders->andWhere('o.orders_status != ?', sysConfig::get('ORDERS_STATUS_ESTIMATE_ID'));
		}
	}

	public function ReservationCheckQueryBeforeExecute(&$Qorders, $settings){
		if(!isset($_GET['isEstimate'])){
			$Qorders->andWhere('is_estimate = ?', '0');
		}
	}

	public function ProductInventoryReportsListingQueryBeforeExecute(&$Products){
		if(!isset($_GET['isEstimate'])){
			$Products->andWhere('opr.is_estimate = ?', '0');
		}
	}

	public function CustomerGroupsExportQueryBeforeExecute(&$Qorders){
		if(!isset($_GET['isEstimate'])){
			$Qorders->andWhere('o.orders_status != ?', sysConfig::get('ORDERS_STATUS_ESTIMATE_ID'));
		}
	}



	public function BoxCustomersAddLink(&$contents){
		$contents['children'][] = array(
			'link' => itw_app_link('appExt=orderCreator','estimates','default'),
			'text' => 'Estimates'
		);
	}
	
	public function OrdersGridButtonsBeforeAdd(&$gridButtons){
		$gridButtons[] = htmlBase::newElement('button')
		->setText(sysLanguage::get('TEXT_NEW_ORDER'))
		->addClass('createButton')
		->setHref(itw_app_link('appExt=orderCreator', 'default', 'new'));
		
		$gridButtons[] = htmlBase::newElement('button')
		->setText(sysLanguage::get('TEXT_EDIT_ORDER'))
		->addClass('editButton')
		->disable();
	}
	public function EstimatesGridButtonsBeforeAdd(&$gridButtons){
		$gridButtons[] = htmlBase::newElement('button')
			->setText(sysLanguage::get('TEXT_NEW_ESTIMATE'))
			->addClass('createButton')
			->setHref(itw_app_link('appExt=orderCreator', 'default', 'new'));

		$gridButtons[] = htmlBase::newElement('button')
			->setText(sysLanguage::get('TEXT_EDIT_ESTIMATE'))
			->addClass('editButton')
			->disable();
	}
}
?>