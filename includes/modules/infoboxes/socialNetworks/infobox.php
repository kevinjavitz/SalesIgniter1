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

		$boxWidgetProperties = $this->getWidgetProperties();
		$facebook = (isset($boxWidgetProperties->facebook) && $boxWidgetProperties->facebook!= '')?'<a target="_blank" href="'.$boxWidgetProperties->facebook.'"><img src="'. sysConfig::getDirWsCatalog() .'images/facebookSocial.png" /></a>':'';
		$twitter = (isset($boxWidgetProperties->twitter) && $boxWidgetProperties->twitter != '')?'<a href="'.$boxWidgetProperties->twitter.'" target="_blank"><img src="'. sysConfig::getDirWsCatalog() .'images/twitterSocial.png" /></a>':'';
		$email = (isset($boxWidgetProperties->email) && $boxWidgetProperties->email != '')?'<a href="'.$boxWidgetProperties->email.'" target="_blank"><img src="'. sysConfig::getDirWsCatalog() .'images/emailSocial.png" /></a>':'';

		$htmlText = htmlBase::newElement('div')
		->addClass('socialNetworks')
		->html(sysLanguage::get('INFOBOX_SOCIAL_NETWORKS_TEXT') . $facebook .$twitter .$email);

		$this->setBoxContent($htmlText->draw());
		return $this->draw();
	}
}
?>