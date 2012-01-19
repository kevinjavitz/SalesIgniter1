<?php
/*
$Id: cmcic.php,v 1.02  15/04/2009 S�bastien STRAZIERI (informatiquedefrance@gmail.com)
Adaptation du module CM-CIC r�vision 3.0 PHP4 - avril 2009

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2009 Informatique de France
http://www.informatiquedefrance.com

Released under the GNU General Public License
*/

require_once(sysConfig::getDirFsCatalog() . 'ext/modules/payment/cmcic/CMCIC_Tpe.php');
require_once(sysConfig::getDirFsCatalog() . 'ext/modules/payment/cmcic/CMCIC_Hmac.php');

function HtmlEncode($data) {
	$SAFE_OUT_CHARS = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890._-";
	$encoded_data = "";
	$result = "";
	for($i = 0; $i < strlen($data); $i++)
	{
		if (strchr($SAFE_OUT_CHARS, $data{$i})){
			$result .= $data{$i};
		}
		else {
			if (($var = bin2hex(substr($data, $i, 1))) <= "7F"){
				$result .= "&#x" . $var . ";";
			}
			else
			{
				$result .= $data{$i};
			}
		}
	}
	return $result;
}

class OrderPaymentCmcic extends StandardPaymentModule
{

	public function __construct() {
		/*
					 * Default title and description for modules that are not yet installed
					 */
		$this->setTitle('Credit Card Via CMCIC');
		$this->setDescription('Credit Card Via CMCIC');

		$this->init('cmcic');

		if ($this->isEnabled() === true){
			$formUrl = 'https://';
			$this->bankImage = '';
			switch($this->getConfigData('MODULE_PAYMENT_CMCIC_BANQUE')){
				case 'CiC':
					$formUrl .= 'ssl.paiement.cic-banques.fr/';
					$this->bankImage = 'divers/cic.gif';
					break;
				case 'CreditMut':
					$formUrl .= 'paiement.creditmutuel.fr/';
					$this->bankImage = 'divers/cybermut.gif';
					break;
				case 'OBC':
					$formUrl .= 'ssl.paiement.banque-obc.fr/';
					$this->bankImage = 'divers/obc.gif';
					break;
			}

			if ($this->getConfigData('MODULE_PAYMENT_CMCIC_MODE') == 'test'){
				$formUrl .= 'test/';
			}

			$formUrl .= 'paiement.cgi';

			$this->setFormUrl($formUrl);
		}
	}

	public function hasHiddenFields() {
		return true;
	}

