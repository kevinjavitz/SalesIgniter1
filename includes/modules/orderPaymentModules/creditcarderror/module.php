<?php
class OrderPaymentCreditcarderror extends StandardPaymentModule {
	var $code, $title, $description, $enabled;

	// class constructor
	public function __construct(){
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Credit Class Error');
		$this->setDescription('Credit Class Error');
		
		$this->init('creditcarderror');
	}

	function getError() {
		return array(
			'title' => 'Error',
			'error' => stripslashes(urldecode($_GET['error']))
		);
	}
}
?>
