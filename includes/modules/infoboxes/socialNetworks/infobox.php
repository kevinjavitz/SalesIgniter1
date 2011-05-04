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
		$facebook = isset($boxWidgetProperties->facebook)?$boxWidgetProperties->facebook:'';
		$twitter = isset($boxWidgetProperties->twitter)?$boxWidgetProperties->twitter:'';
		$email = isset($boxWidgetProperties->email)?$boxWidgetProperties->email:'';
		$htmlText = htmlBase::newElement('div')
		->addClass('socialNetworks')
		->html('<a target="_blank" href="'.$facebook.'"><img src="'. sysConfig::getDirWsCatalog() .'images/facebookSocial.png" /></a>
			<a href="'.$twitter.'" target="_blank"><img src="'. sysConfig::getDirWsCatalog() .'images/twitterSocial.png" /></a>
			<a href="'.$email.'" target="_blank"><img src="'. sysConfig::getDirWsCatalog() .'images/emailSocial.png" /></a>'
		);

		$this->setBoxContent($htmlText->draw());
		return $this->draw();
	}
}
?>