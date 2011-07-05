<?php
class OrderShippingZonereservation extends OrderShippingModule {
	public $methods = array();
	
	public function __construct(){
		global $ShoppingCart;
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Reservation Shipping');
		$this->setDescription('Google Maps Zone Based Shipping');
		
		$this->init('zonereservation');
		$this->type = $this->getConfigData('MODULE_ORDER_SHIPPING_ZONERESERVATION_TYPE');

		if (isset($_GET['app']) && $_GET['app'] == 'checkout' && (!Session::exists('onlyReservations') || Session::get('onlyReservations') == false)){
			$this->setEnabled(false);
		}
		if(class_exists('ModulesShippingZoneReservationMethods')){
			$Qmethods = Doctrine_Query::create()
			->from('ModulesShippingZoneReservationMethods m')
			->leftJoin('m.ModulesShippingZoneReservationMethodsDescription md')
			->orderBy('sort_order')
			->execute()
			->toArray(true);
			if ($Qmethods){
				foreach($Qmethods as $mInfo){
					$this->methods[$mInfo['method_id']] = array(
						'status'     => $mInfo['method_status'],
						'text'       => $mInfo['ModulesShippingZoneReservationMethodsDescription'][Session::get('languages_id')]['method_text'],
						'details'    => $mInfo['ModulesShippingZoneReservationMethodsDescription'][Session::get('languages_id')]['method_details'],
						'cost'       => $mInfo['method_cost'],
						'days_before'       => $mInfo['method_days_before'],
						'days_after'       => $mInfo['method_days_after'],
						'sort_order' => $mInfo['sort_order'],
						'weight_rates' => $mInfo['weight_rates'],
						'default'    => $mInfo['method_default'],
						'zone'       => $mInfo['method_zone']
					);
					foreach(sysLanguage::getLanguages() as $lInfo){
						if (isset($mInfo['ModulesShippingZoneReservationMethodsDescription'][$lInfo['id']]['method_text'])) {
							$this->methods[$mInfo['method_id']][$lInfo['id']]['text'] = $mInfo['ModulesShippingZoneReservationMethodsDescription'][$lInfo['id']]['method_text'];
						}
						if (isset($mInfo['ModulesShippingZoneReservationMethodsDescription'][$lInfo['id']]['method_details'])) {
							$this->methods[$mInfo['method_id']][$lInfo['id']]['details'] = $mInfo['ModulesShippingZoneReservationMethodsDescription'][$lInfo['id']]['method_details'];
						}
					}
				}
			}
		}

	}

	public function getNumBoxes(&$shipping_weight, &$shipping_num_boxes){
		$boxWeight = sysConfig::get('SHIPPING_BOX_WEIGHT');
		$boxPadding = sysConfig::get('SHIPPING_BOX_PADDING');
		$boxMaxWeight = sysConfig::get('SHIPPING_MAX_WEIGHT');


		if ($boxWeight >= $shipping_weight * $boxPadding / 100) {
			$shipping_weight = $shipping_weight + $boxWeight;
		} else {
			$shipping_weight = $shipping_weight + ($shipping_weight * $boxPadding / 100);
		}

		if ($shipping_weight > $boxMaxWeight) { // Split into many boxes
			$shipping_num_boxes = ceil($shipping_weight / $boxMaxWeight);
			$shipping_weight = $shipping_weight / $shipping_num_boxes;
		}
	}

	public function getType(){
		return $this->type;
	}
	
	public function quote($method = '', $shipping_weight_prod = -1){
		global $order;
			$this->quotes = array(
				'id'      => $this->getCode(),
				'module'  => $this->getTitle(),
				'methods' => array()
			);

			$shipping_num_boxes_prod = 1;
			$this->getNumBoxes($shipping_weight_prod, $shipping_num_boxes_prod);//adding boxes weight

			foreach($this->methods as $methodId => $mInfo){
				if ($mInfo['status'] == 'True' && ($method == 'method' . $methodId || $method == '')){

					$shippingCost =  $mInfo['cost'];
					$tableRates = explode(',', $mInfo['weight_rates']);
					foreach($tableRates as $rate){
						$rInfo = explode(':', $rate);
						if ($shipping_weight_prod <= $rInfo[0]) {
							$shippingCost = $rInfo[1];
							break;
						}
					}
					$showCost = $shippingCost;
					if($this->type == 'Order' && (isset($_GET['app']) && $_GET['app'] != 'checkout')){
						$shippingCost = 0;
					}

					$this->quotes['methods'][] = array(
						'id'      => 'method' . $methodId,
						'title'   => $mInfo['text'],
						'cost'    => $shippingCost,
						'showCost' => $showCost,
						'default' => $mInfo['default'],
						'details' => $mInfo['details'],
						'days_before'    => $mInfo['days_before'],
						'days_after'    => $mInfo['days_after'],
						'zone'    => $mInfo['zone']
					);
				}
			}

			$classId = $this->getTaxClass();
			if ($classId > 0) {
				$deliveryAddress = $this->getDeliveryAddress();
				$this->quotes['tax'] = tep_get_tax_rate($classId, $deliveryAddress['country_id'], $deliveryAddress['zone_id']);
			}

			return $this->quotes;
	}
	
	public function getZonesMenu($name, $value = 0){
		$selectBox = htmlBase::newElement('selectbox')
		->setName($name);
		
		$selectBox->addOption('0', 'Everywhere');
		$QGoogleZones = Doctrine_Query::create()
		->from('GoogleZones')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($QGoogleZones){
			foreach($QGoogleZones as $zInfo){
				$selectBox->addOption($zInfo['google_zones_id'], $zInfo['google_zones_name']);
			}
		}
		
		$selectBox->selectOptionByValue($value);
		return $selectBox->draw();
	}

	public function getTaxClass(){
		return 0;
	}
}
?>