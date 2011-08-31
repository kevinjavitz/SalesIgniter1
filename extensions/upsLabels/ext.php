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
		if ($this->enabled === false) return;

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

		return
			'<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' .
				'<table cellpadding="3" cellspacing="0">' .
					'<tr>' .
						'<td><table cellpadding="3" cellspacing="0">' .
                            '<tr>
							 <td class="main" valign="top">' . '<a target="_blank" href="'.$labelLink.'">Print UPS Label</a></td>
							</tr>
						</table></td>' .
					'</tr>' .
				'</table>' .
			'</div>';
	}


}

?>