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

    public function getName($type){
        $Address = null;
        if (isset($this->addresses[$type])){
            $Address = $this->addresses[$type]->getName();
        }
        return $Address;
    }

    public function getCompany($type){
        $Address = null;
        if (isset($this->addresses[$type])){
            $Address = $this->addresses[$type]->getCompany();
        }
        return $Address;
    }

    public function getStreetAddress($type){
        $Address = null;
        if (isset($this->addresses[$type])){
            $Address = $this->addresses[$type]->getStreetAddress();
        }
        return $Address;
    }

    public function getCity($type){
        $Address = null;
        if (isset($this->addresses[$type])){
            $Address = $this->addresses[$type]->getCity();
        }
        return $Address;
    }

    public function getState($type){
        $Address = null;
        if (isset($this->addresses[$type])){
            $Address = $this->addresses[$type]->getState();
        }
        return $Address;
    }

    public function getCountry($type){
        $Address = null;
        if (isset($this->addresses[$type])){
            $Address = $this->addresses[$type]->getCountry();
        }
        return $Address;
    }

    public function getPostcode($type){
        $Address = null;
        if (isset($this->addresses[$type])){
            $Address = $this->addresses[$type]->getPostcode();
        }
        return $Address;
    }

    public function getZoneCode($type){
        $Address = null;
        if (isset($this->addresses[$type])){
            $Address = $this->addresses[$type]->getZoneCode();
        }
        return $Address;
    }

	public function showAddress($Address, $html = true){
		if(sysConfig::get('ACCOUNT_COMPANY') == 'true'){
			$company = htmlspecialchars($Address->getCompany());
		}
		$firstname = htmlspecialchars($Address->getName());
		$lastname = '';
		$street_address = htmlspecialchars($Address->getStreetAddress());
		$suburb = htmlspecialchars($Address->getSuburb());
		$city = htmlspecialchars($Address->getCity());
		$state = htmlspecialchars($Address->getState());
		$country = htmlspecialchars($Address->getCountry());
		$postcode = htmlspecialchars($Address->getPostcode());
		$abbrstate = htmlspecialchars($Address->getZoneCode());
		$vat = htmlspecialchars($Address->getVAT());
		$cif = htmlspecialchars($Address->getCIF());
		$fmt = $Address->getFormat();
		if($html){
			$fmt = nl2br($fmt);
		}
		eval("\$address = \"$fmt\";");

		return $address;
	}
}
?>