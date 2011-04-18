<?php
/*
	Google Analytics Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_fedEX extends ExtensionBase {

	public function __construct(){
		parent::__construct('fedEX');
	}
	
	public function init(){
        global $appExtension;
		if ($this->enabled === false) return;

		EventManager::attachEvents(array(
			'OrdersInfoboxButtons'
		), null, $this);

        if ($appExtension->isAdmin()){
			EventManager::attachEvent('OrderInfoAddBlock', null, $this);
		}
	}

    public function OrdersInfoboxButtons(&$oInfo, &$infoBox){
        $Qmanifest = Doctrine_Core::getTable('ShippingManifest')->findOneByOrdersId($oInfo->orders_id);

        if ($Qmanifest){
            $cancelOrderButton = htmlBase::newElement('button')->setText('Cancel Shipping')
				->setHref(itw_app_link('appExt=fedEX&action=cancelShip&oID=' . $oInfo->orders_id,'ship_fedex','default'));
            $infoBox->addButton($cancelOrderButton);
            $trackOrderButton = htmlBase::newElement('button')->setText('Track Order')
				->setHref(itw_app_link('appExt=fedEX&oID=' . $oInfo->orders_id,'ship_track','default'));
            $infoBox->addButton($trackOrderButton);
             if (sysConfig::get('EXTENSION_FED_EX_PRINTING') == 'Thermal'){
                $labelLink = itw_app_link('action=fedex_popup_thermal&oID='.$oInfo->orders_id ,'ship_fedex','default');
            }else{
                $labelLink = itw_app_link('action=fedex_popup_laser&oID='.$oInfo->orders_id ,'ship_fedex','default');
            }
             $labelButton = htmlBase::newElement('button')->setText('Print Label')
				->setHref($labelLink);
             $infoBox->addButton($labelButton);
        }else{
            $shipOrderButton = htmlBase::newElement('button')->setText('Fedex Ship Order')
				->setHref(itw_app_link('appExt=fedEX&oID=' . $oInfo->orders_id,'ship_fedex','default'));
            $infoBox->addButton($shipOrderButton);
        }

    }

    public function OrderInfoAddBlock($orderId){
        $Qmanifest = Doctrine_Core::getTable('ShippingManifest')->findOneByOrdersId($orderId);
        $labelLink = '#';
        $linkShip = itw_app_link('appExt=fedEX&oID='.$orderId,'ship_fedex','default');
        $scText = 'Fedex Ship Order';
        $linkTrack= '#';
        if ($Qmanifest){
            if (sysConfig::get('EXTENSION_FED_EX_PRINTING') == 'Thermal'){
                $labelLink = itw_app_link('action=fedex_popup_thermal&oID='.$orderId ,'ship_fedex','default');
            }else{
                $labelLink = itw_app_link('action=fedex_popup_laser&oID='.$orderId,'ship_fedex','default');
            }
            $linkShip = itw_app_link('appExt=fedEX&action=cancelShip&oID=' . $orderId,'ship_fedex','default');
            $scText = 'Cancel Fedex Shipping';
            $linkTrack = itw_app_link('appExt=fedEX&oID='.$orderId,'ship_track','default');
        }
		return
			'<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' .
				'<table cellpadding="3" cellspacing="0">' .
					'<tr>' .
						'<td><table cellpadding="3" cellspacing="0">' .
							'<tr>
							 <td class="main" valign="top">' . '<a target="_blank" href="'.$linkShip.'">'.$scText.'</a></td>
							</tr>
							<tr>
							 <td class="main" valign="top">' . '<a target="_blank" href="'.$linkTrack.'">Track FEDEX</a></td>
							</tr>
                            <tr>
							 <td class="main" valign="top">' . '<a target="_blank" href="'.$labelLink.'">Print Label</a></td>
							</tr>
						</table></td>' .
					'</tr>' .
				'</table>' .
			'</div>';
	}


}


  function name_case($name){
       $newname = strtoupper($name[0]);
       $break = 0;
       for ($i=1; $i < strlen($name); $i++){
           $subed = substr($name, $i, 1);
           if (((ord($subed) > 64) && (ord($subed) < 123)) ||
               ((ord($subed) > 48) && (ord($subed) < 58))){
               $word_check = substr($name, $i - 2, 2);
               if (!strcasecmp($word_check, 'Mc') || !strcasecmp($word_check, "O'")){
                   $newname .= strtoupper($subed);
               }else if ($break){
                   $newname .= strtoupper($subed);
               }else{
                   $newname .= strtolower($subed);
               }
                 $break=0;
           }else{
               // not a letter - a boundary
               $newname .= $subed;
               $break=1;
           }
       }
       return $newname;
    }

    function abbreviate_country($country) {
        switch ($country) {
          case 'United States':
            $country = 'US';
            break;
                case 'Canada':
            $country = 'CA';
            break;
                    }
                return $country;
    }

    function abbreviate_state($state) {
        switch ($state) {
          case 'Alabama':
            $state = 'AL';
            break;
          case 'Alaska':
            $state = 'AK';
            break;
          case 'American Samoa':
            $state = 'AS';
            break;
          case 'Arizona':
            $state = 'AZ';
            break;
          case 'Arkansas':
            $state = 'AR';
            break;
                case 'California':
            $state = 'CA';
            break;
                case 'Colorado':
            $state = 'CO';
            break;
          case 'Connecticut':
                  $state = 'CT';
                  break;
                case 'Delaware':
                  $state = 'DE';
                  break;
                case 'District of Columbia':
                  $state = 'DC';
                  break;
                case 'Federated States of Micronesia':
                  $state = 'FM';
                  break;
                case 'Florida':
                  $state = 'FL';
                  break;
                case 'Georgia':
                  $state = 'GA';
                  break;
                case 'Guam':
                  $state = 'GU';
                  break;
                case 'Hawaii':
                  $state = 'HI';
                  break;
                case 'Idaho':
                  $state = 'ID';
                  break;
                case 'Illinois':
                  $state = 'IL';
                  break;
                case 'Indiana':
                  $state = 'IN';
                  break;
                case 'Iowa':
                  $state = 'IA';
                  break;
                case 'Kansas':
                  $state = 'KS';
                  break;
                case 'Kentucky':
                  $state = 'KY';
                  break;
                case 'Louisiana':
                  $state = 'LA';
                  break;
                case 'Maine':
                  $state = 'ME';
                  break;
                case 'Marshall Islands':
                  $state = 'MH';
                  break;
                case 'Maryland':
                  $state = 'MD';
                  break;
                case 'Massachusetts':
                  $state = 'MA';
                  break;
                case 'Michigan':
                  $state = 'MI';
                  break;
                case 'Minnesota':
                  $state = 'MN';
                  break;
                case 'Mississippi':
                  $state = 'MS';
                  break;
                case 'Missouri':
                  $state = 'MO';
                  break;
                case 'Montana':
                  $state = 'MT';
                  break;
                case 'Nebraska':
                  $state = 'NE';
                  break;
                case 'Nevada':
                  $state = 'NV';
                  break;
                case 'New Hampshire':
                  $state = 'NH';
                  break;
                case 'New Jersey':
                  $state = 'NJ';
                  break;
                case 'New Mexico':
                  $state = 'NM';
                  break;
                case 'New York':
                  $state = 'NY';
                  break;
                case 'North Carolina':
                  $state = 'NC';
                  break;
                case 'North Dakota':
                  $state = 'ND';
                  break;
                case 'Northern Mariana Islands':
                  $state = 'MP';
                  break;
                case 'Ohio':
                  $state = 'OH';
                  break;
                case 'Oklahoma':
                  $state = 'OK';
                  break;
                case 'Oregon':
                  $state = 'OR';
                  break;
                case 'Palau':
                  $state = 'PW';
                  break;
                case 'Pennsylvania':
                  $state = 'PA';
                  break;
                case 'Puerto Rico':
                  $state = 'PR';
                  break;
                case 'Rhode Island':
                  $state = 'RI';
                  break;
                case 'South Carolina':
                  $state = 'SC';
                  break;
                case 'South Dakota':
                  $state = 'SD';
                  break;
                case 'Tennessee':
                  $state = 'TN';
                  break;
                case 'Texas':
                  $state = 'TX';
                  break;
                case 'Utah':
                  $state = 'UT';
                  break;
                case 'Vermont':
                  $state = 'VT';
                  break;
                case 'Virgin Islands':
                  $state = 'VI';
                  break;
                case 'Virginia':
                  $state = 'VA';
                  break;
                case 'Washington':
                  $state = 'WA';
                  break;
                case 'West Virginia':
                  $state = 'WV';
                  break;
                case 'Wisconsin':
                  $state = 'WI';
                  break;
                case 'Wyoming':
                  $state = 'WY';
                  break;
                }
        return $state;
    }

    function tep_ship_request($shipData,$ship_type,$order) {
        global $messageStack;
        $fed = new FedExDC($shipData[10],$shipData[498]);
        //echo 'dsds'. $ship_type;
        $ship_Ret = $fed->$ship_type($shipData);
        if ($error = $fed->getError()) {
            // in case the ship date is a holiday, check for the error and correct the date
            // todo: correct the date!
            if (preg_match('/FF43/',$error)) {
                $messageStack->addSession('pageStack','You cannot schedule a pickup on a weekend or holiday! Please go back and change the pickup date.','error');
                tep_redirect(itw_app_link(null,'orders','default'));                
            }else{
                $messageStack->addSession('pageStack', 'This transaction could not be completed. Please note the error message below.<br/>'. $error,'error');
                tep_redirect(itw_app_link(null,'orders','default'));
            }
        }else{
            $trackNum = $ship_Ret[29];
            // decode and save label, named for the tracking number            
            $fed->label(sysConfig::getDirFsCatalog().'extensions/fedEX/images/'. $trackNum . '.png');				
        }
        return $trackNum;
    }



?>