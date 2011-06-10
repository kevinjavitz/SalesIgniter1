<?php
/**
 * Address manager for the order class
 *
 * @package Order
 * @author Stephen Walker <stephen@itwebexperts.com>
 * @copyright Copyright (c) 2010, I.T. Web Experts
 */

require(dirname(__FILE__) . '/Address.php');

class OrderAddressManager {
	protected $addresses = array();
	protected $addressHeadings = array();
	protected $orderId = null;

	public function __construct($addressArray = null){
		$this->addressHeadings = array(
			'customer' => 'Customer Address',
			'billing' => 'Billing Address',
			'delivery' => 'Shipping Address'
		);

		if (sysConfig::exists('EXTENSION_PAY_PER_RENTALS_CHOOSE_PICKUP') && sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_PICKUP') == 'True'){
			$this->addressHeadings['pickup'] = 'Pickup Address';
		}

		if (is_null($addressArray) === false){
			foreach($addressArray as $type => $aInfo){
				$this->addresses[$type] = new OrderAddress($aInfo);
			}
		}else{
			foreach($this->addressHeadings as $type => $heading){
				$this->addresses[$type] = new OrderAddress(array(
					'address_type' => $type
				));
			}
		}
	}

	public function setOrderId($val){
		$this->orderId = $val;
	}
	
	public function getAddress($rType){
		$return = null;
		foreach($this->addresses as $type => $addressObj){
			if ($type == $rType){
				$return = $addressObj;
				break;
			}
		}
		return $return;
	}

	public function listAll(){
		$addressesTable = htmlBase::newElement('table')
				->setCellPadding(2)
				->setCellSpacing(0)
				->css('width', '100%');

		$addressesRow = array();
		foreach($this->addresses as $type => $addressObj){
			if (isset($this->addressHeadings[$addressObj->getAddressType()])){
				$addressTable = htmlBase::newElement('table')
						->setCellPadding(2)
						->setCellSpacing(0)
						->css('width', '100%');

				$addressTable->addBodyRow(array(
					'columns' => array(
						array(
							'addCls' => 'main',
							'valign' => 'top',
							'text' => '<b>' . $this->addressHeadings[$addressObj->getAddressType()] . '</b>'
						)
					)
				));

				$addressTable->addBodyRow(array(
					'columns' => array(
						array(
							'addCls' => 'main ' . $addressObj->getAddressType() . 'Address',
							'valign' => 'top',
							'text' => $this->showAddress($addressObj)
						)
					)
				));

				$addressesRow[] = array(
					'valign' => 'top',
					'text' => $addressTable
				);
			}
		}
		$addressesTable->addBodyRow(array(
			'columns' => $addressesRow
		));

		return $addressesTable->draw();
	}
	
	public function getFormattedAddress($type){
		$Address = null;
		if (isset($this->addresses[$type])){
			$Address = $this->showAddress($this->addresses[$type], true);
		}
		return $Address;
	}

	public function showAddress($Address, $html = true){
		$company = htmlspecialchars($Address->getCompany());
		$firstname = htmlspecialchars($Address->getName());
		$lastname = '';
		$street = htmlspecialchars($Address->getStreetAddress());
		$suburb = htmlspecialchars($Address->getSuburb());
		$city = htmlspecialchars($Address->getCity());
		$state = htmlspecialchars($Address->getState());
		$country = htmlspecialchars($Address->getCountry());
		$postcode = htmlspecialchars($Address->getPostcode());


		$fmt = $Address->getFormat();
		eval("\$address = \"$fmt\";");

		return $address;
	}
}
?>