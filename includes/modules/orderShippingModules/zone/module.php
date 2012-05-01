<?php
class OrderShippingZone extends OrderShippingModuleBase
{

	private $methods = array();

	private $quotes;

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Zones');
		$this->setDescription('Zone Based Shipping');

		$this->init('zone');

		if ($this->isEnabled() === true){
			$Qmethods = Doctrine_Query::create()
				->from('ModulesShippingZoneMethods')
				->orderBy('sort_order')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			try{
				if ($Qmethods){
					foreach($Qmethods as $mInfo){
						$this->methods[$mInfo['method_id']] = array(
							'countries' => explode(',', $mInfo['method_countries']),
							'cost' => explode(',', $mInfo['method_cost']),
							'handling' => $mInfo['method_handling_cost'],
							'sort_order' => $mInfo['sort_order']
						);
					}
				}
			}catch(Doctrine_Connection_Exception $e){

			}
		}
		$isEnabled = true;
		EventManager::notify('ShippingMethodCheckBeforeConstruct', &$isEnabled);
		$this->setEnabled($isEnabled);
	}

	public function getMethods() {
		return $this->methods;
	}

	public function quote($module = '') {
		global $order, $shipping_weight, $shipping_num_boxes;

		$deliveryAddress = $this->getDeliveryAddress();

		$userAccount = &$this->getUserAccount();
		$addressBook =& $userAccount->plugins['addressBook'];

		$deliveryCountry = $addressBook->getCountryInfo($deliveryAddress['entry_country_id']);
		$dest_country = $deliveryCountry['countries_iso_code_2'];
		$error = false;

		$destZone = null;
		foreach($this->methods as $mInfo){
			if (in_array($dest_country, $mInfo['countries'])){
				$destZone = $mInfo;
				break;
			}
		}
		$shipping_method = '';
		$shipping_cost = 0;
		if (is_null($destZone) === true){
			$error = true;
		}
		else {
			$shipping = -1;

			foreach($destZone['cost'] as $costStr){
				$costArr = explode(':', $costStr);
				if ($shipping_weight <= $costArr[0]){
					$shipping = $costArr[1];
					$shipping_method = sprintf('%s %s : %s %s',
						sysLanguage::get('MODULE_ORDER_SHIPPING_ZONE_TEXT_WAY'),
						$dest_country,
						$shipping_weight,
						sysLanguage::get('MODULE_ORDER_SHIPPING_ZONE_TEXT_UNITS')
					);
					break;
				}
			}

			if ($shipping == -1){
				$shipping_cost = 0;
				$shipping_method = sysLanguage::get('MODULE_ORDER_SHIPPING_ZONE_UNDEFINED_RATE');
			}
			else {
				$shipping_cost = ($shipping * $shipping_num_boxes) + $destZone['handling'];
			}
		}

		$this->quotes = array(
			'id' => $this->getCode(),
			'module' => $this->getTitle(),
			'methods' => array(
				array(
					'id' => $this->getCode(),
					'title' => $shipping_method,
					'cost' => $shipping_cost
				)
			)
		);

		$classId = $this->getTaxClass();
		if ($classId > 0){
			$this->quotes['tax'] = tep_get_tax_rate($classId, $deliveryAddress['country_id'], $deliveryAddress['zone_id']);
		}

		if ($error == true) {
			$this->quotes['error'] = sysLanguage::get('MODULE_ORDER_SHIPPING_ZONE_INVALID_ZONE');
		}
		return $this->quotes;
	}

	public function onInstall(&$module, &$moduleConfig) {
		$Zone = new ModulesShippingZoneMethods();
		$Zone->method_countries = 'US,CA';
		$Zone->method_cost = '3:8.50,7:10.50,99:20.00';
		$Zone->method_handling_cost = '0';
		$Zone->sort_order = '1';
		$Zone->save();
	}
}

?>