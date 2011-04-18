<?php
/*
	Go Rentals Depot Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_gorentalsDepot extends ExtensionBase {

	public function __construct(){
		parent::__construct('gorentalsDepot');
	}
	
	public function init(){
		global $App, $appExtension, $Template;
		if ($this->enabled === false) return;
		
		$currentFile = basename($_SERVER['PHP_SELF']);
		
		EventManager::attachEvents(array(
			'NewOrderBeforeSave',
			'OnepageCheckoutLoadOrdersVars',
			'OnepageCheckoutProcessCheckout',
			'CheckoutShippingMethodsBeforeList',
			'OrderShowExtraShippingInfo',
			'OrderSingleLoad',
		), null, $this);
	}
	
	public function OrderSingleLoad(&$order, &$orderResult){
		//print_r($orderResult);
		if (!empty($orderResult['delivery_depot'])){
			$order->info['delivery_depot'] = $orderResult['delivery_depot'];
			$order->info['delivery_depot_postcode'] = $orderResult['delivery_depot_postcode'];
		}
	}
	
	public function OrderShowExtraShippingInfo(&$order){
		if (isset($order->info['delivery_depot'])){
			return '<br /><div class="main"><b>Depot City/Town:</b> ' . $order->info['delivery_depot_postcode'] . '<br /><b>Depot:</b> ' . $order->info['delivery_depot'] . '</div>';
		}
		return;
	}
	
	public function NewOrderBeforeSave(&$order, $newOrder){
		if (isset($order->info['delivery_depot'])){
			$newOrder->delivery_depot = $order->info['delivery_depot'];
			$newOrder->delivery_depot_postcode = $order->info['delivery_depot_postcode'];
		}
	}
	
	public function OnepageCheckoutLoadOrdersVars(&$onePageInfo, &$order){
		if (isset($onePageInfo['delivery_depot'])){
			$order->info['delivery_depot'] = $onePageInfo['delivery_depot'];
			$order->info['delivery_depot_postcode'] = $onePageInfo['delivery_depot_postcode'];
		}
	}
	
	public function OnepageCheckoutProcessCheckout(&$onePageCheckout){
		if (isset($_POST['delivery_depot'])){
			$onePageCheckout->onePage['info']['delivery_depot'] = $_POST['delivery_depot'];
			$onePageCheckout->onePage['info']['delivery_depot_postcode'] = $_POST['delivery_depot_postcode'];
		}
	}
	
	public function CheckoutShippingMethodsBeforeList(){
		global $onePageCheckout;
		return '<table cellpadding="0" cellspacing="0" border="0">
		 <tr>
		  <td class="main" colspan="2">City / Town where rental will take place <input type="text" name="delivery_depot_postcode" value="' . (isset($onePageCheckout->onePage['info']['delivery_depot_postcode']) ? $onePageCheckout->onePage['info']['delivery_depot_postcode'] : '') . '"> (We can deliver countrywide)</td>
		 </tr>
		 <tr>
		  <td class="main">Depot closest to my rental:</td>
		  <td class="main"><table cellpadding="0" cellspacing="0" border="0">
		   <tr>
		    <td><input type="radio" name="delivery_depot" value="Johannesburg"' . ((isset($onePageCheckout->onePage['info']['delivery_depot']) && $onePageCheckout->onePage['info']['delivery_depot'] == 'Johannesburg') || !isset($onePageCheckout->onePage['info']['delivery_depot']) ? ' checked="checked"' : '') . '>Johannesburg</td>
		    <td><input type="radio" name="delivery_depot" value="Pretoria"' . (isset($onePageCheckout->onePage['info']['delivery_depot']) && $onePageCheckout->onePage['info']['delivery_depot'] == 'Pretoria' ? ' checked="checked"' : '') . '>Pretoria</td>
		    <td><input type="radio" name="delivery_depot" value="Cape Town"' . (isset($onePageCheckout->onePage['info']['delivery_depot']) && $onePageCheckout->onePage['info']['delivery_depot'] == 'Cape Town' ? ' checked="checked"' : '') . '>Cape Town</td>
		    <td><input type="radio" name="delivery_depot" value="Durban"' . (isset($onePageCheckout->onePage['info']['delivery_depot']) && $onePageCheckout->onePage['info']['delivery_depot'] == 'Durban' ? ' checked="checked"' : '') . '>Durban</td>
		    <td><input type="radio" name="delivery_depot" value="East London"' . (isset($onePageCheckout->onePage['info']['delivery_depot']) && $onePageCheckout->onePage['info']['delivery_depot'] == 'East London' ? ' checked="checked"' : '') . '>East London</td>
		    <td><input type="radio" name="delivery_depot" value="Not Sure"' . (isset($onePageCheckout->onePage['info']['delivery_depot']) && $onePageCheckout->onePage['info']['delivery_depot'] == 'Not Sure' ? ' checked="checked"' : '') . '>I\'m not sure?</td>
		   </tr>
		  </table></td>
		 </tr>
		</table><br />';
	}
}
?>