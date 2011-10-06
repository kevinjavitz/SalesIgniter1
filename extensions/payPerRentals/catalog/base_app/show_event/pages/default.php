<?php
	$evInfo = ReservationUtilities::getEvent((isset($_GET['ev_id']) ? (int)$_GET['ev_id'] : $_GET['ev_name']));
	$myDate = date('M d',strtotime($evInfo['events_date'])).' - '.date('M d Y', strtotime('+'.$evInfo['events_days'].' DAY', strtotime($evInfo['events_date'])));
    if(isset($_GET['isgate'])){
	    $contentHtml = 'Name: '. $evInfo['events_name']."<br/><br/>Date of Event: ".$myDate."<br/><br/>Description: ".$evInfo['gates_details'];
    }else{
		$contentHtml = 'Name: '. $evInfo['events_name']."<br/><br/>Date of Event: ".$myDate."<br/><br/>Description: ".$evInfo['events_details'];
    }

	$pageTitle = stripslashes($evInfo['events_name']);
	$pageContents = stripslashes($contentHtml);
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
