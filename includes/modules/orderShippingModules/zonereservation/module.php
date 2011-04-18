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
		if (isset($_GET['app']) && $_GET['app'] == 'checkout'){
			$this->setEnabled(false);
		}
		/*if(isset($ShoppingCart)){
			foreach($ShoppingCart->getProducts() as $cartProduct){
				if ($cartProduct->getPurchaseType() == 'reservation'){
					$this->setEnabled(false);
				}
			}
		}*/

		if ($this->isEnabled() === true){
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
	
	public function quote($method = ''){
		global $order;
		if ($this->isEnabled() === true){
			$this->quotes = array(
				'id'      => $this->getCode(),
				'module'  => $this->getTitle(),
				'methods' => array()
			);

			foreach($this->methods as $methodId => $mInfo){
				if ($mInfo['status'] == 'True' && ($method == 'method' . $methodId || $method == '')){
					$this->quotes['methods'][] = array(
						'id'      => 'method' . $methodId,
						'title'   => $mInfo['text'],
						'cost'    => $mInfo['cost'],
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
		}else{
			return false;
		}
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
}
?>