	function validatePost() {
		global $order, $orderTotalModules, $onePageCheckout, $currencies, $ShoppingCart;
		$userAccount = &Session::getReference('userAccount');
		if (!$onePageCheckout->isMembershipCheckout()){
			$order->createOrder();
			$order->insertOrderTotals();
			$order->insertStatusHistory();
			// initialized for the email confirmation
			$products_ordered = '';

			foreach($ShoppingCart->getProducts() as $cartProduct){
				$order->insertOrderedProduct($cartProduct, &$products_ordered);

				EventManager::notify('CheckoutProcessInsertOrderedProduct', $cartProduct, &$products_ordered);
				// #################### Added CCGV ######################
				//$orderTotalModules->update_credit_account($cartProduct);//ICW ADDED FOR CREDIT CLASS SYSTEM
				// #################### End Added CCGV ######################
			}

			// lets start with the email confirmation
			// #################### Added CCGV ######################
			//$orderTotalModules->apply_credit();//ICW ADDED FOR CREDIT CLASS SYSTEM
			// #################### End Added CCGV ######################

			EventManager::notify('CheckoutProcessPostProcess', &$order);
			//$order->sendNewOrderEmail();
		}
		else {
			$order->info['is_rental'] = '1';
			$order->info['bill_attempts'] = '1';
			$planID = $onePageCheckout->onePage['rentalPlan']['id'];

			$order->createOrder();

			if (isset($onePageCheckout->onePage['info']['account_action']) === true){
				if (isset($onePageCheckout->onePage['info']['payment'])){
					$paymentInfo = $onePageCheckout->onePage['info']['payment'];
					$rentalPlan = $onePageCheckout->onePage['rentalPlan'];

					$membershipMonths = $rentalPlan['months'];
					$membershipDays = $rentalPlan['days'];
					$numberOfRentals = $rentalPlan['no_of_titles'];
					$paymentTerm = $rentalPlan['pay_term'];
					$billPrice = tep_add_tax($rentalPlan['price'], $rentalPlan['tax_rate']);

					$nextBillDate = strtotime('+' . $membershipMonths . ' month +' . $membershipDays . ' day');
					if (isset($paymentTerm)){
						if ($paymentTerm == 'M'){
							$nextBillDate = strtotime('+1 month');
						}
						elseif ($paymentTerm == 'Y') {
							$nextBillDate = strtotime('+12 month');
						}
					}

					if ($rentalPlan['free_trial'] > 0){
						$freeTrialPeriod = $rentalPlan['free_trial'];
						$freeTrialEnds = time();
						if ($rentalPlan['free_trial'] > 0){
							$nextBillDate = strtotime('+' . $freeTrialPeriod . ' day');
							$freeTrialEnds = strtotime('+' . $freeTrialPeriod . ' day');
						}

						if ($freeTrialEnds > time() && $rentalPlan['free_trial_amount'] > 0){
							$billPrice = tep_add_tax($rentalPlan['free_trial_amount'], $rentalPlan['tax_rate']);
						}
					}

					$membership =& $userAccount->plugins['membership'];
					$membership->setPlanId($planID);
					$membership->setMembershipStatus('M');
					$membership->setActivationStatus('N');
					if (isset($freeTrialEnds)){
						$membership->setFreeTrailEnd($freeTrialEnds);
					}
					$membership->setNextBillDate($nextBillDate);
					$membership->setPaymentTerm($paymentTerm);
					$membership->setPaymentMethod($onePageCheckout->onePage['info']['payment']['id']);
					$membership->setRentalAddress($userAccount->plugins['addressBook']->getDefaultAddressId());
					if (!empty($paymentInfo['cardDetails']['cardNumber'])){
						$membership->setCreditCardNumber($paymentInfo['cardDetails']['cardNumber']);
						$membership->setCreditCardExpirationDate($paymentInfo['cardDetails']['cardExpMonth'] . $paymentInfo['cardDetails']['cardExpYear']);
						if (!empty($paymentInfo['cardDetails']['cardCvvNumber'])){
							$membership->setCreditCardCvvNumber($paymentInfo['cardDetails']['cardCvvNumber']);
						}
					}
					$membership->createNewMembership();
				}
			}

			$order->insertOrderTotals();
			$order->insertStatusHistory();

			$products_ordered = '';

			for($i = 0, $n = sizeof($order->products); $i < $n; $i++){
				$order->insertMembershipProduct($order->products[$i], &$products_ordered);
			}

			EventManager::notify('CheckoutProcessPostProcess', &$order);
			//$order->sendNewOrderEmail();
		}

		return true;
	}

