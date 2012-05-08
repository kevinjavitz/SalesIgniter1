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
		global $appExtension;
		$boxWidgetProperties = $this->getWidgetProperties();
		$multiStore = $appExtension->getExtension('multiStore');
		if ($multiStore !== false){
			if(Session::exists('current_store_id') == true){
				$fbVar = 'facebook'.Session::get('current_store_id');
				$gpVar = 'googlePlus'.Session::get('current_store_id');
				$ttVar = 'twitter'.Session::get('current_store_id');
				$liVar = 'linked'.Session::get('current_store_id');
				$btVar = 'beforeText'.Session::get('current_store_id');
				$emVar = 'email'.Session::get('current_store_id');

				if(isset($boxWidgetProperties->$fbVar) && $boxWidgetProperties->$fbVar != ''){
					$fbLink = '<a target="_blank" href="'.$boxWidgetProperties->$fbVar.'"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') . '/images/facebookSocial.png" /></a>';
				}else{
					if(isset($boxWidgetProperties->facebook) && $boxWidgetProperties->facebook != ''){
						$fbLink = '<a target="_blank" href="'.$boxWidgetProperties->facebook.'"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') . '/images/facebookSocial.png" /></a>';
					}
				}

				if(isset($boxWidgetProperties->$gpVar) && $boxWidgetProperties->$gpVar != ''){
					$gpLink = '<a target="_blank" href="'.$boxWidgetProperties->$gpVar.'"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') . '/images/googleSocial.png" /></a>';
				}else{
					if(isset($boxWidgetProperties->googlePlus) && $boxWidgetProperties->googlePlus != ''){
						$gpLink = '<a target="_blank" href="'.$boxWidgetProperties->googlePlus.'"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') . '/images/googleSocial.png" /></a>';
					}
				}

				if(isset($boxWidgetProperties->$ttVar) && $boxWidgetProperties->$ttVar != ''){
					$ttLink = '<a target="_blank" href="'.$boxWidgetProperties->$gpVar.'"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') . '/images/twitterSocial.png" /></a>';
				}else{
					if(isset($boxWidgetProperties->twitter) && $boxWidgetProperties->twitter != ''){
						$ttLink = '<a target="_blank" href="'.$boxWidgetProperties->twitter.'"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') . '/images/twitterSocial.png" /></a>';
					}
				}

				if(isset($boxWidgetProperties->$liVar) && $boxWidgetProperties->$liVar != ''){
					$liLink = '<a target="_blank" href="'.$boxWidgetProperties->$liVar.'"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') . '/images/linkedinSocial.png" /></a>';
				}else{
					if(isset($boxWidgetProperties->linked) && $boxWidgetProperties->linked != ''){
						$liLink = '<a target="_blank" href="'.$boxWidgetProperties->linked.'"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') . '/images/linkedinSocial.png" /></a>';
					}
				}

				if(isset($boxWidgetProperties->$emVar) && $boxWidgetProperties->$emVar != ''){
					$emLink = '<a target="_blank" href="'.$boxWidgetProperties->$emVar.'"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') . '/images/emailSocial.png" /></a>';
				}else{
					if(isset($boxWidgetProperties->email) && $boxWidgetProperties->email != ''){
						$emLink = '<a target="_blank" href="'.$boxWidgetProperties->email.'"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') . '/images/emailSocial.png" /></a>';
					}
				}

				if(isset($boxWidgetProperties->$btVar) && $boxWidgetProperties->$btVar != ''){
					$btLink = $boxWidgetProperties->$btVar;
				}else{
					if(isset($boxWidgetProperties->beforeText) && $boxWidgetProperties->beforeText != ''){
						$btLink = $boxWidgetProperties->beforeText;
					}
				}

				$facebook = (isset($fbLink) && $fbLink != '')?$fbLink:'';
				$googlePlus = (isset($gpLink)&& $gpLink!= '') ?$gpLink:'';
				$twitter = (isset($ttLink)&& $ttLink!= '')?$ttLink:'';
				$linked = (isset($liLink)&& $liLink!= '') ?$liLink:'';
				$email = (isset($emLink)&& $emLink!= '') ?$emLink:'';
				$beforeText = (isset($btLink)&& $btLink!= '') ?$btLink:'';

				$htmlText = htmlBase::newElement('div')
				->addClass('socialNetwork')
				->html($beforeText . $facebook .$linked. $twitter . $googlePlus.$email);
			}
		}else{
			$facebook = (isset($boxWidgetProperties->facebook) && $boxWidgetProperties->facebook!= '')?'<a target="_blank" href="'.$boxWidgetProperties->facebook.'"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') . '/images/facebookSocial.png" /></a>'. $boxWidgetProperties->facebookText:'';
			$googlePlus = (isset($boxWidgetProperties->googlePlus) && $boxWidgetProperties->googlePlus!= '')?'<a target="_blank" href="'.$boxWidgetProperties->googlePlus.'"><img src="'. sysConfig::getDirWsCatalog() .'templates/' . Session::get('tplDir') . '/images/googleSocial.png" /></a>'. $boxWidgetProperties->googleText:'';
			$twitter = (isset($boxWidgetProperties->twitter) && $boxWidgetProperties->twitter != '')?'<a href="'.$boxWidgetProperties->twitter.'" target="_blank"><img src="'. sysConfig::getDirWsCatalog().'templates/' . Session::get('tplDir') . '/images/twitterSocial.png" /></a>'. $boxWidgetProperties->twitterText:'';
			$linked = (isset($boxWidgetProperties->linked) && $boxWidgetProperties->linked != '')?'<a href="'.$boxWidgetProperties->linked.'" target="_blank"><img src="'. sysConfig::getDirWsCatalog() .'templates/'.Session::get('tplDir').'/images/linkedinSocial.png" /></a>'. $boxWidgetProperties->linkedText:'';
			$email = (isset($boxWidgetProperties->email) && $boxWidgetProperties->email != '')?'<a href="'.$boxWidgetProperties->email.'" target="_blank"><img src="'. sysConfig::getDirWsCatalog().'templates/'.Session::get('tplDir') .'/images/emailSocial.png" /></a>'. $boxWidgetProperties->emailText:'';
			$beforeText = (isset($boxWidgetProperties->beforeText) && $boxWidgetProperties->beforeText != '')?$boxWidgetProperties->beforeText:'';

			$htmlText = htmlBase::newElement('div')
				->addClass('socialNetwork')
				->html($beforeText . $facebook .$linked. $twitter . $googlePlus.$email);
		}

		$this->setBoxContent($htmlText->draw());
		return $this->draw();
	}
}
?>