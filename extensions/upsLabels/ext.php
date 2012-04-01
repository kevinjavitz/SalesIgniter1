<?php
/*
	Google Analytics Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_upsLabels extends ExtensionBase {

	public function __construct(){
		parent::__construct('upsLabels');
	}
	
	public function init(){
        global $appExtension;
		if ($this->isEnabled() === false) return;

		EventManager::attachEvents(array(
			'OrdersInfoboxButtons'
		), null, $this);

        if ($appExtension->isAdmin()){
			EventManager::attachEvent('OrderInfoAddBlock', null, $this);
		}
	}

    public function OrdersInfoboxButtons(&$oInfo, &$infoBox){

    }

    public function OrderInfoAddBlock($orderId){

       $labelLink = itw_app_link('appExt=upsLabels&oID='.$orderId,'ship_ups','default');
       $labelLinkGenerate = itw_app_link('appExt=upsLabels&action=shipOrdersAuto&oID=' . $orderId, 'ship_ups', 'default');

		return
			'<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' .
				'<table cellpadding="3" cellspacing="0">' .
					'<tr>' .
						'<td><table cellpadding="3" cellspacing="0">' .
                            '<tr>
							 <td class="main" valign="top">' . '<a target="_blank" href="'.$labelLinkGenerate.'">Generate UPS Label</a></td>
							</tr>
                            <tr>
							 <td class="main" valign="top">' . '<a target="_blank" href="'.$labelLink.'">Print UPS Label</a></td>
							</tr>
						</table></td>' .
					'</tr>' .
				'</table>' .
			'</div>';
	}


}

function tep_cfg_get_packaging_type($pkgName){
	$retVal = '';
	switch($pkgName){
		case '01': $retVal = 'UPS Letter';
						break;
		case '02': $retVal = 'Customer Supplied Package';
		                   break;
		case '03': $retVal = 'Tube';
		                   break;
		case '04': $retVal = 'PAK';
		                   break;
		case '21': $retVal = 'UPS Express Box';
		                   break;
		case '24': $retVal = 'UPS 25KG Box';
		                   break;
		case '25': $retVal = 'UPS 10KG Box';
		                   break;
		case '30': $retVal = 'Pallet';
		                   break;
		case '2a': $retVal = 'Small Express Box';
		                   break;
		case '2b': $retVal = 'Medium Express Box';
		                   break;
		case '2c': $retVal = 'Large Express Box';
		                   break;



	}
	return $retVal;
}

function tep_cfg_pull_down_packaging_types($pkgName, $key = '') {
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
	$switcher = htmlBase::newElement('selectbox')
		->setName($name)
		->selectOptionByValue($pkgName);

	$switcher->addOption('01','UPS Letter');
	$switcher->addOption('02','Customer Supplied Package');
	$switcher->addOption('03','Tube');
	$switcher->addOption('04','PAK');
	$switcher->addOption('21','UPS Express Box');
	$switcher->addOption('24','UPS 25KG Box');
	$switcher->addOption('25','UPS 10KG Box');
	$switcher->addOption('30','Pallet');
	$switcher->addOption('2a','Small Express Box');
	$switcher->addOption('2b','Medium Express Box');
	$switcher->addOption('2c','Large Express Box');


	return $switcher->draw();
}


function tep_cfg_get_service_type($sName){
	$retVal = '';
	switch($sName){
		case '01': $retVal = 'UPS Next Day Air';
		           break;
		case '02': $retVal = 'UPS Second Day Air';
		           break;
		case '03': $retVal = 'UPS Ground';
		             break;
		case '12': $retVal = 'UPS Three-Day Select';
		            break;
		case '13': $retVal = 'UPS Next Day Air Saver';
		                        break;
		case '14': $retVal = 'UPS Next Day Air Early A.M. SM';
		                     break;
		case '59': $retVal = 'UPS Second Day Air A.M.';
		                     break;
		case '65': $retVal = 'UPS Saver';
		               break;


	}
	return $retVal;
}

function tep_cfg_pull_down_service_types($sName, $key = '') {
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
	$switcher = htmlBase::newElement('selectbox')
		->setName($name)
		->selectOptionByValue($sName);

	$switcher->addOption('01','UPS Next Day Air');
	$switcher->addOption('02','UPS Second Day Air');
	$switcher->addOption('03','UPS Ground');
	$switcher->addOption('12','UPS Three-Day Select');
	$switcher->addOption('13','UPS Next Day Air Saver');
	$switcher->addOption('14','UPS Next Day Air Early A.M. SM');
	$switcher->addOption('59','UPS Second Day Air A.M.');
	$switcher->addOption('65','UPS Saver');

	return $switcher->draw();
}

?>