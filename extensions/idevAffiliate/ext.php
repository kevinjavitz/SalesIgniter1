<?php

class Extension_idevAffiliate extends ExtensionBase {

	/**
	 * class constructor
	 * @public
	 * @return void
	 */
	public function __construct() {
		parent::__construct('idevAffiliate');
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * Initialize this class. (loaded by core)
	 *
	 * @public
	 * @return void
	 */
	public function init() {
		global $App, $appExtension;
		if ($this->enabled === false) return;


		EventManager::attachEvents(array(
			'CheckoutSuccessFinish',
			'CheckoutSuccessRemoteFinish'
		), null, $this);

	}
	   /*

	    * */
	public function CheckoutSuccessFinish($Order){
		echo '<img border="0" src="'.sysConfig::get('EXTENSION_IDEVAFFILIATE_URL').'&idev_saleamt='.$Order['OrdersTotal'][0]['value'].'&idev_ordernum='.$Order['orders_id'].'" width="1" height="1">';

	}
	public function CheckoutSuccessRemoteFinish($orderId, $amount, $ip){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, sysConfig::get('EXTENSION_IDEVAFFILIATE_URL').'&idev_saleamt='.$amount.'&idev_ordernum='.$orderId.'&ip_address='.$ip);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_exec($ch);
		curl_close($ch);
	}
}

?>
