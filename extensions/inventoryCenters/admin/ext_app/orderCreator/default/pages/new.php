<?php
class inventoryCenters_admin_orderCreator_default_new extends Extension_inventoryCenters
{

	public function __construct() {
		parent::__construct('inventoryCenters');
	}

	public function load() {
		global $appExtension;
		if ($this->isEnabled() === false){
			return;
		}

		EventManager::attachEvents(array(
				'OrderCreatorAddToInfoTable',
				'OrderCreatorLoadExtraFields',
				'OrderCreatorSaveCustomerInfoResponse',
				'SaveResInfoOrderCreator',
				'SaveResInfoOrderCreatorNew',
				'SaveResInfoAddToOrderOrCart'
			), null, $this);
	}

	public function SaveResInfoOrderCreator(&$resInfo){
		global $Editor;
		if(is_object($Editor)){
			if($Editor->hasData('inventory_center_id')){
				$resInfo['inventory_center_pickup'] = $Editor->getData('inventory_center_id');
			}
			if($Editor->hasData('inventory_center_lp')){
				$resInfo['inventory_center_lp'] = $Editor->getData('inventory_center_lp');
			}

		}
	}

	public function SaveResInfoOrderCreatorNew(&$dataArray, $resInfo){
		if(isset($resInfo['inventory_center_pickup'])){
			$dataArray['inventory_center_pickup'] = $resInfo['inventory_center_pickup'];
		}
		if(isset($resInfo['inventory_center_lp'])){
			$dataArray['inventory_center_lp'] = $resInfo['inventory_center_lp'];
		}
	}

	public function SaveResInfoAddToOrderOrCart(&$pInfo, $resInfo){
		if(isset($resInfo['inventory_center_pickup'])){
			$pInfo['reservationInfo']['inventory_center_pickup'] = $resInfo['inventory_center_pickup'];
		}
		if(isset($resInfo['inventory_center_lp'])){
			$pInfo['reservationInfo']['inventory_center_lp'] = $resInfo['inventory_center_lp'];
		}
	}

	public function OrderCreatorAddToInfoTable(&$infoTable, OrderCreator $Editor){
		global $App, $appExtension;
		if ($this->stockMethod == 'Zone'){

			$customersStore = htmlBase::newElement('selectbox')
				->setName('customers_inventory_center');
			//$customersStore->addOption('', sysLanguage::get('TEXT_PLEASE_SELECT'));

			$QInventoryCenters = Doctrine_Query::create()
			->from('ProductsInventoryCenters')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			foreach($QInventoryCenters as $sInfo){
				$customersStore->addOption($sInfo['inventory_center_id'], $sInfo['inventory_center_name']);
			}


			$infoTable->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_CUSTOMERS_INVENTORY_CENTER') . '</b>'),
						array('addCls' => 'main', 'text' => $customersStore->draw())
					)
			));

			$customersLP = htmlBase::newElement('selectbox')
				->setName('customers_inventory_lp');
			//$customersLP->addOption('', sysLanguage::get('TEXT_PLEASE_SELECT'));

			$QLP = Doctrine_Query::create()
			->from('InventoryCentersLaunchPoints')
			->where('inventory_center_id > ?', '0')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			foreach($QLP as $sInfo){
				$customersLP->addOption($sInfo['lp_name'], $sInfo['lp_name']);
			}

			$infoTable->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_CUSTOMERS_INVENTORY_LP') . '</b>'),
						array('addCls' => 'main', 'text' => $customersLP->draw())
					)
				));


		}
	}

	public function OrderCreatorLoadExtraFields(){
		global $Editor;
		if(isset($_POST['customers_inventory_center'])){
			$Editor->setData('inventory_center_id', $_POST['customers_inventory_center']);
		}
		if(isset($_POST['customers_inventory_lp'])){
			$Editor->setData('inventory_center_lp', $_POST['customers_inventory_lp']);
		}

	}

	public function OrderCreatorSaveCustomerInfoResponse(){
		global $Editor;
		$Editor->setData('inventory_center_id', $_POST['customers_inventory_center']);
		$Editor->setData('inventory_center_lp', $_POST['customers_inventory_lp']);
	}
}