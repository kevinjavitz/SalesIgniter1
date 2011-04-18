<?php
define("CMCIC_CTLHMAC","V1.04.sha1.php--[CtlHmac%s%s]-%s");
define("CMCIC_CTLHMACSTR", "CtlHmac%s%s");
define("CMCIC_CGI2_RECEIPT","version=2\ncdr=%s");
define("CMCIC_CGI2_MACOK","0");
define("CMCIC_CGI2_MACNOTOK","1\n");
define("CMCIC_CGI2_FIELDS", "%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*");
define("CMCIC_CGI1_FIELDS", "%s*%s*%s%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s");


/*****************************************************************************
*
* Classe / Class : CMCIC_Tpe
*
*****************************************************************************/

class CMCIC_Tpe {

	var $sVersion;	// Version du TPE - TPE Version (Ex : 3.0)
	var $sNumero;	// Numero du TPE - TPE Number (Ex : 1234567)
	var $sCodeSociete;	// Code Societe - Company code (Ex : companyname)
	var $sLangue;	// Langue - Language (Ex : FR, DE, EN, ..)
	var $_sCle;		// La clé - The Key


	// ----------------------------------------------------------------------------
	//
	// Constructeur / Constructor
	//
	// ----------------------------------------------------------------------------

	function CMCIC_Tpe($sLangue = "EN") {

		$chemincle = "cmcic/".OrderPaymentModules::getModule('cmcic')->getConfigData('MODULE_PAYMENT_CMCIC_TPE').".key";
   		if (file_exists($chemincle)) {
			$data_cle = explode(' ',nl2br(file_get_contents($chemincle)));
			define ("CMCIC_CLE", substr($data_cle[2], 0, -3));
   		}


		// contrôle de l'existence des constantes de paramétrages.
		$aRequiredConstants = array('CMCIC_CLE', 'MODULE_PAYMENT_CMCIC_VERSION', 'MODULE_PAYMENT_CMCIC_TPE', 'MODULE_PAYMENT_CMCIC_SOCIETE');

		//$this->_checkTpeParams($aRequiredConstants);
		$this->sVersion = OrderPaymentModules::getModule('cmcic')->getConfigData('MODULE_PAYMENT_CMCIC_VERSION');
		$this->_sCle = CMCIC_CLE;
		$this->sNumero = OrderPaymentModules::getModule('cmcic')->getConfigData('MODULE_PAYMENT_CMCIC_TPE');
		$this->sCodeSociete = OrderPaymentModules::getModule('cmcic')->getConfigData('MODULE_PAYMENT_CMCIC_SOCIETE');
		$this->sLangue = $sLangue;
	}

	// ----------------------------------------------------------------------------
	//
	// Fonction / Function : getCle
	//
	// Renvoie la clé du TPE / return the TPE Key
	//
	// ----------------------------------------------------------------------------

	function getCle() {

		return $this->_sCle;
	}

	// ----------------------------------------------------------------------------
	//
	// Fonction / Function : _checkTpeParams
	//
	// Contrôle l'existence des constantes d'initialisation du TPE
	// Check for the initialising constants of the TPE
	//
	// ----------------------------------------------------------------------------

	function _checkTpeParams($aConstants) {

		for ($i = 0; $i < count($aConstants); $i++)
			if (!defined($aConstants[$i]))
				die ("Erreur paramètre " . $aConstants[$i] . " indéfini");
	}

}
?>