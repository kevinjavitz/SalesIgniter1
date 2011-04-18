<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxSocialNetworks extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('socialNetworks');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_SOCIAL_NETWORKS'));
	}

	public function show(){


			$htmlText = htmlBase::newElement('p')
						->html('Follow Ironman Wheel Rentals on Twitter and Facebook, click the icons below<br/><br/><a target="_blank" href="http://www.facebook.com/?ref=home#!/pages/Boulder-CO/Ironman-Wheel-Rentals/117693644956058?ref=ts"><img style="margin-right:8px;margin-left:15px;"  src="'. sysConfig::getDirWsCatalog() .'images/facebook.png" /></a><a href="http://twitter.com/ironmanwheels" target="_blank"><img src="'. sysConfig::getDirWsCatalog() .'images/twitter.png" /></a>');


			$this->setBoxContent($htmlText->draw());

			return $this->draw();

			return false;
	}
}
?>