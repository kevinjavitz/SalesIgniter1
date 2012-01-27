<?php
/*
	Product Purchase Type: Rental

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

/**
 * Rental Membership Purchase Type
 * @package ProductPurchaseTypes
 */

class PurchaseType_rental extends PurchaseTypeAbstract {
	public $typeLong = 'rental';
	public $typeName;
	public $typeShow;

	public function __construct($ProductCls, $forceEnable = false){

		$this->typeName = sysLanguage::get('PURCHASE_TYPE_RENTAL_NAME');
		$this->typeShow = sysLanguage::get('PURCHASE_TYPE_RENTAL_SHOW');

		$productInfo = $ProductCls->productInfo;
		$this->enabled = ($forceEnable === true ? true : (in_array($this->typeLong, $productInfo['typeArr'])));

		if ($this->enabled === true){
			$this->productInfo = array(
				'id'      => $productInfo['products_id'],
				'isBox'   => $ProductCls->isBox(),
				'price'   => null,
				'keepPrice' => $productInfo['products_keepit_price']
			);

			$this->inventoryCls = new ProductInventory(
				$this->productInfo['id'],
				$this->typeLong,
				$productInfo['products_inventory_controller']
			);
		}
	}

	public function hasInventory(){
		if ($this->enabled === false) return false;
		return true;
	}

	public function processRemoveFromCart(){
		return null;
	}

	public function processAddToOrder(&$pInfo){
		$this->processAddToCart($pInfo);
	}

	public function processAddToCart(&$pInfo){
		$pInfo['price'] = $this->productInfo['keepPrice'];
		$pInfo['final_price'] = $this->productInfo['keepPrice'];
		$pInfo['queue_id'] = $_POST['queue_id'];
	}

	/*public function addToCartPrepare(&$pInfo){
		$pInfo['price'] = $this->productInfo['keepPrice'];
		$pInfo['final_price'] = $this->productInfo['keepPrice'];
		$pInfo['queue_id'] = $_POST['queue_id'];
	} */


	public function onInsertOrderedProduct($cartProduct, $orderId, &$orderedProduct, &$products_ordered) {
		$pID = (int)$cartProduct->getIdString();
		$pInfo = $cartProduct->getInfo();
		$queueID = $pInfo['queue_id'];

		$Qrented = Doctrine_Query::create()
			->from('RentedQueue')
			->where('customers_queue_id = ?', $queueID)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if(isset($Qrented[0])){
			$Barcode = Doctrine_Core::getTable('ProductsInventoryBarcodes')->find($Qrented[0]['products_barcode']);
			if ($Barcode){
				$Barcode->status = 'P';
				$Barcode->save();
			}
			$orderedProduct->barcode_id = $Qrented[0]['products_barcode'];
		}

		Doctrine_Query::create()
			->delete('RentedQueue')
			->where('customers_queue_id = ?', $queueID)
			->execute();
		$orderedProduct->purchase_type = 'rental';
		$orderedProduct->save();

	}

	public function canUseSpecial(){
		return false;
	}

	public function getPurchaseHtml($key){
		global $rentalQueue;

		$return = null;
		switch($key){
			case 'product_info':
				$button = htmlBase::newElement('button')->setType('submit');
				if ($this->productInfo['isBox'] === false){
					$button->setText(sysLanguage::get('TEXT_BUTTON_IN_QUEUE'))->setName('add_queue');
					if ($rentalQueue->in_queue($this->productInfo['id']) === true){
						$button->disable();
					}
				}elseif ($this->productInfo['isBox'] === true){
					$button->setText(sysLanguage::get('TEXT_BUTTON_IN_QUEUE_SERIES'))->setName('add_queue_all');
				}
				$content = '';
			    if ($this->showRentalAvailability()){
					$content = '<table cellpadding="1" cellspacing="0" border="0"><tr>
						<td class="main">' . sysLanguage::get('TEXT_AVAILABLITY') . '</td>
						<td class="main">' . $this->getAvailabilityName() . '</td>
					   </tr></table>';
			    }


				$return = array(
					'form_action'   => itw_app_link(tep_get_all_get_params(array('action'))),
					'purchase_type' => $this->typeLong,
					'allowQty'      => false,
					'header'        => $this->typeName,
					'content'       => $content,
					'button'        => $button
				);
				break;
		}
		return $return;
	}

	function showRentalAvailability(){
		if (sysConfig::get('ALLOW_RENTALS') == 'false' || sysConfig::get('RENTAL_AVAILABILITY_PRODUCT_INFO') == 'false'){
			return false;
		}
		return (sysConfig::get('RENTAL_AVAILABILITY_PRODUCT_INFO') == 'true');
	}

	function getAvailabilityName(){

		$QproductsInQueue = Doctrine_Query::create()
							->from('RentalQueueTable')
							->where('products_id = ?', $this->productInfo['id'])
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$QAvailability = Doctrine_Query::create()
						->from('RentalAvailability r')
						->leftJoin('r.RentalAvailabilityDescription rd')
						->where('rd.language_id = ?', Session::get('languages_id'))
						->orderBy('r.ratio')
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$availability = count($QproductsInQueue) - $this->getCurrentStock();
		$availabilityName = null;

		if ($QAvailability){
			foreach($QAvailability as $aInfo){
				if ($availability <= $aInfo['ratio']){
					$availabilityName = $aInfo['RentalAvailabilityDescription'][0]['name'];
					break;
				}
			}
		}

		return $availabilityName;
	}
}
?>