<?php
class multiStore_admin_orderCreator_default_new extends Extension_multiStore
{

	public function __construct() {
		parent::__construct('multiStore');
	}

	public function load() {
		global $appExtension;
		if ($this->enabled === false){
			return;
		}

		EventManager::attachEvents(array(
				'OrderCreatorAddToInfoTable',
				'OrderCreatorLoadCustomerInfoResponse',
				'OrderCreatorSaveCustomerInfoResponse',
				'OrderCreatorFindCustomerQueryBeforeExecute'
			), null, $this);
	}

	public function OrderCreatorFindCustomerQueryBeforeExecute(&$Query){
		$Query
			->leftJoin('c.CustomersToStores c2s')
			->andWhereIn('c2s.stores_id', Session::get('admin_showing_stores'));
	}

	public function OrderCreatorAddToInfoTable(&$infoTable, OrderCreator $Editor){
		$customersStore = htmlBase::newElement('selectbox')
			->setName('customers_store');
		$customersStore->addOption('', sysLanguage::get('TEXT_PLEASE_SELECT'));
		$allowedStores = Session::get('admin_allowed_stores');
		//if (sizeof($allowedStores) == 1){
			$customersStore->selectOptionByValue($allowedStores[0]);
		//}
		foreach($this->getStoresArray() as $sInfo){
			if (in_array($sInfo['stores_id'], Session::get('admin_allowed_stores')) === false) continue;
			
			$customersStore->addOption($sInfo['stores_id'], $sInfo['stores_name']);
		}

		if (isset($_GET['oID'])){
			$QcustomersStore = Doctrine_Query::create()
				->from('CustomersToStores')
				->where('customers_id = ?', $Editor->getCustomerId())
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$customersStore
				->attr('disabled', 'disabled')
				->selectOptionByValue($QcustomersStore[0]['stores_id']);
		}

		$infoTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_CUSTOMERS_STORE') . '</b>'),
					array('addCls' => 'main', 'text' => $customersStore->draw())
				)
			));
	}

	public function OrderCreatorLoadCustomerInfoResponse(&$response, $Customer){
		global $Editor;
		$ToStores = $Customer->CustomersToStores;

		$Editor->setData('store_id', $ToStores->stores_id);
		$response['field_values']['customers_store'] = $ToStores->stores_id;
	}
	public function OrderCreatorSaveCustomerInfoResponse(){
		global $Editor;
		$Editor->setData('store_id', $_POST['customers_store']);
	}
}