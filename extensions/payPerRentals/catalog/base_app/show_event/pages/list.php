<?php
	$events = Doctrine_Query::create()
	->from('PayPerRentalEvents')
	->where('DATE_ADD(events_date,INTERVAL events_days DAY) > ?', date("Y-m-d h:i:s"))
	->orderBy('events_date')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$contentHtml = '';
	foreach($events as $evInfo){
		$contentHtml .= "<div class='list_ev' style='margin-bottom:20px;'><b>Event Name:</b> ".$evInfo['events_name']."<br/>";
		$contentHtml .= '<b>Date:</b> '. (($evInfo['events_days'] <= 0)?strftime(sysLanguage::getDateFormat('short'), strtotime($evInfo['events_date'])):strftime(sysLanguage::getDateFormat('short'), strtotime($evInfo['events_date'])).' - '.strftime(sysLanguage::getDateFormat('short'), strtotime('+'.$evInfo['events_days'].' day', strtotime($evInfo['events_date']))))."<br/>";
		$contentHtml .= "<a class='moreinfo' href='".itw_app_link('appExt=payPerRentals&ev_id='.$evInfo['events_id'],'show_event','default')."'><b>More info</b></a>"."</div>";
	}


	$pageTitle = 'List of Events';
	$pageContents = stripslashes($contentHtml);

	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
?>