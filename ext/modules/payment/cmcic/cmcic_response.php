<?php
/*
$Id: cmcic_response_rc1.php,v 1.02  15/04/2009 Sébastien STRAZIERI (informatiquedefrance@gmail.com)
Adaptation du module CM-CIC révision 3.0 PHP4 - avril 2009

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2009 Informatique de France
http://www.informatiquedefrance.com

Released under the GNU General Public License
*/

	//chdir('../../../../');
	//require('includes/application_top.php');

	//include(DIR_WS_MODULES . 'payment/cmcic.php');

	/*tep_db_perform('cmcic_response', array(
	'ref_number' => $CMCIC_bruteVars['reference'],
	'MAC' => $CMCIC_bruteVars['MAC'],
	'TPE' => $CMCIC_bruteVars['TPE'],
	'date' => $CMCIC_bruteVars['date'],
	'montant' => $CMCIC_bruteVars['montant'],
	'texte_libre' => $CMCIC_bruteVars['texte-libre'],
	'code_retour' => $CMCIC_bruteVars['code-retour'],
	'retourPLUS' => $CMCIC_bruteVars['retourPLUS'])
	);*/

	//$CMCIC_bruteVars = getMethode();
	require_once(sysConfig::getDirFsCatalog(). 'ext/modules/payment/cmcic/CMCIC_Tpe.php');
	require_once(sysConfig::getDirFsCatalog(). 'ext/modules/payment/cmcic/CMCIC_Hmac.php');
	// TPE init variables
	$oTpe = new CMCIC_Tpe();
	$oHmac = new CMCIC_Hmac($oTpe);

	// Message Authentication
	$cgi2_fields = sprintf(CMCIC_CGI2_FIELDS, $oTpe->sNumero,
	$CMCIC_bruteVars["date"],
	$CMCIC_bruteVars['montant'],
	$CMCIC_bruteVars['reference'],
	$CMCIC_bruteVars['texte-libre'],
	$oTpe->sVersion,
	$CMCIC_bruteVars['code-retour'],
	(isset($CMCIC_bruteVars['cvx'])?$CMCIC_bruteVars['cvx']:''),
	(isset($CMCIC_bruteVars['vld'])?$CMCIC_bruteVars['vld']:''),
	(isset($CMCIC_bruteVars['brand'])?$CMCIC_bruteVars['brand']:''),
	(isset($CMCIC_bruteVars['status3ds'])?$CMCIC_bruteVars['status3ds']:''),
	(isset($CMCIC_bruteVars['numauto'])?$CMCIC_bruteVars['numauto']:''),
	(isset($CMCIC_bruteVars['motifrefus'])?$CMCIC_bruteVars['motifrefus']:''),
	(isset($CMCIC_bruteVars['originecb'])?$CMCIC_bruteVars['originecb']:''),
	(isset($CMCIC_bruteVars['bincb'])?$CMCIC_bruteVars['bincb']:''),
	(isset($CMCIC_bruteVars['hpancb'])?$CMCIC_bruteVars['hpancb']:''),
	(isset($CMCIC_bruteVars['ipclient'])?$CMCIC_bruteVars['ipclient']:''),
	(isset($CMCIC_bruteVars['originetr'])?$CMCIC_bruteVars['originetr']:''),
	(isset($CMCIC_bruteVars['veres'])?$CMCIC_bruteVars['veres']:''),
	(isset($CMCIC_bruteVars['pares'])?$CMCIC_bruteVars['pares']:'')
	);

	if ($oHmac->computeHmac($cgi2_fields) == strtolower($CMCIC_bruteVars['MAC']))
	{
		switch($CMCIC_bruteVars['code-retour']) {
			case "Annulation" :
				tep_mail(sysConfig::get('STORE_NAME') . ': CYBERMUT', sysConfig::get('STORE_OWNER_EMAIL_ADDRESS') , "Cancelled order: " . $CMCIC_bruteVars['reference'],
				"This order was cancelled", sysConfig::get('STORE_NAME'), sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'));
				break;

			case "payetest":
			case "paiement":
			    $userAccount = new rentalStoreUser($theCustomerID);
          		$userAccount->loadPlugins();
			    require('includes/classes/order.php');
				$order = new OrderProcessor($theOrderID);
				$order->sendNewOrderEmail();

				$QOrder = Doctrine_Query::create()
						->select('currency_value')
						->from('Orders')
						->where('orders_id = ?', $theOrderID)
						->fetchOne();
				//$ShoppingCart->emptyCart(true);

				$newStatus = new OrdersPaymentsHistory();
				$newStatus->orders_id = $theOrderID;
				$newStatus->payment_module = 'cmcic';
				$newStatus->payment_method = 'Credit Card';
				$newStatus->gateway_message = 'Successfull payment';
				$newStatus->payment_amount = floatval($CMCIC_bruteVars['montant'])/$QOrder->currency_value;
				$newStatus->card_details = 'NULL';
				$newStatus->save();

				/*
				 * tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . $order_status_id . "', last_modified = now() where orders_id = '" . $orderID . "'");

				$sql_data_array = array(
					'orders_id' => $orderID,
					'orders_status_id' => $order_status_id,
					'date_added' => 'now()',
					'customer_notified' => $customer_notified,
					'comments' => 'PayPal IPN Verified [' . $comment_status . ']'
				);
				tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
				 */
				break;
		}
		$receipt = CMCIC_CGI2_MACOK;
	}
	else
	{
		// your code if the HMAC doesn't match
		$receipt = CMCIC_CGI2_MACNOTOK.$cgi2_fields;
	}
	//-----------------------------------------------------------------------------
	// Send receipt to CMCIC server
	//-----------------------------------------------------------------------------
	printf (CMCIC_CGI2_RECEIPT, $receipt);

	//require('includes/application_bottom.php');
?>