	function getHiddenFields() {
		global $order, $currencies, $userAccount;

		$Reference = new CmcicReference();
		$Reference->order_id = 0;
		$Reference->save();

		$refId = $Reference->ref_id;

		/*
					 * @TODO: Use Doctrine's preInsert function to build this?
					 */
		$Reference->ref_number = str_pad($refId, 12, '0', STR_PAD_LEFT);
		$Reference->save();

		$version = $this->getConfigData('MODULE_PAYMENT_CMCIC_VERSION');
		$societe = $this->getConfigData('MODULE_PAYMENT_CMCIC_SOCIETE');

		$sOptions = "";

		// Reference: unique, alphaNum (A-Z a-z 0-9), 12 characters max
		$sReference = $refId;

		// Amount : format  "xxxxx.yy" (no spaces)
		// $sMontant = 1.01;
		$sMontant = number_format(
			$order->info['total'] * $currencies->get_value($order->info['currency']),
			$currencies->currencies[$order->info['currency']]['decimal_places'],
			'.',
			''
		);

		// Currency : ISO 4217 compliant
		// $sDevise  = "EUR";
		$sDevise = $order->info['currency'];

		// free texte : a bigger reference, session context for the return on the merchant website
		//$sTexteLibre = "Texte Libre";
		$sTexteLibre = Session::getSessionId() . ';' . $order->newOrder['orderID'] . ';' . $userAccount->getCustomerId();

		// transaction date : format d/m/y:h:m:s
		$sDate = date('d/m/Y:H:i:s');

		// Language of the company code
		$sLangue = "EN";

		// customer email
		// $sEmail = "test@test.zz";
		$sEmail = $userAccount->getEmailAddress();

		// ----------------------------------------------------------------------------

		// between 2 and 4
		//$sNbrEch = "4";
		$sNbrEch = "";

		// date echeance 1 - format dd/mm/yyyy
		//$sDateEcheance1 = date("d/m/Y");
		$sDateEcheance1 = "";

		// montant �ch�ance 1 - format  "xxxxx.yy" (no spaces)
		//$sMontantEcheance1 = "0.26" . $sDevise;
		$sMontantEcheance1 = "";

		// date echeance 2 - format dd/mm/yyyy
		//$sDateEcheance2 = date("d/m/Y", mktime(0, 0, 0, date("m") +1 , date("d"), date("Y")));
		$sDateEcheance2 = "";

		// montant �ch�ance 2 - format  "xxxxx.yy" (no spaces)
		//$sMontantEcheance2 = "0.25" . $sDevise;
		$sMontantEcheance2 = "";

		// date echeance 3 - format dd/mm/yyyy
		//$sDateEcheance3 = date("d/m/Y", mktime(0, 0, 0, date("m") +2 , date("d"), date("Y")));
		$sDateEcheance3 = "";

		// montant �ch�ance 3 - format  "xxxxx.yy" (no spaces)
		//$sMontantEcheance3 = "0.25" . $sDevise;
		$sMontantEcheance3 = "";

		// date echeance 4 - format dd/mm/yyyy
		//$sDateEcheance4 = date("d/m/Y", mktime(0, 0, 0, date("m") +3 , date("d"), date("Y")));
		$sDateEcheance4 = "";

		// montant �ch�ance 4 - format  "xxxxx.yy" (no spaces)
		//$sMontantEcheance4 = "0.25" . $sDevise;
		$sMontantEcheance4 = "";

		// ----------------------------------------------------------------------------

		$oTpe = new CMCIC_Tpe($sLangue);
		$oHmac = new CMCIC_Hmac($oTpe);

		// Control String for support
		$CtlHmac = sprintf(
			CMCIC_CTLHMAC,
			$oTpe->sVersion,
			$oTpe->sNumero,
			$oHmac->computeHmac(sprintf(CMCIC_CTLHMACSTR, $oTpe->sVersion, $oTpe->sNumero))
		);

		// Data to certify
		$PHP1_FIELDS = sprintf(
			CMCIC_CGI1_FIELDS,
			$oTpe->sNumero,
			$sDate,
			$sMontant,
			$sDevise,
			$sReference,
			$sTexteLibre,
			$oTpe->sVersion,
			$oTpe->sLangue,
			$oTpe->sCodeSociete,
			$sEmail,
			$sNbrEch,
			$sDateEcheance1,
			$sMontantEcheance1,
			$sDateEcheance2,
			$sMontantEcheance2,
			$sDateEcheance3,
			$sMontantEcheance3,
			$sDateEcheance4,
			$sMontantEcheance4,
			$sOptions
		);

		// MAC computation
		$sMAC = $oHmac->computeHmac($PHP1_FIELDS);

		// ----------------------------------------------------------------------------
		// Your Page displaying payment button to be customized
		// ----------------------------------------------------------------------------
		//	echo "gg". $oTpe->sNumero."hhhh".$oTpe->sCodeSociete."hhhd".HtmlEncode($sMAC);

		$process_button_string = tep_draw_hidden_field('MAC', HtmlEncode($sMAC)) . "\n" .
			tep_draw_hidden_field('version', HtmlEncode($oTpe->sVersion)) . "\n" .
			tep_draw_hidden_field('TPE', HtmlEncode($oTpe->sNumero)) . "\n" .
			tep_draw_hidden_field('date', HtmlEncode($sDate)) . "\n" .
			tep_draw_hidden_field('montant', HtmlEncode($sMontant . $sDevise)) . "\n" .
			tep_draw_hidden_field('reference', HtmlEncode($sReference)) . "\n" .
			tep_draw_hidden_field('lgue', HtmlEncode($oTpe->sLangue)) . "\n" .
			tep_draw_hidden_field('societe', HtmlEncode($oTpe->sCodeSociete)) . "\n" .
			tep_draw_hidden_field('url_retour', itw_app_link(null, 'checkout', 'default', 'SSL')) . "\n" .
			tep_draw_hidden_field('url_retour_ok', itw_app_link('action=sessionClean&order_id=' . $order->newOrder['orderID'], 'account', 'default', 'SSL')) . "\n" .
			tep_draw_hidden_field('url_retour_err', itw_app_link(null, 'checkout', 'default', 'SSL')) . "\n" .
			tep_draw_hidden_field('texte-libre', HtmlEncode($sTexteLibre)) . "\n" .
			tep_draw_hidden_field('mail', $sEmail) . "\n" .
			tep_draw_hidden_field('bouton', 'CB Payment') . "\n";

		return $process_button_string;
	}
}

?>