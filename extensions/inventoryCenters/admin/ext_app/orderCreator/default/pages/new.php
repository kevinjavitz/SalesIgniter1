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
				'OrderCreatorAddToInfoTableAfter',
				'OrderCreatorSaveCustomerInfoResponse'
			), null, $this);
	}



	public function OrderCreatorAddToInfoTableAfter(&$infoTable, OrderCreator $Editor){
		global $App, $appExtension;
		if ($this->stockMethod == 'Zone'){

			$customersStore = htmlBase::newElement('selectbox')
				->setName('customers_inventory_center');
			$customersStore->addOption('', sysLanguage::get('TEXT_PLEASE_SELECT'));

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

		}
	}

	public function OrderCreatorSaveCustomerInfoResponse(){
		global $Editor;
		$Editor->setData('inventory_center_id', $_POST['customers_inventory_center']);
	}
}