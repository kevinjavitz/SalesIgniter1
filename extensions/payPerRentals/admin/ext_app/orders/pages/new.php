<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of new
 *
 * @author Stephen
 */
class payPerRentals_admin_orders_new extends Extension_payPerRentals {

	public function __construct(){
		parent::__construct();
	}

	public function load(){
		global $App;
		if ($this->enabled === false) return;

		$App->addJavascriptFile('ext/jQuery/external/datepick/jquery.datepick.js');
		$App->addStylesheetFile('ext/jQuery/external/datepick/css/jquery.datepick.css');
	}
}
?>