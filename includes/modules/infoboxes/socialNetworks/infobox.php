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
		$facebook = (isset($boxWidgetProperties->facebook) && $boxWidgetProperties->facebook!= '')?'<a target="_blank" href="'.$boxWidgetProperties->facebook.'"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') .'/images/facebookSocial.png" /></a>'. sysLanguage::get('INFOBOX_SOCIAL_NETWORKS_FACEBOOK_TEXT'):'';
		$twitter = (isset($boxWidgetProperties->twitter) && $boxWidgetProperties->twitter != '')?'<a href="'.$boxWidgetProperties->twitter.'" target="_blank"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') .'/images/twitterSocial.png" /></a>'. sysLanguage::get('INFOBOX_SOCIAL_NETWORKS_TWITTER_TEXT'):'';
		$youtube = (isset($boxWidgetProperties->youtube) && $boxWidgetProperties->youtube != '')?'<a href="'.$boxWidgetProperties->youtube.'" target="_blank"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') .'/images/youtubeSocial.png" /></a>'. sysLanguage::get('INFOBOX_SOCIAL_NETWORKS_YOUTUBE_TEXT'):'';
		$email = (isset($boxWidgetProperties->email) && $boxWidgetProperties->email != '')?'<a href="'.$boxWidgetProperties->email.'" target="_blank"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') .'/images/emailSocial.png" /></a>'. sysLanguage::get('INFOBOX_SOCIAL_NETWORKS_EMAIL_TEXT'):'';

		$htmlText = htmlBase::newElement('div')
		->addClass('socialNetworks')
		->html(sysLanguage::get('INFOBOX_SOCIAL_NETWORKS_TEXT') . $facebook .$twitter .$youtube .$email);

		$this->setBoxContent($htmlText->draw());
		return $this->draw();
	}
}
?>