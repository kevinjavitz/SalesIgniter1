<?php
	$request_method = $_SERVER["REQUEST_METHOD"];
	$wRequestVars = "_" . $request_method;
	$CMCIC_bruteVars = ${$wRequestVars};
 	if (isset($CMCIC_bruteVars['texte-libre'])){
		$textLibre = $CMCIC_bruteVars['texte-libre'];
		$textArr = explode(';', $textLibre);
		$_GET['osCID'] = $textArr[0];
		$_REQUEST['osCID'] = $textArr[0];
		$_POST['osCID'] = $textArr[0];
		$theOrderID = $textArr[1];
		$theCustomerID = $textArr[2];
	 }

	//chdir('../../../../../ses_alpha/');

	include('includes/application_top.php');

	include('ext/modules/payment/cmcic/cmcic_response.php');

	include('includes/application_bottom.php');
?>