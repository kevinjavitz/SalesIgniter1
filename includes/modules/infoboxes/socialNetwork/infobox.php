<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxSocialNetwork extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('socialNetwork');
	}

	public function show(){

		$boxWidgetProperties = $this->getWidgetProperties();
		$facebook = (isset($boxWidgetProperties->facebook) && $boxWidgetProperties->facebook!= '')?'<a target="_blank" href="'.$boxWidgetProperties->facebook.'"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') . '/images/facebookSocial.png" /></a>'. $boxWidgetProperties->facebookText:'';
		$twitter = (isset($boxWidgetProperties->twitter) && $boxWidgetProperties->twitter != '')?'<a href="'.$boxWidgetProperties->twitter.'" target="_blank"><img src="'. sysConfig::getDirWsCatalog().'templates/' . Session::get('tplDir') . '/images/twitterSocial.png" /></a>'. $boxWidgetProperties->twitterText:'';
		$linked = (isset($boxWidgetProperties->linked) && $boxWidgetProperties->linked != '')?'<a href="'.$boxWidgetProperties->twitter.'" target="_blank"><img src="'. sysConfig::getDirWsCatalog() .'templates/'.Session::get('tplDir').'/images/linkedinSocial.png" /></a>'. $boxWidgetProperties->linkedText:'';
		$email = (isset($boxWidgetProperties->email) && $boxWidgetProperties->email != '')?'<a href="'.$boxWidgetProperties->email.'" target="_blank"><img src="'. sysConfig::getDirWsCatalog().'templates/'.Session::get('tplDir') .'/images/emailSocial.png" /></a>'. $boxWidgetProperties->emailText:'';
		$beforeText = (isset($boxWidgetProperties->beforeText) && $boxWidgetProperties->beforeText != '')?$boxWidgetProperties->beforeText:'';

		$htmlText = htmlBase::newElement('div')
		->addClass('socialNetwork')
		->html($beforeText . $facebook .$linked. $twitter .$email);

		$this->setBoxContent($htmlText->draw());
		return $this->draw();
	}
}
?>